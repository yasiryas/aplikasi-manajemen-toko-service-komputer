<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = Customer::query()
            ->withCount('devices')
            ->when($request->filled('q'), fn ($query) => $query
                ->where('nama', 'like', "%{$request->string('q')}%")
                ->orWhere('no_hp', 'like', "%{$request->string('q')}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customers.index', [
            'customers' => $customers,
            'search' => $request->string('q')->toString(),
        ]);
    }

    public function show(Customer $customer): View
    {
        $customer->loadCount('devices');
        $customer->load(['devices' => fn ($query) => $query->withCount('serviceOrders'), 'devices.serviceOrders' => fn ($query) => $query->latest(), 'devices.serviceOrders.invoice']);

        return view('customers.show', [
            'customer' => $customer,
        ]);
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = Customer::create($request->validated());

        return response()->json(['customer' => $customer]);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        $customer->update($request->validated());

        return response()->json(['customer' => $customer]);
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $customer->delete();

        return response()->json(['message' => 'Pelanggan dihapus.']);
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->string('q');

        $customers = Customer::query()
            ->when($query->isNotEmpty(), fn ($builder) => $builder
                ->where('nama', 'like', "%{$query}%")
                ->orWhere('no_hp', 'like', "%{$query}%"))
            ->with('devices')
            ->limit(8)
            ->get();

        return response()->json(['customers' => $customers]);
    }
}
