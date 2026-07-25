<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Sell;
use App\Models\Rider;
use App\Services\GoogleSheetService;
use Barryvdh\DomPDF\Facade\Pdf;
use setasign\Fpdi\Tcpdf\Fpdi;
use App\Enums\OrderStatus;


class SaleAndOrderController extends Controller
{
    public function index(Request $request)
    {
        $common_data = new \stdClass();
        $common_data->title = 'Sales and Orders';


        $from_date = $request->query('from_date') ?? now()->subMonth()->toDateString();
        $to_date = $request->query('to_date') ?? now()->toDateString();
        $riderID = $request->query('filter_rider');
        $status = $request->query('status');

        $orderQuery = Order::with('customer')
            ->whereDate('created_at', '>=', $from_date)
            ->whereDate('created_at', '<=', $to_date)
            ->when($riderID, fn($query) => $query->where('rider_id', $riderID))
            ->when($status, fn($query) => $query->where('status', $status));


        $sellListQuery = Sell::with('customer')
            ->whereDate('created_at', '>=', $from_date)
            ->whereDate('created_at', '<=', $to_date)
            ->when($riderID, fn($query) => $query->where('rider_id', $riderID))
            ->when($status, fn($query) => $query->where('status', $status));

        $orderList = $orderQuery->orderByDesc('created_at')->get();
        $sellList = $sellListQuery->orderByDesc('created_at')->get();


        $riders = Rider::all();

        $data = compact(
            'orderList',
            'sellList',
            'common_data',
            'riders',
            'from_date',
            'to_date',
            'status',
        );


        return view('adminPanel.sales_and_orders.index', $data);
    }



    public function allocateRider(Request $request)
    {
        $type = $request->input('item_type');
        $item_id = $request->input('item_id');
        $rider_id = $request->input('rider_id');

        if ($type == 'order') {
            $item = Order::find($item_id);
        }
        if ($type == 'sell') {
            $item = Sell::find($item_id);
        }

        $item->rider_id = $rider_id;
        $item->rider_pay_status = 'unpaid';
        $item->save();

        return response()->json(['success' => true, 'message' => 'Rider allocated successfully.']);
    }

    public function sellAllocateRider(Request $request)
    {

        $sell = Sell::find($request->sell_id);
        $sell->rider_id = $request->rider_id;
        $sell->rider_pay_status = 'unpaid';
        $sell->save();

        return response()->json(['success' => true, 'message' => 'Rider allocated successfully.']);
    }

    public function multiActionForm(Request $request)
    {
        $request->validate([
            'formAction' => 'required|in:downloadPDF,print,riderAndPay',
        ]);

        $formAction = $request->input('formAction');
        $orderIds = $request->input('orderIds', []);
        $sellIds = $request->input('sellIds', []);

        $riderID = $request->input('riderID', null);
        $paymentMethod = $request->input('paymentMethod', null);

        if (blank($orderIds) && blank($sellIds)) {
            return redirect()->back()->with('error', 'No items selected.');
        }

        switch ($formAction) {
            case 'downloadPDF':
                return $this->printOrDownload($orderIds, $sellIds, 'D');

            case 'print':
                return $this->printOrDownload($orderIds, $sellIds, 'I');

            case 'riderAndPay':
                return $this->allocateRiderAndPayMethod($orderIds, $sellIds, $riderID, $paymentMethod);

            default:
                return redirect()->back()->with('error', 'Invalid action.');
        }
    }


    public function printOrDownload($orderIds, $sellIds, $action)
    {
        $pdf = new Fpdi();
        $counter = 1;

        // === ORDERS SECTION ===
        if (!blank($orderIds)) {
            $orders = Order::with(['orderDetails', 'orderBilling', 'orderDetails.product', 'customer'])
                ->whereIn('id', $orderIds)
                ->get();

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
        }

        // === SELLS SECTION ===
        if (!blank($sellIds)) {
            $sells = Sell::with(['sellDetail', 'customer'])
                ->whereIn('id', $sellIds)
                ->get();

            foreach ($sells as $index => $oRow) {
                for ($pdfIndex = 1; $pdfIndex <= 4; $pdfIndex++) {
                    $sellPdf = PDF::loadView('adminPanel.pos.sell_invoice', [
                        'sell' => $oRow,
                        'counter' => $counter,
                        'pdfIndex' => $pdfIndex,
                    ]);

                    $tempFile = storage_path("app/temp_sell_{$index}_{$pdfIndex}.pdf");
                    $sellPdf->save($tempFile);

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
        }

        // === FINAL COMBINED OUTPUT ===
        $fileName = 'web_and_pos_orders_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->Output($fileName, $action);
    }



    private function allocateRiderAndPayMethod($orderIds, $sellIds, $riderID, $paymentMethod)
    {
        $updateData = [];

        if (filled($riderID)) {
            $updateData['rider_id'] = $riderID;
        }

        if (filled($paymentMethod)) {
            $updateData['pay_method'] = $paymentMethod;
        }

        if (!blank($orderIds)) {
            Order::whereIn('id', $orderIds)->update($updateData);

            $orders = Order::with('rider')
                ->whereIn('id', $orderIds)
                ->get();

            foreach ($orders as $order) {
                if ($order->rider) {
                    GoogleSheetService::updateOrder($order->order_no)
                        ->set('Rider', $order->rider->name)
                        ->save();
                }
            }
        }

        if (!blank($sellIds)) {
            Sell::whereIn('id', $sellIds)->update($updateData);
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

                    if ($newStatus) {
                        $order->order_status = $newStatus;
                    }

                    $order->save();

                    if ($order->rider && $newStatus == OrderStatus::COMPLETED) {
                        $order->rider->increment('earning', $order->shipping_charges);
                    }

                    // if ($this->shouldSendSmsForStatusChange($previousStatus, $newStatus)) {
                    //     $this->createSmsEntry($order, $newStatus);
                    // }
                });
            } catch (\Exception $e) {
                continue;
            }
        }

        return redirect()->back()->with('success', 'Successfully Order Status Updated. SMS will be sent shortly.');
    }
}