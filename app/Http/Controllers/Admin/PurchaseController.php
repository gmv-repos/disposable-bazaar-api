<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\PosCustomer;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Purchase_product_list;
use App\Models\PurchaseDetails;
use App\Models\PurchaseProductList;
use App\Models\Supplier;
use PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\Array_;
use Illuminate\Support\Facades\DB;

use App\Models\PurchaseReceive;
use App\Models\PurchaseReceiveItem;
use Illuminate\Support\Facades\Log;
use App\Models\Stock;
use App\Models\StockLog;

class PurchaseController extends Controller
{
    public function purchaseProductView()
    {
        $common_data = new Array_();
        $common_data->title = 'Product Purchase';
        $productList = Product::where('status', 1)->where('deleted', 0)->paginate(12);
        $supplierList = Supplier::where('status', 1)->where('deleted', 0)->get();
        $bankList = BankAccount::where('status', 1)->where('deleted', 0)->get();

        return view('adminPanel.product_stock.purchase_product')->with(
            compact('supplierList', 'bankList', 'common_data'),
        );
    }

    public function purchaseList()
    {
        $common_data = new Array_();
        $common_data->title = 'Product Purchase';
        $purchaseList = PurchaseProductList::orderBy('date', 'desc')->get();
        return view('adminPanel.product_stock.purchase_list')->with(compact('purchaseList'));
    }

