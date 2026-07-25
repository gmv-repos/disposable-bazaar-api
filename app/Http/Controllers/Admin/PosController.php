<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Area;
use App\Models\PosCustomer;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Sell;
use App\Models\Sell_details;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Array_;
use setasign\Fpdi\Tcpdf\Fpdi;
use App\Models\Rider;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use App\Models\Stock;
use App\Models\StockLog;
use App\Enums\OrderStatus;

class PosController extends Controller
{
    public function addReturnInvoiceForm(Request $request)
    {
        $id = $request->input('id');
        $selldata = Sell::with('customer')->with('sellDetail')->find($id);
        return view('adminPanel.pos.returnInvoiceForm', compact('selldata'));
    }

    public function addReturnInvoiceStore(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required|integer|exists:sells,id',
            'return_remarks' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        // Get inputs
        $invoiceId = $request->input('invoice_id');
        $returnRemarks = $request->input('return_remarks');
        $returnDate = date('Y-m-d');

        // Update the `sells` table
        $updateStatus = DB::table('sells')
            ->where('id', $invoiceId)
            ->update([
                'order_status' => 7,
                'return_remarks' => $returnRemarks,
                'return_date' => $returnDate,
            ]);

        if ($updateStatus) {
            return redirect()->back()->with('success', 'Return Invoices Updated Successfully');
        } else {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Failed to update return invoice',
                ],
                500,
            );
        }
    }

    public function posView()
    {
        $common_data = new \stdClass();
        $common_data->title = 'Sell Product';
        $productList = Product::where('status', 1)->where('deleted', 0)->paginate(12);
        $posCustomerList = PosCustomer::where('status', 1)->where('deleted', 0)->get();
        $bankList = BankAccount::where('status', 1)->where('deleted', 0)->get();

        $salesOrderList = SalesOrder::whereHas('items', function ($query) {
            $query->whereColumn('quantity', '>', 'delivered_quantity');
        })
            ->orderBy('id', 'desc')
            ->get();

        $areaList = Area::all();
        $riders = Rider::all();

        return view('adminPanel.pos.pos')->with(
            compact('posCustomerList', 'bankList', 'salesOrderList', 'common_data', 'areaList', 'riders'),
        );
    }

    public function posCustomerList()
    {
        $posCustomer = PosCustomer::where('deleted', 0)->get();
        return view('adminPanel.pos.pos_customer')->with(compact('posCustomer'));
    }

    public function posCustomerStore(Request $request)
    {
        $pusCustomer = new PosCustomer();
        $pusCustomer->name = $request->name;
        $pusCustomer->phone = $request->phone;
        $pusCustomer->email = $request->email;
        $pusCustomer->delivery_charges = $request->delivery_charges;
        $pusCustomer->address = $request->address;
        $pusCustomer->save();
        return redirect()->back()->with('success', 'Customer Created Successfully');
    }

    public function posCustomerUpdate(Request $request)
    {
        $pusCustomer = PosCustomer::find($request->id);
        $pusCustomer->name = $request->name;
        $pusCustomer->phone = $request->phone;
        $pusCustomer->email = $request->email;
        $pusCustomer->area_id = $request->customer_area_id;

        $delivery_charges = Area::find($request->customer_area_id)->shipping_rate;

        $pusCustomer->delivery_charges = $delivery_charges;
        $pusCustomer->address = $request->address;
        $pusCustomer->save();
        return redirect()->back()->with('success', 'Customer Info Updated Successfully');
    }

    public function getPostProductList()
    {
        $productList = Product::where('status', 1)->where('deleted', 0)->paginate(12);

        return view('adminPanel.pos._card_product_list')->with(compact('productList'))->render();
    }

    public function postProductSearch(Request $request)
    {
        $productList = Product::where('status', 1)
            ->where('deleted', 0)
            ->where(function ($query) use ($request) {
                $query
                    ->where('code', 'LIKE', '%' . $request->product_info . '%')
                    ->orWhere('name', 'LIKE', '%' . $request->product_info . '%')
                    ->orWhereHas('brand', function ($query) use ($request) {
                        $query->where('name', 'LIKE', '%' . $request->product_info . '%');
                    });
            })
            ->get();

        $totalItem = $productList->count();
        $uniqProductId = 0;
        if ($totalItem == 1) {
            $uniqProductId = $productList[0]->id;
        }
        $list = view('adminPanel.pos._card_product_list')->with(compact('productList'))->render();
        $data = [$totalItem, $list, $uniqProductId];
        return $data;
    }

    public function sellItemGet(Request $request)
    {
        $productinfo = Product::find($request->product_id);
        $discount = 0;
        if ($productinfo->discount_type == 0) {
            $discount = $productinfo->discount;
        }
        if ($productinfo->discount_type == 1) {
            $discount = ($productinfo->discount * $productinfo->current_sale_price) / 100;
        }

        $productVariants = ProductVariant::where([
            ['status', '=', 1],
            ['product_id', '=', $request->product_id],
        ])->get();

        // if ($request->type != 'saleOrder' && $productinfo->available_quantity <= 0) {
        //     return response()->json([
        //         'error' => 'This Product is Out of Stock'
        //     ]);
        // }

        $brandList = Stock::with('brand')->where('product_id', $productinfo->id)->get();

        $data = compact('productinfo', 'discount', 'productVariants', 'brandList');

        return view('adminPanel.pos._pos_item_list', $data)->render();
    }

    // public function sellItemVariantGetToOrderList(Request $request)
    // {
    //     $productVariantInfo = ProductVariant::with('product', 'variant')->find($request->variant_id);

    //     $productInfo = $productVariantInfo->product;

    //     $discount = 0;
    //     if ($productInfo->discount_type == 0) {
    //         $discount = $productInfo->discount;
    //     }
    //     if ($productInfo->discount_type == 1) {
    //         $discount = ($productInfo->discount * $productVariantInfo->price / 100);
    //     }

    //     if ($productVariantInfo->variant_in_stock <= 0) {
    //         return response()->json([
    //             'error' => 'This Variant is Out of Stock'
    //         ]);
    //     }
    //     return view('adminPanel.pos._pos_item_variant_list')->with(compact('productVariantInfo', 'productInfo', 'discount'))->render();
    // }

    public function posCustomerStoreInPos(Request $request)
    {
        $pusCustomer = new PosCustomer();
        $pusCustomer->name = $request->name;
        $pusCustomer->phone = $request->phone;
        $pusCustomer->email = $request->email;
        $pusCustomer->area_id = $request->customer_area_id;
        $delivery_charges = Area::find($request->customer_area_id)->shipping_rate;

        $pusCustomer->delivery_charges = $delivery_charges;
        $pusCustomer->address = $request->address;
        $pusCustomer->save();
        $customer_id = $pusCustomer->id;
        $posCustomerList = PosCustomer::where('status', 1)->where('deleted', 0)->get();
        return view('adminPanel.pos._pos_customer_list')->with(compact('posCustomerList', 'customer_id'));
    }

    public function posPaymentStore(Request $request)
    {
        $arraySalesOrderItemIDs = $request->input('salesOrderItemIDs', []);
        $arrayItemIDs = $request->input('itemID', []);
        $arrayItemBrands = $request->input('itemBrand', []);
        $arrayItemSellVariants = $request->input('itemSellVariant', []);
        $arrayItemSellPrices = $request->input('itemSellPrice', []);
        $arrayItemQTYs = $request->input('itemQTY', []);

        $totalCost = $request->input('hiddenTotalCost', 0);
        $totalPayable = $request->input('hiddenTotalPayable', 0);
        $discountAmount = $request->input('hiddenDiscountAmount', 0);

        $shippingCost = $request->input('shippingCost', 0);
        $additionalCharges = $request->input('additionalCharges', 0);

        $customerID = $request->input('customer_id');
        $salesOrderID = $request->input('sales_order_id', null);

        $riderID = $request->input('rider_id', null);
        $paymentType = $request->input('paymentType', null);
        $paymentDate = $request->input('paymentDate', null);

        DB::beginTransaction();

        try {
            $sell = new Sell();
            $sell->sales_order_id = $salesOrderID;
            $sell->total_payable_amount = $totalPayable;
            $sell->total_discount = $discountAmount;
            $sell->total_paid = 0;
            $sell->total_due = $totalPayable;
            $sell->shipping_cost = $shippingCost;
            $sell->additional_charges = $additionalCharges;
            $sell->customer_id = $customerID;
            $sell->bank_id = $request->bank_id;
            $sell->sell_type = 1;
            $sell->sell_by = 1;
            $sell->order_status = OrderStatus::PENDING;
            $sell->rider_id = $riderID;
            $sell->pay_method = $paymentType;
            $sell->payment_date = $paymentDate;
            $sell->date = Carbon::now();
            $sell->created_at = Carbon::now();
            $sell->save();

            if (!is_null($riderID)) {
                $sell->rider_pay_status = 'unpaid';
            }

            $sell->invoice_id = 'POS-' . strtoupper(uniqid());
            $sell->save();

            $tData = [
                'transaction_id' => $sell->id,
                'customer_id' => $customerID,
                'transaction_type' => 1,
                'voucher_no' => $sell->invoice_id,
                'transaction_date' => Carbon::now(),
                'gross_amount' => $totalCost,
                'discount_amount' => $discountAmount,
                'payable_amount' => 0,
                'receiveable_amount' => $totalPayable,
                'receipt_amount' => 0,
                'paid_amount' => 0,
                'particular' => '-',
            ];

            DB::table('transaction')->insert($tData);

            foreach ($arrayItemIDs as $key => $itemID) {
                $product = Product::find($itemID);

                $itemSellVariant = $arrayItemSellVariants[$key];
                $itemBrand = $arrayItemBrands[$key];
                $itemSellPrice = $arrayItemSellPrices[$key];
                $itemQTY = $arrayItemQTYs[$key];

                $unitProductSell = $itemSellPrice;

                $soldItemQTY = $itemQTY;

                if ($itemSellVariant != 'custom') {
                    $productVariant = ProductVariant::find($itemSellVariant);
                    $productVariantPackSize = $productVariant->variant->pack_size;

                    $soldItemQTY = $itemQTY * $productVariantPackSize;

                    $unitProductSell = $itemSellPrice / $productVariantPackSize;
                }

                //Minus Product Stock
                $product->available_quantity -= $soldItemQTY;
                $product->save();

                $productSell = new Sell_details();
                $productSell->sell_id = $sell->id;
                $productSell->product_id = $itemID;
                $productSell->product_variant_id = $itemSellVariant;
                $productSell->total_discount = 0;
                $productSell->sale_quantity = $itemQTY;
                $productSell->brand_id = $itemBrand;
                $productSell->unit_product_cost = 0;
                $productSell->unit_sell_price = $unitProductSell;
                $productSell->total_payable_amount = $unitProductSell * $itemQTY;
                $productSell->save();

                if (isset($arraySalesOrderItemIDs[$key])) {
                    $salesOrderItem = SalesOrderItem::find($arraySalesOrderItemIDs[$key]);
                    $salesOrderItem->delivered_quantity += $itemQTY;
                    $salesOrderItem->save();
                    $salesOrderItem->order->balance += $totalPayable;
                    $salesOrderItem->order->save();
                }

                // --- Stock Handling --- //also can be negative
                $stock = Stock::firstOrNew([
                    'product_id' => $itemID,
                    'brand_id' => $itemBrand,
                ]);

                if ($stock->exists) {
                    $stock->qty -= $itemQTY;
                } else {
                    $stock->qty = -$itemQTY;
                }

                $stock->save();

                // --- Stock Log ---
                StockLog::create([
                    'type' => StockLog::TYPE_OUT,
                    'stock_id' => $stock->id,
                    'party_type' => PosCustomer::class,
                    'party_id' => $customerID,
                    'product_id' => $itemID,
                    'brand_id' => $itemBrand,
                    'price' => $unitProductSell,
                    'qty' => $itemQTY,
                ]);
            }
            DB::commit();
            return redirect()->back()->with('success', 'Successfully Payment Completed');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function sellList(Request $request)
    {
        $from_date = $request->query('from_date');
        $to_date = $request->query('to_date');
        $riderID = $request->query('byRider');

        $common_data = new Array_();
        $common_data->title = 'Sell List';

        $sellListQuery = Sell::where('sell_type', 1);

        if (!is_null($from_date)) {
            $sellListQuery->whereDate('created_at', '>=', $from_date);
        }

        if (!is_null($to_date)) {
            $sellListQuery->whereDate('created_at', '<=', $to_date);
        }
        if (!is_null($riderID)) {
            $sellListQuery->where('rider_id', '=', $riderID);
        }

        $sellList = $sellListQuery->orderByDesc('id')->get();

        $riders = Rider::all();

        $data = compact('sellList', 'common_data', 'riders', 'from_date', 'to_date');

        return view('adminPanel.pos.pos_sell')->with($data);
    }

    public function admin_receipt_list()
    {
        $common_data = new Array_();
        $common_data->title = 'Receipt Voucher List';
        return view('adminPanel.pos.receipt_list')->with(compact('common_data'));
    }

    public function sellInvoice(Request $request)
    {
        return $this->printOrDownload([$request->id], 'I');
        // $selldata = Sell::with('customer')->with('sellDetail')->find($request->id);

        // $data = [
        //     'sell' => $selldata
        // ];

        // $fileName = $selldata->customer->name ?? "000" . '_buy_invoice.pdf';

        // $pdf = PDF::loadView('adminPanel.pos.sell_invoice', $data);
        // //      return view('adminPanel.pos.sell_invoice');
        // //  return $pdf->download($fileName);
        // return $pdf->stream('buy_invoice.pdf');
    }

    public function getProductVariants(Request $request)
    {
        $action = $request->input('action', null);

        $product = Product::with('productVariants.variant')->where('id', $request->id)->first();
        $html = view('adminPanel.pos._get_product_variants', compact('product', 'action'))->render();
        return response()->json(['html' => $html]);
    }

    public function posCustomerActive($id)
    {
        // Find the ExpenseAccount instance by ID
        $posCustomer = PosCustomer::findOrFail($id);

        // Directly set the status field manually
        $posCustomer->status = 1; // Active
        $posCustomer->save();

        // Redirect back with a success message
        return redirect()->route('admin.pos.customer.list')->with('success', 'POS Customer activated successfully!');
    }

    public function posCustomerInactive($id)
    {
        // Find the ExpenseAccount instance by ID
        $posCustomer = PosCustomer::findOrFail($id);

        // Directly set the status field manually
        $posCustomer->status = 0; // Inactive
        $posCustomer->save();

        // Redirect back with a success message
        return redirect()->route('admin.pos.customer.list')->with('success', 'POS Customer deactivated successfully!');
    }

    public function sellsMultiAction(Request $request)
    {
        $sellsAction = $request->input('sellsAction');

        $sellIDs = $request->input('sellIDs', []);

        $riderID = $request->input('riderID');
        $paymentType = $request->input('paymentType');

        $payStatus = $request->input('payStatus');

        if (empty($sellIDs)) {
            return redirect()->back()->with('error', 'No items selected.');
        }

        switch ($sellsAction) {
            case 'downloadPDF':
                return $this->printOrDownload($sellIDs, 'D');

            case 'print':
                return $this->printOrDownload($sellIDs, 'I');

            case 'allocateRiderAndPayType':
                return $this->allocateRiderAndPayType($sellIDs, $riderID, $paymentType);

            case 'riderPayStatus':
                return $this->sellPayStatusUpdate($sellIDs, $payStatus);

            default:
                return redirect()->back()->with('error', 'Invalid action.');
        }
    }

    public function printOrDownload($sellIDs, $action)
    {
        $sells = Sell::with(['sellDetail', 'customer'])
            ->whereIn('id', $sellIDs)
            ->get();

        $pdf = new Fpdi();
        $counter = 1;

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

        return $pdf->Output('Sale.' . now() . '.pdf', $action);
    }

    public function print($sellIDs)
    {
        $orders = Sell::with(['sellDetail', 'customer'])
            ->whereIn('id', $sellIDs)
            ->get();

        $pdf = new Fpdi(); // Use FPDI to combine PDFs
        $counter = 1;

        foreach ($orders as $index => $oRow) {
            // Generate the PDF content for each order
            $orderPdf = Pdf::loadView('adminPanel.pos.sell_invoice', ['sell' => $oRow]);

            // Save the PDF to a temporary file
            $tempFile = storage_path("app/temp_order_{$index}.pdf");
            $orderPdf->save($tempFile);

            // Add the generated PDF to the final combined PDF
            $pdf->setSourceFile($tempFile);
            $pageCount = $pdf->setSourceFile($tempFile); // Get the number of pages in the file
            for ($i = 1; $i <= $pageCount; $i++) {
                $tplId = $pdf->importPage($i);
                $pdf->addPage();
                $pdf->useTemplate($tplId);
            }

            // Optionally delete the temporary file
            unlink($tempFile);
            $counter++;
        }

        // Output the combined PDF as a single file
        return $pdf->Output('pos_order_pdfs.pdf', 'I');
    }

    private function allocateRiderAndPayType($sellIDs, $riderID, $paymentType)
    {
        $updateData = [];

        if (!is_null($riderID)) {
            $updateData['rider_id'] = $riderID;
        }

        if (!is_null($paymentType)) {
            $updateData['pay_method'] = $paymentType;
        }

        if (!empty($updateData)) {
            Sell::whereIn('id', $sellIDs)->update($updateData);
        }

        return redirect()->back()->with('success', 'Selected rider and payment method successfully applied');
    }

    private function sellPayStatusUpdate($sellIDs, $payStatus)
    {
        foreach ($sellIDs as $sellID) {
            try {
                DB::transaction(function () use ($sellID, $payStatus) {
                    $sell = Sell::find($sellID);

                    if (!$sell) {
                        throw new \Exception('Order not found for ID: ' . $sellID);
                    }

                    if ($payStatus) {
                        $sell->rider_pay_status = $payStatus;
                    }
                    $sell->save();
                });
            } catch (\Exception $e) {
                Log::error('Transaction failed for Order ID: ' . $sellID . ' - ' . $e->getMessage());
                continue;
            }
        }

        return redirect()->back()->with('success', 'Successfully Pay Status Updated.');
    }

    public function salesOrders()
    {
        $common_data = new Array_();
        $common_data->title = 'Sales Orders List';
        $salesOrders = SalesOrder::with('items.product')->orderBy('id', 'desc')->paginate(15);

        $data = compact('common_data', 'salesOrders');
        return view('adminPanel.pos.sales_orders', $data);
    }

    public function createSalesOrder()
    {
        $common_data = new Array_();
        $common_data->title = 'Sale Order';
        $productList = Product::where('status', 1)->where('deleted', 0)->paginate(12);
        $posCustomerList = PosCustomer::where('status', 1)->where('deleted', 0)->get();
        $bankList = BankAccount::where('status', 1)->where('deleted', 0)->get();

        $areaList = Area::all();
        $riders = Rider::all();

        return view('adminPanel.pos.create_sales_order')->with(
            compact('posCustomerList', 'bankList', 'common_data', 'areaList', 'riders'),
        );
    }

    public function storeSalesOrder(Request $request)
    {
        $order = SalesOrder::create([
            'sales_order_number' => 'SO-' . strtoupper(uniqid()),
            'customer_id' => $request->customer_id,
            'bank_id' => $request->bank_id,
            'total_cost' => $request->hiddenTotalCost,
        ]);

        foreach ($request->itemID as $index => $product_id) {
            $productVariantID =
                $request->itemSellVariant[$index] == 'custom' ? null : $request->itemSellVariant[$index];

            SalesOrderItem::create([
                'sales_order_id' => $order->id,
                'product_id' => $product_id,
                'product_variant_id' => $productVariantID,
                'brand_id' => $request->itemBrand[$index],
                'sell_price' => $request->itemSellPrice[$index],
                'quantity' => $request->itemQTY[$index],
            ]);
        }

        return redirect()->back()->with('success', 'Sales Order Created Successfully');
    }

    public function getSalesOrderInfo(Request $request)
    {
        $id = $request->input('sales_order_id');
        $common_data = new Array_();
        $common_data->title = 'Sales Order Details';
        $salesOrder = SalesOrder::with(['items.product', 'items.itemVariant', 'items.itemVariant.variant'])->find($id);

        return view('adminPanel.pos.get_in_pos_sales_order_items')->with(compact('salesOrder'))->render();
    }

    public function getCustomerInfoHTML(Request $request)
    {
        $customer_id = $request->input('customer_id');

        $customer = PosCustomer::with('area')->find($customer_id);

        return view('adminPanel.pos._getCustomerInfoHTML', compact('customer'))->render();
    }
}