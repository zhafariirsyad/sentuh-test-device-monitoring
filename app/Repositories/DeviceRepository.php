<?php

namespace App\Repositories;

use App\Models\Device;
use Carbon\Carbon;

class DeviceRepository
{
    public function findBySerialNumber(string $serialNumber): Device|null
    {
        return Device::where('serial_number', $serialNumber)->firstOrFail();
    }

    public function findById(int $id): Device
    {
        return Device::findOrFail($id);
    }

    public function markAsOnline(Device $device): Device
    {
        $device->forceFill([
            'status' => 'online',
            'last_seen_at' => Carbon::now(),
        ])->save();

        return $device->refresh();
    }

    public function markAsOffline(Device $device, ?Carbon $lastSeenAt = null): Device
    {
        $device->forceFill([
            'status' => 'offline',
            'last_seen_at' => $lastSeenAt ?? Carbon::now(),
        ])->save();

        return $device->refresh();
    }

}
