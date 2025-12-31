<?php

namespace App\Services;

use App\Events\DeviceStatusUpdated;
use App\Models\Device;
use App\Repositories\DeviceRepository;
use App\Jobs\MarkDeviceOffline;
use Illuminate\Support\Facades\Log;

class DeviceService
{
    protected DeviceRepository $deviceRepository;

    public function __construct(DeviceRepository $deviceRepository)
    {
        $this->deviceRepository = $deviceRepository;
    }

    public function connect(string $serialNumber)
    {
        $device = $this->deviceRepository->findBySerialNumber($serialNumber);
        $device = $this->deviceRepository->markAsOnline($device);

        Log::info("Device Connected",[
            'device_id' => $device->id,
            'serial_number' => $device->serial_number
        ]);

        $this->broadcastStatus($device);
        $this->scheduleOfflineCheck($device);
    }

    public function ping(string $serialNumber)
    {
        $device = $this->deviceRepository->findBySerialNumber($serialNumber);
        $device = $this->deviceRepository->markAsOnline($device);

        Log::info("Device Ping",[
            'device_id' => $device->id,
            'serial_number' => $device->serial_number
        ]);

        $this->broadcastStatus($device);
        $this->scheduleOfflineCheck($device);
    }

    public function disconnect(string $serialNumber)
    {
        $device = $this->deviceRepository->findBySerialNumber($serialNumber);
        $device = $this->deviceRepository->markAsOffline($device);

        Log::info("Device Disconnected", [
            'device_id' => $device->id,
            'serial_number' => $device->serial_number
        ]);

        $this->broadcastStatus($device);
    }

    protected function broadcastStatus(Device $device): void
    {
        broadcast(new DeviceStatusUpdated(
            deviceId: $device->id,
            status: $device->status,
            lastSeenAt: optional($device->last_seen_at)?->toISOString()
        ));
    }

    protected function scheduleOfflineCheck(Device $device): void
    {
        if (! $device->last_seen_at) {
            return;
        }

        $timeoutMinutes = (int) config('devices.timeout_minutes', 2);

        MarkDeviceOffline::dispatch(
            deviceId: $device->id,
            expectedLastSeen: $device->last_seen_at->toISOString()
        )->delay(now()->addMinutes($timeoutMinutes));
    }
}
