<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SmsDetails;
use App\Models\User;
use App\Models\Sell;
use App\Models\Rider;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\Array_;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use setasign\Fpdi\Tcpdf\Fpdi;
use App\Services\GoogleSheetService;
use App\Services\WhatsAppService;
use App\Enums\OrderStatus;

class OrderController extends Controller
{
    public function ecommerceOrderList(Request $request)
    {
        // Initialize common data array
        $common_data = new Array_();

        $status = $request->get('status', null);
        $sell_type = $request->get('sell_type', null);

        $from_date = $request->get('from_date', null);
        $to_date = $request->get('to_date', null);
        $riderID = $request->query('byRider');

        $common_data->title = 'All Orders';

        $query = Order::with(['customer']);

        if ($status !== null) {
            switch ($status) {
                case OrderStatus::PENDING:
                    $common_data->title = 'Pending Order';
                    break;

                case OrderStatus::PROCESSING:
                    $common_data->title = 'Processing Order';
                    break;
                case OrderStatus::ON_THE_WAY:
                    $common_data->title = 'Order On The Way';
                    break;
                case OrderStatus::CANCELED:
                    $common_data->title = 'Order Canceled Accepted';
                    break;
                case OrderStatus::COMPLETED:
                    $common_data->title = 'Completed Order';
                    break;
                default:
                    $common_data->title = 'All Orders';
            }

            $query->where(['order_status' => $status]);
        }

        if ($sell_type !== null) {
            $query->where(['sell_type' => $sell_type, 'deleted' => 0]);
        }

        if (!is_null($from_date)) {
            $query->whereDate('created_at', '>=', $from_date);
        }

        if (!is_null($to_date)) {
            $query->whereDate('created_at', '<=', $to_date);
        }

        if (!is_null($riderID)) {
            $query->where('rider_id', '=', $riderID);
        }

        $orderList = $query->orderBy('id', 'desc')->get();

        $riders = Rider::all();

        return view('adminPanel.order._order_ecommerce_list')->with(
            compact('orderList', 'common_data', 'from_date', 'to_date', 'riders'),
        );
    }

    public function SellOrderDetails(Request $request)
    {
        $orderList = Order::with(['orderDetails', 'orderBilling', 'orderDetails.product', 'customer'])->find(
            $request->order_id,
        );

        return view('adminPanel.order._order_details')->with(compact('orderList'))->render();
    }

    public function ecommerceCustomerList(Request $request)
    {
        $common_data = new Array_();
        $common_data->title = 'E-Commerce Customer List'; // Default title

        $from_date = $request->get('from_date', null);
        $to_date = $request->get('to_date', null);

        // Fetch unique customers who have placed orders
        $query = Order::select('name', 'email', 'phone', 'grand_total', 'order_status')->whereNotNull('name');

        if (!is_null($from_date)) {
            $query->whereDate('created_at', '>=', $from_date);
        }
        if (!is_null($to_date)) {
            $query->whereDate('created_at', '<=', $to_date);
        }
        $orders = $query->get()->groupBy('email');

        // Prepare data for customers with their total amount spent and due amounts
        $customerData = $orders->map(function ($group) {
            $firstOrder = $group->first();

            // Ensure that values are treated as numbers
            $totalSpent = $group->sum(function ($order) {
                return (float) $order->grand_total; // Cast to float
            });
            $totalPaid = $group->where('order_status', OrderStatus::COMPLETED)->sum(function ($order) {
                return (float) $order->grand_total; // Cast to float
            });
            $dueAmount = $group->whereIn('order_status', [
                OrderStatus::PENDING,
                OrderStatus::PROCESSING,
                OrderStatus::ON_THE_WAY
            ])
                ->sum(function ($order) {
                    return (float) $order->grand_total; // Cast to float
                });

            return [
                'name' => $firstOrder->name,
                'email' => $firstOrder->email,
                'phone' => $firstOrder->phone,
                'total_spent' => $totalSpent,
                'total_paid' => $totalPaid,
                'due_amount' => $dueAmount,
                'orders_count' => $group->count(), // Count of orders for this customer
            ];
        });

        return view('adminPanel.order._customer_list', compact('customerData', 'common_data', 'from_date', 'to_date'));
    }

