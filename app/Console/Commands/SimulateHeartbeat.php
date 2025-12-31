<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Services\DeviceService;
use Illuminate\Console\Command;

class SimulateHeartbeat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'device:simulate-heartbeat
        {serial_number : Serial number used by the virtual device}
        {--name= : Optional name to assign when creating the device}
        {--interval=30 : Seconds to wait between pings (0 = no wait)}
        {--count=0 : Total number of pings to send (0 = infinite)}
        {--disconnect : Disconnect when finished}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate a device that connects and keeps sending heartbeat (ping) calls';

    /**
     * Execute the console command.
     */
    public function handle(DeviceService $deviceService): int
    {
        $serial = (string) $this->argument('serial_number');
        $name = $this->option('name') ?: 'Simulated Device';
        $interval = max(0, (int) $this->option('interval'));
        $count = max(0, (int) $this->option('count'));
        $disconnect = (bool) $this->option('disconnect');

        $device = Device::firstOrCreate(
            ['serial_number' => $serial],
            [
                'name' => $name,
                'status' => 'offline',
            ]
        );

        $this->info("Simulating heartbeat for device [{$device->name}] ({$serial})");

        $deviceService->connect($serial);
        $this->line('Connected');

        $pingNumber = 0;

        while ($count === 0 || $pingNumber < $count) {
            $pingNumber++;
            $deviceService->ping($serial);
            $this->line("Ping #{$pingNumber} sent");

            if ($count !== 0 && $pingNumber >= $count) {
                break;
            }

            if ($interval > 0) {
                sleep($interval);
            }
        }

        if ($disconnect) {
            $deviceService->disconnect($serial);
            $this->line('Disconnected');
        }

        $this->info('Simulation complete');

        return 0;
    }
}
