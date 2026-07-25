<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Order;
use App\Models\OrderBilling;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Services\GoogleSheetService;
use App\Services\WhatsAppService;
use App\Models\Bundle;
use App\Models\Stock;
use App\Models\StockLog;
use Illuminate\Support\Facades\DB;
use App\Enums\OrderStatus;


class OrderController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();

            // Validate the incoming request data
            $validator = Validator::make($input, [
                'order_date' => 'required|date',
                'first_name' => 'required|regex:/^[a-zA-Z0-9\s\-\_\@\#\$\%\^\&\*\(\)]+$/',
                'last_name' => 'required|regex:/^[a-zA-Z0-9\s\-\_\@\#\$\%\^\&\*\(\)]+$/',
                // 'email' => 'email|required',
                'mobile_no' => 'required',
                'sub_total' => 'required|numeric',
                'area_id' => 'required|exists:areas,id',
                'grand_total' => 'required|numeric',
                'billing_address' => 'required|string',
                'special_instruction' => 'nullable|string',
                'bundle_ids' => 'nullable|array',
                'bundle_qtys' => 'nullable|array',
                'order_detail' => 'required_without:bundle_ids|array',
                'order_detail.*.product_id' => 'required|exists:products,id',
                'order_detail.*.quantity' => 'required|integer|min:1',
                'order_detail.*.pack_size' => 'required|numeric',
                'order_detail.*.total_pieces' => 'required|numeric',
                'order_detail.*.product_sub_total' => 'required|numeric',
                'continue_as_guest' => 'required|boolean',
                // Additional validation for is_customize
                'order_detail.*._is_customize' => 'required|boolean',
                'order_detail.*.customize_logo_image' => 'nullable|mimes:pdf,ai,cdr,psd|max:10240', // Add image validation
                'order_detail.*.product_option_id' => 'nullable|exists:product_options,id',
            ]);

            // If validation fails, return a warning response
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'warning',
                    'message' => $validator->errors()->first(),
                ]);
            }
            if ($request->continue_as_guest == 1) {
                $userId = null;
                $user_type = 1;
            } else {
                // Expect the user ID to come from the request
                if (isset($request->user_id) && !empty($request->user_id)) {
                    $userId = $request->user_id;
                    $user_type = 2;
                } else {
                    // Return an error if user_id is not provided in the request when continue_as_guest is false
                    return response()->json(
                        [
                            'status' => 'error',
                            'message' => 'User ID is required when not continuing as guest.',
                        ],
                        400,
                    );
                }
            }

            $bundleData = null;
            if (!is_null($request->bundle_ids)) {
                foreach ($request->bundle_ids as $key => $val) {
                    $bundleData[] = [
                        'id' => $val,
                        'qty' => $request->bundle_qtys[$key],
                    ];
                }
                $bundleData = json_encode($bundleData);
            }

            $area = Area::find($request->area_id);

            // Create the order
            $order = Order::create([
                'customer_id' => $userId,
                'order_date' => $request->order_date,
                'order_no' => 'ORD-' . strtoupper(uniqid()), // Generates a unique order number
                'bundle_ids' => $bundleData,
                'name' => $request->first_name . ' ' . $request->last_name,
                'phone' => $request->mobile_no,
                'email' => $request->email,
                'total_amount' => $request->sub_total,
                'shipping_charges' => $area->shipping_rate, // Get shipping rate from area
                'discount_amount' => 0, // Modify if discounts are available
                'grand_total' => $request->grand_total,
                'user_type' => $user_type,
                'order_status' => OrderStatus::PENDING, // Set default status to "Pending"
                'status' => 1, // Active order
            ]);

            // Save order billing information
            $orderBilling = OrderBilling::create([
                'order_id' => $order->id,
                'area_id' => $request->area_id,
                'address' => $request->billing_address,
                'special_instructions' => $request->special_instruction,
            ]);

            // Save order details (products)
            foreach ($request->input('order_detail', []) as $key => $orderDetail) {
                $customizeLogoImage = null;
                if ($orderDetail['_is_customize']) {
                    log::info('Checking developent rocessign line: ' . json_encode($orderDetail));
                    // Handle the image upload if customization is requested
                    if (
                        isset($orderDetail['customize_logo_image']) &&
                        $request->hasFile("order_detail.$key.customize_logo_image")
                    ) {
                        $file = $request->file("order_detail.$key.customize_logo_image");
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        $customizeLogoImage = $file->storeAs('customize_order_logos', $fileName, 'public'); // Store the file
                    }
                }

                $productOptionId =
                    $orderDetail['_is_customize'] && isset($orderDetail['product_option_id'])
                    ? $orderDetail['product_option_id']
                    : null;

                $product_lid_option_id =
                    array_key_exists('lid', $orderDetail) && $orderDetail['lid'] ? $orderDetail['lid'] : null;

                OrderDetails::create([
                    'order_id' => $order->id,
                    'product_id' => $orderDetail['product_id'],
                    'product_lid_option_id' => $product_lid_option_id,
                    'pack_size' => $orderDetail['pack_size'],
                    // 'variant_size' => $orderDetail['variant_size'] ?? null,
                    // 'lid_price' => $orderDetail['lid_price'] ?? null,
                    // 'printing_price' => $orderDetail['lid_price'] ?? null,
                    'qty' => $orderDetail['quantity'],
                    'total_peices' => $orderDetail['total_pieces'],
                    'product_sub_total' => $orderDetail['product_sub_total'],
                    'is_customize' => $orderDetail['_is_customize'],
                    'customize_logo_image' => $customizeLogoImage, // Store image if provided
                    'packaging_options' => $orderDetail['packagingOptions'],
                    'product_option_id' => $productOptionId, // Store option ID if provided
                    'additional_customization' => $orderDetail['customizeDetail'] ?? null,
                ]);

                // --- Stock Handling ---
                $stock = Stock::where([
                    'product_id' => $orderDetail['product_id'],
                    'brand_id' => $orderDetail['brand_id'] ?? null,
                ])->first();

                if ($stock) {
                    $stock->qty = $stock->qty - $orderDetail['total_pieces'];
                    $stock->save();

                    // --- Stock Log ---
                    StockLog::create([
                        'type' => StockLog::TYPE_OUT,
                        'stock_id' => $stock->id,
                        'party_type' => User::class,
                        'party_id' => $userId ?? User::first()->id,
                        'product_id' => $orderDetail['product_id'],
                        'brand_id' => $orderDetail['brand_id'],
                        'price' => $orderDetail['product_sub_total'] / $orderDetail['quantity'],
                        'qty' => $orderDetail['quantity'],
                    ]);
                }
            }

            ////Google Sheet
            GoogleSheetService::addOrder($order)
                ->set('Area', $area->area_name)
                ->save();

            $productNames = $order->orderDetails
                ->pluck('product.name')
                ->implode(', ');

            // WhatsApp Message
            (new WhatsAppService())
                ->to($order->phone)
                ->template(WhatsAppService::ORDER_PLACED)
                ->params([
                    $order->name ?? "Guest",
                    $order->order_no,
                    $productNames,
                    $order->grand_total,
                ])
                ->send();


            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Your Order has been Placed successfully',
                'order_id' => $order->order_no,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error(
                'Order Placement Error: ' . $th->getMessage() . ' in ' . $th->getFile() . ' on line ' . $th->getLine(),
            );

            return response()->json(
                [
                    'status' => 'error',
                    'message' => $th->getMessage(),
                ],
                500,
            );
        }
    }

    public function index()
    {
        try {
            // Get the authenticated user
            $user = Auth::user();

            // Fetch orders of the authenticated user grouped by order status
            $orders = Order::with(['orderDetails', 'orderBilling'])
                ->where('customer_id', $user->id)
                ->orderByRaw('FIELD(order_status, 1, 2, 3, 4, 5)') // Sort by status: 1 = Pending, 2 = Confirmed, 3 = On the way, 4 = Completed, 5 = Declined
                ->orderBy('order_date', 'desc') // Order by date within each status group
                ->get();

            // Check if the user has any orders
            if ($orders->isEmpty()) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'No orders found for this user.',
                ]);
            }

            // Group orders by their status
            $groupedOrders = $orders->groupBy('order_status')->map(function ($orderGroup, $status) {
                switch ($status) {
                    case 1:
                        $statusLabel = 'Pending Orders';
                        break;
                    case 2:
                        $statusLabel = 'Approved Orders';
                        break;
                    case 3:
                        $statusLabel = 'On the Way Orders';
                        break;
                    case 4:
                        $statusLabel = 'Completed Orders';
                        break;
                    case 5:
                        $statusLabel = 'Declined Orders';
                        break;
                    default:
                        $statusLabel = 'Unknown Status';
                        break;
                }
                return [
                    'status_label' => $statusLabel,
                    'orders' => $orderGroup,
                ];
            });

            // Return the grouped orders with a success message
            return response()->json([
                'status' => 'success',
                'message' => 'Orders fetched successfully.',
                'data' => $groupedOrders,
            ]);
        } catch (\Throwable $th) {
            // Handle errors
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching orders.',
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function show($orderNo)
    {
        try {
            // Fetch the order by order_no for the authenticated user
            $order = Order::with(['orderDetails', 'orderBilling', 'orderDetails.product'])
                ->where('order_no', $orderNo)
                ->first();

            // Check if the order exists
            if (!$order) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Order not found.',
                ]);
            }

            // Return the order details with a success message
            return response()->json([
                'status' => 'success',
                'message' => 'Order fetched successfully.',
                'data' => $order,
            ]);
        } catch (\Throwable $th) {
            // Handle any exceptions that may occur
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the order.',
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function cancelOrder($orderid)
    {
        try {
            // Get the authenticated user
            $user = Auth::user();

            // Fetch the order by order ID for the authenticated user
            $order = Order::where('id', $orderid)->where('customer_id', $user->id)->first();

            // Check if the order exists
            if (!$order) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Order not found.',
                ]);
            }

            // Check the order status and respond accordingly
            switch ($order->order_status) {
                case OrderStatus::PENDING:
                case OrderStatus::PROCESSING:
                    $order->order_status = OrderStatus::CANCELED;
                    $order->save();

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Order canceled successfully.',
                    ]);

                case OrderStatus::ON_THE_WAY: // On the way
                    return response()->json([
                        'status' => 'warning',
                        'message' => 'Your order is on the way and cannot be canceled.',
                    ]);

                case OrderStatus::COMPLETED: // Completed
                    return response()->json([
                        'status' => 'warning',
                        'message' => 'Your order is completed and cannot be canceled.',
                    ]);

                case OrderStatus::CANCELED: // Already canceled
                    return response()->json([
                        'status' => 'warning',
                        'message' => 'This order is already canceled and cannot be canceled again.',
                    ]);

                default:
                    return response()->json([
                        'status' => 'warning',
                        'message' => 'Invalid order status.',
                    ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while canceling the order.',
                'error' => $th->getMessage(),
            ]);
        }
    }
}