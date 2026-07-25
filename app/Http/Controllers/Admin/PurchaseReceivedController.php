<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDF;

use App\Models\PurchaseProductList;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Supplier;

use App\Models\PurchaseReceive;
use App\Models\PurchaseReceiveItem;
use App\Models\PurchaseDetails;
use App\Models\Stock;
use App\Models\StockLog;

class PurchaseReceivedController extends Controller
{
    public function index()
    {
        $purchaseReceive = PurchaseReceive::orderByDesc('id')->get();
        return view('adminPanel.purchase_received.index', compact('purchaseReceive'));
    }

    public function create()
    {
        $purchases = PurchaseProductList::whereHas('purchaseDetails', function ($query) {
            $query->whereColumn('total_qty', '>', 'received_qty');
        })
            ->orderBy('id', 'desc')
            ->get();

        return view('adminPanel.purchase_received.create', compact('purchases'));
    }

    public function store(Request $request)
    {
        $purchaseID = $request->input('purchaseID');
        $supplierID = $request->input('supplierID');
        $itemIDs = $request->input('itemID', []);
        $listIDs = $request->input('listID', []);
        $brandIDs = $request->input('brandID', []);
        $itemPurchaseVariants = $request->input('itemPurchaseVariant', []);
        $itemCostPrices = $request->input('itemCostPrice', []);
        $itemQTYs = $request->input('itemQTY', []);
        $subTotal = $request->input('hiddenSubTotal', 0);

        DB::beginTransaction();

        try {
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
                $purchaseReceiveItem->total_qty = $itemQTYs[$key];
                $purchaseReceiveItem->total_cost_amount = $itemQTYs[$key] * $itemCostPrices[$key];

                $productNewQTY = $itemQTYs[$key];

                $itemPurchaseVariant = $itemPurchaseVariants[$key] ?? 'custom';

                if ($itemPurchaseVariant != 'custom') {
                    $purchaseReceiveItem->product_variant_id = $itemPurchaseVariant;
                    $productVariant = ProductVariant::find($itemPurchaseVariant);
                    $productVariantPackSize = $productVariant->variant->pack_size;
                    $productNewQTY = $productVariantPackSize * $itemQTYs[$key];
                }

                $purchaseReceiveItem->save();

                $product = Product::find($itemID);
                $product->available_quantity += $productNewQTY;
                $product->save();

                PurchaseDetails::find($listIDs[$key])
                    ->increment('received_qty', $productNewQTY);

                // --- Stock Handling ---
                $stock = Stock::firstOrNew([
                    'product_id' => $itemID,
                    'brand_id' => $brandIDs[$key],
                ]);

                $stock->qty = ($stock->qty ?? 0) + $productNewQTY;
                $stock->brand_id = $brandIDs[$key];
                $stock->save();

                // --- Stock Log ---
                StockLog::create([
                    'type' => StockLog::TYPE_IN,
                    'stock_id' => $stock->id,
                    'party_type' => Supplier::class,
                    'party_id' => $supplierID,
                    'product_id' => $itemID,
                    'brand_id' => $brandIDs[$key],
                    'price' => $itemCostPrices[$key],
                    'qty' => $productNewQTY,
                ]);
            }

            DB::commit();
            return redirect()
                ->back()
                ->with(['success' => 'Purchase Received Saved Successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Purchase Received failed: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with(['error' => 'Failed to save data. Please try again.']);
        }
    }

    public function pdfDownload($id)
    {
        $purchaseReceive = PurchaseReceive::find($id);

        $purchaseReceiveItems = $purchaseReceive->prItems;

        $data = compact('purchaseReceive', 'purchaseReceiveItems');

        // return view('adminPanel.purchase_received.purchase_receive_pdf', $data);
        $pdf = PDF::loadView('adminPanel.purchase_received.purchase_receive_pdf', $data);

        return $pdf->download($purchaseReceive->pr_code . '.pdf');
    }

    public function loadPurchaseOrderDetails(Request $request)
    {
        $purchase_id = $request->input('purchase_id');

        $data = PurchaseProductList::with('purchaseDetails')->find($purchase_id);
        $purchaseReceives = PurchaseReceive::where('purchase_id', $purchase_id)->get();
        $purchaseReceiveItems = PurchaseReceiveItem::where('purchase_id', $purchase_id)->get();

        if (is_null($data)) {
            return '';
        }

        return view('adminPanel.purchase_received.loadPurchaseOrderDetails', [
            'purchase' => $data,
            'supplierInfo' => $data->supplierInfo,
            'purchaseDetails' => $data->purchaseDetails,
            'purchaseReceives' => $purchaseReceives,
            'purchaseReceiveItems' => $purchaseReceiveItems,
        ])->render();
    }
}