    public function OrderStatusUpdate(Request $request)
    {
        $orderIds = $request->order_ids;
        $newStatus = (int) $request->status;

        foreach ($orderIds as $orderId) {
            try {
                DB::transaction(function () use ($orderId, $newStatus) {
                    $order = Order::find($orderId);

                    if (!$order) {
                        throw new \Exception('Order not found for ID: ' . $orderId);
                    }

                    $previousStatus = (int) $order->order_status;

                    Log::info('Previous Status: ' . $previousStatus . ', New Status: ' . $newStatus);

                    if ($newStatus) {
                        $order->order_status = $newStatus;
                    }

                    $order->save();

                    $statusToSheet = match ($newStatus) {
                        OrderStatus::PENDING => 'Pending',
                        OrderStatus::PROCESSING => 'Processing',
                        OrderStatus::ON_THE_WAY => 'On the way',
                        OrderStatus::CANCELED => 'Cancelled',
                        OrderStatus::COMPLETED => 'Completed',
                        default => throw new \Exception("Invalid status code: $newStatus"),
                    };

                    GoogleSheetService::updateOrder($order->order_no)
                        ->set('Order Status', $statusToSheet)
                        ->save();

                    // Cancelled
                    if ($newStatus == OrderStatus::CANCELED) {
                        (new WhatsAppService())
                            ->to($order->phone)
                            ->template(WhatsAppService::ORDER_CANCELLED)
                            ->params([
                                $order->name ?? "Guest",
                                $order->order_no,
                                '15',
                            ])
                            ->send();
                    }

                    // Completed / Delivered
                    if ($newStatus == OrderStatus::COMPLETED) {
                        (new WhatsAppService())
                            ->to($order->phone)
                            ->template(WhatsAppService::ORDER_DELIVERED)
                            ->params([
                                $order->name ?? "Guest",
                                $order->order_no,
                            ])
                            ->send();
                    }


                    if ($order->rider && $newStatus == OrderStatus::COMPLETED) {
                        $order->rider->increment('earning', $order->shipping_charges);
                    }

                    if ($this->shouldSendSmsForStatusChange($previousStatus, $newStatus)) {
                        $this->createSmsEntry($order, $newStatus);
                    }
                });
            } catch (\Exception $e) {
                Log::error('Transaction failed for Order ID: ' . $orderId . ' - ' . $e->getMessage());
                continue;
            }
        }

        return redirect()->back()->with('success', 'Successfully Order Status Updated. SMS will be sent shortly.');
    }
    public function OrderPayStatusUpdate(Request $request)
    {
        $orderIds = $request->order_ids;
        $payStatus = $request->pay_status;

        foreach ($orderIds as $orderId) {
            try {
                DB::transaction(function () use ($orderId, $payStatus) {
                    $order = Order::find($orderId);

                    if (!$order) {
                        throw new \Exception('Order not found for ID: ' . $orderId);
                    }

                    if ($payStatus) {
                        $order->rider_pay_status = $payStatus;
                    }
                    $order->save();
                });
            } catch (\Exception $e) {
                Log::error('Transaction failed for Order ID: ' . $orderId . ' - ' . $e->getMessage());
                continue;
            }
        }

        return redirect()->back()->with('success', 'Successfully Pay Status Updated.');
    }

    // Helper method to check if the status transition is valid for sending SMS
    protected function shouldSendSmsForStatusChange($previousStatus, $newStatus)
    {
        // Define valid status transitions based on numbers
        $statusTransitions = [
            1 => 2, // Pending to Confirmed
            2 => 3, // Confirmed to On the way
            3 => 5, // On the way to Completed
        ];

        return isset($statusTransitions[$previousStatus]) && $statusTransitions[$previousStatus] === $newStatus;
    }

    // Helper method to create the SMS entry
    protected function createSmsEntry($order, $status)
    {
        $phoneNumber = $order->phone; // Assuming the user model has a phone field
        $recipientName = $order->name; // Assuming the user model has a name field

        // Create a personalized message based on the status
        $message = $this->buildSmsMessage($order, $status);

        // Create an entry in the sms_details table
        SmsDetails::create([
            'order_id' => $order->id,
            'phone_number' => $phoneNumber,
            'recipient_name' => $recipientName,
            'status' => SmsDetails::STATUS_PENDING,
            'message_type' => 'Order Status Update',
            'message' => $message,
        ]);
    }

    // Helper method to build a nice, appealing SMS message based on status
    protected function buildSmsMessage($order, $status)
    {
        $recipientName = $order->name;
        $orderId = $order->order_no;
        $statusMessage = '';

        switch ($status) {
            case 2: // Confirmed
                $statusMessage = "Great news! Your order #$orderId has been confirmed. We will notify you when it's on the way!";
                break;
            case 3: // On the way
                $statusMessage = "Good news, $recipientName! Your order #$orderId is on the way and will be delivered soon. Stay tuned!";
                break;
            case 4: // Completed
                $statusMessage = "Thank you for shopping with us, $recipientName! Your order #$orderId has been delivered successfully. We hope you enjoy your purchase!";
                break;
            default:
                $statusMessage = "Thank you, $recipientName! Your order status has been updated.";
        }

        return $statusMessage;
    }

