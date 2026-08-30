<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ServiceOrderStatus;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoicePaymentRequest;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\ServiceOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $invoices = Invoice::query()
            ->with('serviceOrder.device.customer')
            ->when($request->filled('status'), fn ($query) => $query->where('status_bayar', $request->string('status')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('invoices.index', [
            'invoices' => $invoices,
            'currentStatus' => $request->string('status')->toString(),
        ]);
    }

    public function readyOrders(): JsonResponse
    {
        $orders = ServiceOrder::query()
            ->where('status', ServiceOrderStatus::Selesai)
            ->doesntHave('invoice')
            ->with(['device.customer'])
            ->latest()
            ->get();

        return response()->json([
            'orders' => $orders->map(fn ($order) => [
                'id' => $order->id,
                'no_tiket' => $order->no_tiket,
                'customer' => $order->device->customer->nama,
                'perangkat' => $order->device->merk.' '.$order->device->model,
            ]),
        ]);
    }

    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        $data = $request->validated();

        $invoice = DB::transaction(function () use ($data) {
            $total = 0;

            foreach ($data['items'] as $item) {
                $total += (int) $item['qty'] * (int) $item['harga'];
            }

            $invoice = Invoice::create([
                'service_order_id' => $data['service_order_id'],
                'total_biaya' => $total,
                'status_bayar' => $data['status_bayar'],
                'metode_bayar' => $data['metode_bayar'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'nama_item' => $item['nama_item'],
                    'tipe' => $item['tipe'],
                    'qty' => $item['qty'],
                    'harga' => $item['harga'],
                ]);
            }

            if ($invoice->isLunas()) {
                $this->closeServiceOrder($invoice->service_order_id);
            }

            return $invoice;
        });

        return response()->json(['invoice' => $invoice->load('items', 'serviceOrder.device.customer')]);
    }

    public function updatePayment(UpdateInvoicePaymentRequest $request, Invoice $invoice): JsonResponse
    {
        DB::transaction(function () use ($invoice, $request) {
            $invoice->update([
                'status_bayar' => PaymentStatus::Lunas,
                'metode_bayar' => $request->enum('metode_bayar', PaymentMethod::class),
            ]);

            $this->closeServiceOrder($invoice->service_order_id);
        });

        return response()->json(['invoice' => $invoice->fresh('serviceOrder')]);
    }

    public function print(Invoice $invoice): View
    {
        $invoice->load('serviceOrder.device.customer', 'items');

        return view('invoices.print', ['invoice' => $invoice]);
    }

    private function closeServiceOrder(int $serviceOrderId): void
    {
        ServiceOrder::whereKey($serviceOrderId)->update([
            'status' => ServiceOrderStatus::Diambil,
            'tanggal_selesai' => now()->toDateString(),
        ]);
    }
}
