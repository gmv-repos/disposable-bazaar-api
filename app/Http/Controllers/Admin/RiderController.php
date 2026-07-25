<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rider;
use App\Models\Order;
use PhpParser\Node\Expr\Array_;

class RiderController extends Controller
{
    public function index()
    {
        $riders = Rider::all();
        return view('adminPanel.riders.index', compact('riders'));
    }
    public function create()
    {
        return view('adminPanel.riders.create');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'phone' => 'required|numeric',
            'address' => 'required',
            'status' => 'required',
        ]);

        $data = $validated;
        if ($request->has('email')) {
            $data['email'] = $request->input('email');
        }

        Rider::create($data);

        return redirect()->route('riders.index')->with('success', 'Rider created successfully!');
    }
    public function show(Request $request, $id)
    {
        $rider = Rider::findOrFail($id);

        $payToRider = $rider->orders
            ->where('pay_method', '=', 2)
            ->where('rider_pay_status', '=', 'unpaid')
            ->sum('shipping_charges');

        $onlineWithoutDC = $rider->orders
            ->where('pay_method', '=', 3)
            ->where('rider_pay_status', '=', 'unpaid')
            ->sum('shipping_charges');

        $paidToRider = $rider->orders
            ->where('pay_method', '=', 2)
            ->where('rider_pay_status', '=', 'paid')
            ->sum('shipping_charges');

        $payToCompany = $rider->orders
            ->where('pay_method', '=', 1)
            ->where('rider_pay_status', '=', 'unpaid')
            ->sum('total_amount');

        $paidToCompany = $rider->orders
            ->where('pay_method', '=', 1)
            ->where('rider_pay_status', '=', 'paid')
            ->sum('total_amount');

        $status = $request->get('status');
        $sellType = $request->get('sell_type');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $payStatus = $request->get('payStatus');
        $payMethod = $request->get('payMethod');

        // Build the query
        $query = Order::with('customer')->where('rider_id', $id);

        if ($status != '') {
            $query->where('order_status', $status);
        }

        if ($payStatus != '') {
            $query->where('rider_pay_status', $payStatus);
        }

        if ($sellType != '') {
            $query->where('sell_type', $sellType)->where('deleted', 0);
        }

        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        if ($payMethod) {
            $query->where('pay_method', '=', $payMethod);
        }

        $orderList = $query->get();

        $data = compact(
            'rider',
            'payToRider',
            'paidToRider',
            'payToCompany',
            'paidToCompany',
            'orderList',
            'fromDate',
            'toDate',
            'onlineWithoutDC',
        );

        return view('adminPanel.riders.show', $data);
    }

    public function edit($id)
    {
        $rider = Rider::findOrFail($id);

        return view('adminPanel.riders.edit', compact('rider'));
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required',
            'phone' => 'required|numeric',
            'address' => 'required',
            'status' => 'required',
        ]);

        $data = $validated;

        if ($request->has('email')) {
            $data['email'] = $request->input('email');
        }

        $rider = Rider::findOrFail($id);
        $rider->update($data);

        return redirect()->route('riders.index')->with('success', 'Rider updated successfully!');
    }

    public function destroy($id)
    {
        $rider = Rider::findOrFail($id);
        $rider->delete();
        return redirect()->route('riders.index')->with('success', 'Rider deleted successfully!');
    }
}
