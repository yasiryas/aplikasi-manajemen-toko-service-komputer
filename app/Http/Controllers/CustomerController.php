<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $trashed = $this->wantsTrashed($request);
        $customers = $this->filterQuery($request, $trashed)->paginate(10)->withQueryString();

        return view('customers.index', [
            'customers' => $customers,
            'search' => $request->string('q')->toString(),
            'currentStatus' => $trashed ? 'arsip' : '',
        ]);
    }

    public function table(Request $request): JsonResponse
    {
        $trashed = $this->wantsTrashed($request);
        $customers = $this->filterQuery($request, $trashed)->paginate(10)->withQueryString();

        return response()->json([
            'total' => $customers->total(),
            'html' => view('customers.partials.table', [
                'customers' => $customers,
                'archived' => $trashed,
            ])->render(),
            'pagination' => $customers->links()->toHtml(),
        ]);
    }

    private function wantsTrashed(Request $request): bool
    {
        return $request->string('status')->toString() === 'arsip';
    }

    private function filterQuery(Request $request, bool $trashed = false)
    {
        return Customer::query()
            ->withCount('devices')
            ->when($trashed, fn ($query) => $query->onlyTrashed())
            ->when($request->filled('q'), fn ($query) => $query
                ->where('nama', 'like', "%{$request->string('q')}%")
                ->orWhere('no_hp', 'like', "%{$request->string('q')}%"))
            ->latest();
    }

    public function show(Customer $customer): View
    {
        $customer->loadCount('devices');
        $customer->load(['devices' => fn ($query) => $query->withCount('serviceOrders')->with(['serviceOrders' => fn ($q) => $q->latest()])]);

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

        return response()->json(['message' => 'Pelanggan diarsipkan.']);
    }

    public function restore(int $customer): JsonResponse
    {
        $customer = Customer::onlyTrashed()->findOrFail($customer);
        $customer->restore();

        return response()->json(['message' => 'Pelanggan dipulihkan.']);
    }

    public function destroyPermanently(int $customer): JsonResponse
    {
        $customer = Customer::onlyTrashed()->findOrFail($customer);
        $customer->forceDelete();

        return response()->json(['message' => 'Pelanggan dihapus permanen.']);
    }

    public function detail(Customer $customer): JsonResponse
    {
        $customer->loadCount('devices');
        $customer->load(['devices' => fn ($q) => $q->withCount('serviceOrders')->with(['serviceOrders' => fn ($q) => $q->latest()])]);

        return response()->json(['customer' => $customer]);
    }

    public function export(Request $request): StreamedResponse
    {
        $customers = $this->filterQuery($request)->get();

        return new StreamedResponse(function () use ($customers) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['ID', 'Nama', 'No HP', 'Alamat', 'Perangkat']);

            foreach ($customers as $customer) {
                fputcsv($handle, [
                    $customer->id,
                    $customer->nama,
                    $customer->no_hp,
                    $customer->alamat,
                    $customer->devices_count,
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="pelanggan-'.now()->format('Y-m-d').'.csv"',
        ]);
    }

    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        fgetcsv($handle);

        $imported = 0;
        $updated = 0;
        $skipped = [];
        $existing = Customer::query()->pluck('id', 'no_hp');

        while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            $nama = trim($row[1] ?? '');
            $noHp = trim((string) ($row[2] ?? ''));
            $alamat = trim($row[3] ?? '') ?: null;

            if ($nama === '' || $noHp === '') {
                continue;
            }

            if (($customerId = $existing[$noHp] ?? null) !== null) {
                Customer::query()->find($customerId)?->update(compact('nama', 'alamat'));
                $updated++;
                continue;
            }

            Customer::create([
                'nama' => $nama,
                'no_hp' => $noHp,
                'alamat' => $alamat,
            ]);
            $existing[$noHp] = true;
            $imported++;
        }

        fclose($handle);

        return response()->json([
            'message' => "Import selesai: {$imported} baru, {$updated} diperbarui, ".count($skipped).' dilewati.',
            'imported' => $imported,
            'updated' => $updated,
            'skipped' => count($skipped),
        ]);
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
