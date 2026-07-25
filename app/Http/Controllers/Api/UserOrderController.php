<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserOrderDetailsResource;
use App\Http\Resources\UserOrderListResource;
use App\Models\Sell;
use App\Models\SellDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Enums\OrderStatus;

class UserOrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'invoice_id' => 'required|string|unique:sells',
            'sell_by' => 'required|string',
            'bank_id' => 'nullable|integer',
            'shipping_cost' => 'required|numeric',
            'total_vat_amount' => 'required|numeric',
            'total_discount' => 'required|numeric',
            'total_payable_amount' => 'required|numeric',
            'total_paid' => 'required|numeric',
            'total_due' => 'required|numeric',
            'order_status' => 'required',
            'pay_method' => 'required',
            'products' => 'required|array',
            'products.*.product_id' => 'required|integer',
            'products.*.unit_product_cost' => 'required|numeric',
            'products.*.unit_sell_price' => 'required|numeric',
            'products.*.unit_vat' => 'required|numeric',
            'products.*.sale_quantity' => 'required|integer',
            'products.*.total_discount' => 'required|numeric',
            'products.*.total_payable_amount' => 'required|numeric',
        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Create a new Sell record
            $sell = Sell::create([
                'customer_id' => Auth::user()->id,
                'invoice_id' => $validatedData['invoice_id'],
                'sell_type' => 2, // Assuming sell_type 2 is for this type of order
                'sell_by' => $validatedData['sell_by'],
                'bank_id' => $validatedData['bank_id'] ?? null,
                'shipping_cost' => $validatedData['shipping_cost'],
                'total_vat_amount' => $validatedData['total_vat_amount'],
                'total_discount' => $validatedData['total_discount'],
                'total_payable_amount' => $validatedData['total_payable_amount'],
                'total_paid' => $validatedData['total_paid'],
                'total_due' => $validatedData['total_due'],
                'order_status' => $validatedData['order_status'],
                'pay_method' => $validatedData['pay_method'],
            ]);

            // Create SellDetail records
            foreach ($validatedData['products'] as $product) {
                SellDetail::create([
                    'product_id' => $product['product_id'],
                    'sell_id' => $sell->id,
                    'unit_product_cost' => $product['unit_product_cost'],
                    'unit_sell_price' => $product['unit_sell_price'],
                    'unit_vat' => $product['unit_vat'],
                    'sale_quantity' => $product['sale_quantity'],
                    'total_discount' => $product['total_discount'],
                    'total_payable_amount' => $product['total_payable_amount'],
                    'status' => 'available', // or based on your logic
                    'created_by' => Auth::user()->id,
                ]);
            }

            // Commit the transaction
            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Order placed successfully'], 201);
        } catch (\Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollBack();

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function orderList()
    {
        $userId = Auth::user()->id;

        $orderList = Sell::where('sell_type', 2)->where('customer_id', $userId)->orderBy('id', 'desc')->get();

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Order list retrieved successfully',
                'data' => UserOrderListResource::collection($orderList),
            ],
            200,
        );
    }

    public function orderDetails($id)
    {
        $order = Sell::with('sellDetail')->where('sell_type', 2)->where('id', $id)->first();

        if (!$order) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Order not found',
                ],
                404,
            );
        }

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Order details retrieved successfully',
                'data' => new UserOrderDetailsResource($order),
            ],
            200,
        );
    }

    public function cancel(Request $request)
    {
        $data = Sell::where('id', $request->order_id)->update(['order_status' => OrderStatus::ON_THE_WAY]);
        if ($data) {
            return response()->json(['status' => 200, 'msg' => 'Address created successfully']);
        }
    }
}