    // public function OrderStatusUpdate(Request $request)
    // {
    //     $order = Order::find($request->order_id);
    //     $newStatus = $request->status;
    //     $previousStatus = $order->order_status;

    //     if ($this->shouldSendSmsForStatusChange($previousStatus, $newStatus)) {
    //         $order->status = $newStatus;
    //         $order->save();

    //         $this->createSmsEntry($order, $newStatus);

    //         $info = Order::where('id', $request->order_id)->update(['order_status' => $request->status]);

    //
    //     } else {
    //         return response()->json(['message' => 'No SMS sent for this status change.']);
    //     }
    // }

    public function printOrDownload($orderIDs, $action)
    {
        $orders = Order::with(['orderDetails', 'orderBilling', 'orderDetails.product', 'customer'])
            ->whereIn('id', $orderIDs)
            ->get();

        $pdf = new Fpdi();
        $counter = 1;

        foreach ($orders as $index => $oRow) {
            for ($pdfIndex = 1; $pdfIndex <= 3; $pdfIndex++) {
                $orderPdf = PDF::loadView('adminPanel.order.order_pdf', [
                    'order' => $oRow,
                    'counter' => $counter,
                    'pdfIndex' => $pdfIndex,
                ]);

                $tempFile = storage_path("app/temp_order_{$index}_{$pdfIndex}.pdf");
                $orderPdf->save($tempFile);

                $pageCount = $pdf->setSourceFile($tempFile);
                for ($i = 1; $i <= $pageCount; $i++) {
                    $tplId = $pdf->importPage($i);
                    $pdf->addPage();
                    $pdf->useTemplate($tplId);
                }

                unlink($tempFile);
                $counter++;
            }
        }

        return $pdf->Output('combined_order_pdfs.pdf', $action);
    }

    public function ordersMultiAction(Request $request)
    {
        $ordersAction = $request->input('ordersAction');

        $orderIDs = $request->input('order_ids', []);

        $ordersStatus = $request->input('ordersStatus');

        $riderID = $request->input('riderID');
        $paymentMethod = $request->input('paymentMethod');

        if (empty($orderIDs)) {
            return redirect()->back()->with('error', 'No items selected.');
        }

        switch ($ordersAction) {
            case 'downloadPDF':
                return $this->printOrDownload($orderIDs, 'D');

            case 'print':
                return $this->printOrDownload($orderIDs, 'I');

            case 'changeOrderStatus':
                return $this->changeOrderStatus($orderIDs, $ordersStatus);

            case 'allocateRiderAndPayMethod':
                return $this->allocateRiderAndPayMethod($orderIDs, $riderID, $paymentMethod);
            default:
                return redirect()->back()->with('error', 'Invalid action.');
        }
    }

    private function allocateRiderAndPayMethod($orderIDs, $riderID, $paymentMethod)
    {
        $updateData = [];

        if (!is_null($riderID)) {
            $updateData['rider_id'] = $riderID;
        }

        if (!is_null($paymentMethod)) {
            $updateData['pay_method'] = $paymentMethod;
        }

        if (!empty($updateData)) {
            Order::whereIn('id', $orderIDs)->update($updateData);
        }

        return redirect()->back()->with('success', 'Selected rider and payment method successfully applied');
    }

    private function changeOrderStatus($orderIds, $newStatus)
    {
        foreach ($orderIds as $orderId) {
            try {
                DB::transaction(function () use ($orderId, $newStatus) {
                    $order = Order::find($orderId);

                    if (!$order) {
                        throw new \Exception('Order not found for ID: ' . $orderId);
                    }

                    $previousStatus = (int) $order->order_status;

                    Log::info('Previous Status: ' . $previousStatus . ', New Status: ' . $newStatus);

                    if ($newStatus) {
                        $order->order_status = $newStatus;
                    }

                    $order->save();

                    if ($order->rider && $newStatus == OrderStatus::COMPLETED) {
                        $order->rider->increment('earning', $order->shipping_charges);
                    }

                    if ($this->shouldSendSmsForStatusChange($previousStatus, $newStatus)) {
                        $this->createSmsEntry($order, $newStatus);
                    }
                });
            } catch (\Exception $e) {
                Log::error('Transaction failed for Order ID: ' . $orderId . ' - ' . $e->getMessage());
                continue;
            }
        }

        return redirect()->back()->with('success', 'Successfully Order Status Updated. SMS will be sent shortly.');
    }
}