    public function purchasePaymentStore(Request $request)
    {
        DB::beginTransaction();
        try {
            $purchaseType = $request->input('purchaseType', 'PO');

            $arrayItemIDs = $request->input('itemID', []);
            $arrayItemPurchaseVariants = $request->input('itemPurchaseVariant', []);
            $arrayBrandId = $request->input('brandListId', []);
            $arrayItemCostPrices = $request->input('itemCostPrice', []);
            $arrayItemQTYs = $request->input('itemQTY', []);

            $totalCost = $request->input('hiddenTotalCost', 0);
            $totalPayable = $request->input('hiddenTotalPayable', 0);
            $discountAmount = $request->input('hiddenDiscountAmount', 0);
            $deliveryCharges = $request->input('deliveryCharges', 0);

            $supplierID = $request->input('supplier_id');

            $newPurchase = new PurchaseProductList();
            $newPurchase->fill(
                attributes: [
                    'total_cost' => $totalCost,
                    'total_payable_amount' => $totalPayable,
                    'total_paid' => 0,
                    'total_vat' => 0,
                    'total_discount' => $discountAmount,
                    'delivery_charges' => $deliveryCharges,
                    'total_due' => $totalPayable,
                    'supplier_id' => $supplierID,
                    'date' => now(),
                ],
            );

            $newPurchase->save();

            $newPurchase->purchase_code =
                'PUR-' . now()->format('Ymd') . '-' . str_pad($newPurchase->id, 4, '0', STR_PAD_LEFT);
            $newPurchase->save();

            // Insert transaction
            DB::table('transaction')->insert([
                'transaction_id' => $newPurchase->id,
                'supplier_id' => $supplierID,
                'transaction_type' => 3,
                'voucher_no' => $newPurchase->purchase_code,
                'transaction_date' => now()->format('Y-m-d'),
                'gross_amount' => $totalCost,
                'discount_amount' => $discountAmount,
                'payable_amount' => $totalPayable,
                'receiveable_amount' => 0,
                'receipt_amount' => 0,
                'paid_amount' => 0,
                'particular' => '-',
            ]);

            foreach ($arrayItemIDs as $key => $itemID) {
                $product = Product::find($itemID);

                // $itemPurchaseVariant = $arrayItemPurchaseVariants[$key];
                $itemCostPrice = $arrayItemCostPrices[$key];
                $itemQTY = $arrayItemQTYs[$key];
                $brandId = $arrayBrandId[$key];

                // if ($itemPurchaseVariant == 'custom') {

                //     $product->current_purchase_cost = $itemCostPrice;
                //     $product->available_quantity += $itemQTY;
                // }
                // else{
                //     $productVariant = ProductVariant::find($itemPurchaseVariant);
                //     $productVariantPackSize = $productVariant->variant->pack_size;

                //     $perPeicePrice = $itemCostPrice / $productVariantPackSize;
                //     $productVariant->price = $itemCostPrice;
                //     $productVariant->price_per_peice = $perPeicePrice;

                //     $productVariant->save();

                //     $product->current_purchase_cost = $perPeicePrice;
                //     $product->available_quantity += $itemQTY * $productVariantPackSize;

                // }

                // $product->save();

                $payable = $itemCostPrice * $itemQTY;

                $purchaseProduct = new PurchaseDetails([
                    'purchase_id' => $newPurchase->id,
                    'product_id' => $itemID,
                    'brand_id' => $brandId,
                    // 'product_variant_id' => ($itemPurchaseVariant == 'custom') ? null : $itemPurchaseVariant,
                    'purchase_payable_amount' => $payable,
                    'unit_cost' => $itemCostPrice,
                    'total_qty' => $itemQTY,
                    'total_cost' => $payable,
                    'total_vat' => 0,
                    'total_discount' => 0,
                    'date' => now(),
                ]);
                $purchaseProduct->save();
            }

            if ($purchaseType == 'GRN') {
                $this->createGRN(
                    $newPurchase->id,
                    $supplierID,
                    $arrayItemIDs,
                    $arrayBrandId,
                    // $arrayItemPurchaseVariants,
                    $arrayItemCostPrices,
                    $arrayItemQTYs,
                    $totalPayable,
                );
            }

            DB::commit();
            return redirect()->back()->with('success', 'Successfully Product Purchased');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'exception' => $e,
            ]);
            return redirect()
                ->back()
                ->with('error', 'Purchase failed: ' . $e->getMessage());
        }
    }

    public function purchaseInvoice(Request $request)
    {
        //        return $request->id;
        $purchaseInfo = PurchaseProductList::with('purchaseInfo')->where('id', $request->id)->first();
        $purchaseDetails = PurchaseDetails::with('productInfo', 'brand')->where('purchase_id', $request->id)->get();

        //        return $purchaseInfo->purchaseDetails;

        //        supplierInfo
        //purchaseDetails
        //ProductInfo

        $data = [
            'purchase' => $purchaseInfo,
            'purchaseDetails' => $purchaseDetails,
        ];

        $pdf = PDF::loadView('adminPanel.product_stock.purchase_invoice', $data);
        //      return view('adminPanel.pos.sell_invoice');
        //      return $pdf->download('buy_invoice.pdf');
        return $pdf->stream('Purchase_invoice.pdf');
    }

    public function admin_payment_list()
    {
        $common_data = new Array_();
        $common_data->title = 'Payment Voucher List';
        return view('adminPanel.product_stock.payment_list')->with(compact('common_data'));
    }

    public function createGRN(
        $purchaseID,
        $supplierID,
        $itemIDs,
        $brandIds,
        // $itemPurchaseVariants,
        $itemCostPrices,
        $itemQTYs,
        $subTotal,
    ) {
        $purchaseReceive = new PurchaseReceive();

        $purchaseReceive->purchase_id = $purchaseID;
        $purchaseReceive->supplier_id = $supplierID;
        $purchaseReceive->payable_amount = $subTotal;
        $purchaseReceive->paid_amount = 0;
        $purchaseReceive->due_amount = $subTotal;
        $purchaseReceive->save();

        $purchaseReceive->pr_code =
            'PR-' . now()->format('Ymd') . '-' . str_pad($purchaseReceive->id, 4, '0', STR_PAD_LEFT);
        $purchaseReceive->save();

        foreach ($itemIDs as $key => $itemID) {
            $purchaseReceiveItem = new PurchaseReceiveItem();
            $purchaseReceiveItem->pr_id = $purchaseReceive->id;
            $purchaseReceiveItem->purchase_id = $purchaseID;
            $purchaseReceiveItem->product_id = $itemID;
            $purchaseReceiveItem->product_variant_id = null;
            $purchaseReceiveItem->cost_amount = $itemCostPrices[$key];
            $purchaseReceiveItem->brand_id = $brandIds[$key];
            $purchaseReceiveItem->total_qty = $itemQTYs[$key];
            $purchaseReceiveItem->received_qty = $itemQTYs[$key];
            $purchaseReceiveItem->total_cost_amount = $itemQTYs[$key] * $itemCostPrices[$key];

            $productNewQTY = $itemQTYs[$key];

            // $itemPurchaseVariant = $itemPurchaseVariants[$key] ?? 'custom';

            // if ($itemPurchaseVariant != 'custom') {

            //     $purchaseReceiveItem->product_variant_id = $itemPurchaseVariant;
            //     $productVariant = ProductVariant::find($itemPurchaseVariant);
            //     $productVariantPackSize = $productVariant->variant->pack_size;
            //     $productNewQTY = $productVariantPackSize * $itemQTYs[$key];
            // }

            $purchaseReceiveItem->save();

            $product = Product::find($itemID);
            $product->available_quantity += $productNewQTY;
            $product->save();

            // --- Stock Handling ---
            $stock = Stock::firstOrNew([
                'product_id' => $itemID,
                'brand_id' => $brandIds[$key],
            ]);

            $stock->qty = ($stock->qty ?? 0) + $productNewQTY;
            $stock->brand_id = $brandIds[$key];
            $stock->save();

            // --- Stock Log ---
            StockLog::create([
                'type' => StockLog::TYPE_IN,
                'stock_id' => $stock->id,
                'party_type' => Supplier::class,
                'party_id' => $supplierID,
                'product_id' => $itemID,
                'brand_id' => $brandIds[$key],
                'price' => $itemCostPrices[$key],
                'qty' => $productNewQTY,
            ]);
        }
    }
}