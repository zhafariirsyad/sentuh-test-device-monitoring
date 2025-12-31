<?php

namespace App\Jobs;

use App\Events\DeviceStatusUpdated;
use App\Models\Device;
use App\Repositories\DeviceRepository;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MarkDeviceOffline implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $deviceId,
        public string $expectedLastSeen
    ) {
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     */
    public function handle(DeviceRepository $repository): void
    {
        $device = $repository->findById($this->deviceId);

        if ($device->status !== 'online') {
            return;
        }

        if (! $device->last_seen_at) {
            return;
        }

        $expected = Carbon::parse($this->expectedLastSeen);

        // If a newer ping happened after this job was scheduled, bail out.
        if ($device->last_seen_at->gt($expected)) {
            return;
        }

        $device = $repository->markAsOffline($device, $expected);

        Log::warning('Device offline due to timeout', [
            'device_id' => $device->id,
            'serial_number' => $device->serial_number,
            'last_seen_at' => $device->last_seen_at,
        ]);

        broadcast(new DeviceStatusUpdated(
            deviceId: $device->id,
            status: $device->status,
            lastSeenAt: optional($device->last_seen_at)?->toISOString()
        ));
    }
}
