<?php

namespace App\Http\Controllers;

use App\Events\DeviceStatusUpdated;
use App\Models\Device;
use App\Services\DeviceService;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    protected DeviceService $deviceService;

    public function __construct(DeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }

    public function index(){
        $devices = Device::orderBy('name')->get();

        return view('devices.index', [
            'devices' => $devices
        ]);
    }

    public function connect(Request $request){
        $validated = $request->validate([
            'serial_number' => ['required', 'string'],
        ]);

        $this->deviceService->connect($validated['serial_number']);

        return response()->json(['status' => 'connected']);
    }

    public function ping(Request $request){
        $validated = $request->validate([
            'serial_number' => ['required', 'string'],
        ]);

        $this->deviceService->ping($validated['serial_number']);

        return response()->json(['status' => 'pong']);
    }

    public function disconnect(Request $request){
        $validated = $request->validate([
            'serial_number' => ['required', 'string'],
        ]);

        $this->deviceService->disconnect($validated['serial_number']);

        return response()->json(['status' => 'disconnected']);
    }
}
