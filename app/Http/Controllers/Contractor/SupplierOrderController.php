<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contractor\StoreSupplierOrderRequest;
use App\Http\Requests\Contractor\UpdateSupplierOrderRequest;
use App\Models\SupplierOrder;
use App\Models\SupplierPrice;
use App\Services\Contractor\SupplierOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierOrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = SupplierOrder::query()
            ->where('contractor_id', $request->user()->id)
            ->with(['supplier', 'project', 'payment'])
            ->latest()
            ->get();

        return view('contractor.suppliers.orders', compact('orders'));
    }

    public function show(Request $request, SupplierOrder $order): View
    {
        $this->authorize('view', $order);
        $order->load(['supplier', 'project', 'payment', 'price', 'priceRequest']);

        return view('contractor.suppliers.order', compact('order'));
    }

    public function store(StoreSupplierOrderRequest $request, SupplierPrice $price, SupplierOrderService $orders): RedirectResponse
    {
        $order = $orders->place(
            $request->user(),
            $price,
            $request->input('delivery_address'),
            $request->input('notes'),
        );

        return redirect()
            ->route('contractor.orders.show', $order)
            ->with('status', 'Order placed. A payment request was sent to the homeowner. It is not paid.');
    }

    public function update(UpdateSupplierOrderRequest $request, SupplierOrder $order, SupplierOrderService $orders): RedirectResponse
    {
        $orders->advance($order, $request->string('status')->toString());

        return back()->with('status', 'Order status updated.');
    }
}
