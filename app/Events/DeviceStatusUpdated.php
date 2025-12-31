<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeviceStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public int $deviceId,
        public string $status,
        public string|null $lastSeenAt
    )
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('devices.status'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'devices.status.updated';
    }

    public function broadcastWith()
    {
        return [
            'device_id' => $this->deviceId,
            'status' => $this->status,
            'last_seen_at' => $this->lastSeenAt,
        ];
    }
}
