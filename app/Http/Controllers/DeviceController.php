<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeviceController extends Controller
{
    public function index(Request $request): View
    {
        $devices = Device::query()
            ->with('customer')
            ->when($request->filled('q'), fn ($query) => $query
                ->where('merk', 'like', "%{$request->string('q')}%")
                ->orWhere('model', 'like', "%{$request->string('q')}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('devices.index', [
            'devices' => $devices,
            'search' => $request->string('q')->toString(),
        ]);
    }

    public function store(StoreDeviceRequest $request): JsonResponse
    {
        $device = Device::create($request->validated());

        return response()->json(['device' => $device->load('customer')]);
    }

    public function update(UpdateDeviceRequest $request, Device $device): JsonResponse
    {
        $device->update($request->validated());

        return response()->json(['device' => $device->load('customer')]);
    }

    public function destroy(Device $device): JsonResponse
    {
        $device->delete();

        return response()->json(['message' => 'Perangkat dihapus.']);
    }
}
