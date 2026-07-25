<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use PhpParser\Node\Expr\Array_;

use App\Models\Product;
use App\Models\PosCustomer;
use App\Models\Quotation;
use App\Models\QuotationItem;

use App\Models\Sell;
use App\Models\Sell_details;
use Barryvdh\DomPDF\Facade\Pdf;

class QuotationController extends Controller
{
    public function index()
    {
        $quotations = Quotation::all();
        return view('adminPanel.quotations.index', compact('quotations'));
    }

    public function create()
    {
        return view('adminPanel.quotations.create');
    }

    public function store(Request $request)
    {
        if (is_null($request->input('hiddenProductIDs'))) {
            return redirect()->back()->with('error', 'Please select at least one product.');
        }

        $quotationDate = $request->input('quotation_date');
        $customerName = $request->input('customer_name');
        $companyName = $request->input('company_name');
        $validUntil = $request->input('valid_until', null);
        $note = $request->input('note', null);

        $productIDs = $request->input('hiddenProductIDs');
        $productPrices = $request->input('hiddenProductPrices');
        $productQTYs = $request->input('hiddenProductQTYs');
        $productDiscounts = $request->input('hiddenProductDiscounts');
        $taxAmount = $request->input('taxAmount', 0);
        $discountType = $request->input('discountType', 0);
        $discountInput = $request->input('discountInput', 0);
        $productVariantIds = $request->input('variantSelect', []);
        $brandIds = $request->input('brandSelect', []);

        DB::beginTransaction();

        try {
            $quotationTotal = 0;

            $quotationItemsIDs = [];
            foreach ($productIDs as $key => $value) {
                $itemPrice = $productPrices[$key];
                $itemQTY = $productQTYs[$key];
                $itemDiscount = $productDiscounts[$key];

                $product = Product::findOrFail($value);

                $itemTotalPrice = $itemPrice * $itemQTY - $itemDiscount;

                $quotationItem = new QuotationItem();

                $quotationItem->product_id = $product->id;
                $quotationItem->quantity = $itemQTY;
                $quotationItem->price = $itemPrice;
                $quotationItem->discount = $itemDiscount;
                $quotationItem->total = $itemTotalPrice;
                $quotationItem->product_variant_id = $productVariantIds[$key];
                $quotationItem->brand_id = $brandIds[$key];

                $quotationItem->save();

                $quotationItemsIDs[] = $quotationItem->id;

                $quotationTotal += $itemTotalPrice;
            }

            $referenceCode = 'QUO-' . now()->format('Ymd-his');
            $discountOnQuotationInAmount = $discountInput;
            if ($discountType == 1) {
                $discountOnQuotationInAmount = ($quotationTotal * $discountInput) / 100;
            }

            $quotation = Quotation::create([
                'quotation_date' => $quotationDate,
                'reference_code' => $referenceCode,
                'customer_name' => $customerName,
                'company_name' => $companyName,
                'total' => $quotationTotal,
                'discount' => $discountOnQuotationInAmount ?? 0,
                'delivery_charges' => 0,
                'tax' => $taxAmount,
                'payable_amount' => $quotationTotal - $discountOnQuotationInAmount + $taxAmount,
                'notes' => $note,
                'valid_until' => $validUntil,
            ]);

            QuotationItem::whereIn('id', $quotationItemsIDs)->update(['quotation_id' => $quotation->id]);

            DB::commit();
            return redirect()->route('quotations.index')->with('success', 'Quotation created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Quotation creation failed: ' . $e->getMessage());

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $customers = PosCustomer::all();

        $quotation = Quotation::with(
            'quotationItems',
            'quotationItems.productVariant.variant',
            'quotationItems.brand',
        )->findOrFail($id);

        $brands = DB::table('brands')->where('status', 1)->get();
        return view('adminPanel.quotations.edit', compact('quotation', 'customers', 'brands'));
    }

    public function update(Request $request, $id)
    {
        if (is_null($request->input('hiddenProductIDs'))) {
            return redirect()->back()->with('error', 'Please select at least one product.');
        }

        $customerName = $request->input('customer_name');
        $companyName = $request->input('company_name');

        $validUntil = $request->input('valid_until', null);
        $note = $request->input('note', null);

        $productIDs = $request->input('hiddenProductIDs');
        $productPrices = $request->input('hiddenProductPrices');
        $productQTYs = $request->input('hiddenProductQTYs');
        $productDiscounts = $request->input('hiddenProductDiscounts');
        $productVariantIds = $request->product_variant_ids;
        $brandIds = $request->brand_ids;

        $taxAmount = $request->input('taxAmount', 0);

        $discountType = $request->input('discountType', 0);
        $discountInput = $request->input('discountInput', 0);

        DB::beginTransaction();

        try {
            $quotation = Quotation::findOrFail($id);

            $quotationTotal = 0;

            $quotation->quotationItems()->delete();

            foreach ($productIDs as $key => $productID) {
                $itemPrice = $productPrices[$key];
                $itemQTY = $productQTYs[$key];
                $itemDiscount = $productDiscounts[$key];

                $product = Product::findOrFail($productID);

                $itemTotalPrice = $itemPrice * $itemQTY - $itemDiscount;

                $quotationItem = new QuotationItem();
                $quotationItem->quotation_id = $quotation->id;
                $quotationItem->product_id = $product->id;
                $quotationItem->quantity = $itemQTY;
                $quotationItem->price = $itemPrice;
                $quotationItem->discount = $itemDiscount;
                $quotationItem->total = $itemTotalPrice;
                $quotationItem->product_variant_id = $productVariantIds[$key];
                $quotationItem->brand_id = $brandIds[$key];
                $quotationItem->save();

                $quotationTotal += $itemTotalPrice;
            }

            $discountOnQuotationInAmount = $discountInput;
            if ($discountType == 1) {
                $discountOnQuotationInAmount = ($quotationTotal * $discountInput) / 100;
            }

            $quotation->update([
                'customer_name' => $customerName,
                'company_name' => $companyName,
                'total' => $quotationTotal,
                'discount' => $discountOnQuotationInAmount,
                'delivery_charges' => 0,
                'tax' => $taxAmount,
                'payable_amount' => $quotationTotal - $discountOnQuotationInAmount + $taxAmount,
                'notes' => $note,
                'valid_until' => $validUntil,
            ]);

            DB::commit();

            return redirect()->route('quotations.index')->with('success', 'Quotation updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Quotation update failed: ' . $e->getMessage());

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $quotation = Quotation::findOrFail($id);

        $quotation->delete();

        return redirect()->route('quotations.index')->with('success', 'Quotation deleted successfully!');
    }

    public function show($id)
    {
        $quotation = Quotation::with('quotationItems.brand', 'quotationItems.productVariant.variant')->findOrFail($id);
        return view('adminPanel.quotations.show', compact('quotation'));
    }

    //Create Sell

    public function createSell(Request $request, $id)
    {
        $quotationID = $id;

        $discountAmount = $request->discountAmount;
        DB::beginTransaction();
        try {
            $sell = new Sell();
            $sell->total_payable_amount = $request->total_payable;
            $sell->total_discount = $discountAmount;
            $sell->total_paid = 0;
            $sell->total_due = $request->total_payable;
            $sell->customer_id = 1;
            $sell->bank_id = $request->bank_id;
            $sell->sell_type = 1;
            $sell->sell_by = 1;
            $sell->date = Carbon::now();
            $sell->created_at = Carbon::now();
            $sell->save();

            $sell->invoice_id = 'POS-' . strtoupper(uniqid());
            $sell->save();

            $tData = [
                'transaction_id' => $sell->id,
                'customer_id' => 1,
                'transaction_type' => 1,
                'voucher_no' => $sell->invoice_id,
                'transaction_date' => date('Y-m-d'),
                'gross_amount' => $request->total_payable + $discountAmount,
                'discount_amount' => $discountAmount,
                'payable_amount' => 0,
                'receiveable_amount' => $request->total_payable,
                'receipt_amount' => 0,
                'paid_amount' => 0,
                'particular' => '-',
            ];
            DB::table('transaction')->insert($tData);

            foreach ($request->product_id as $key => $product) {
                $unitPyable = $request->product_variant_price[$key] - $request->product_discount[$key];
                $productSell = new Sell_details();
                $productSell->sell_id = $sell->id;
                $productSell->product_id = $request->product_id[$key];
                $productSell->total_discount = $request->product_discount[$key];
                $productSell->sale_quantity = $request->sell_qty[$key];
                $productSell->unit_product_cost = $request->product_cost[$key];
                $productSell->product_variant_id = $request->product_variant_id[$key];
                $productSell->brand_id = $request->brand_id[$key];
                $productSell->unit_sell_price = $request->product_variant_price[$key];
                $productSell->total_payable_amount = $unitPyable * $request->sell_qty[$key];
                $productSell->save();
            }

            foreach ($request->product_id as $key => $product_id) {
                $product = Product::where('id', $product_id)->first();
                $availableProduct = $product->available_quantity;
                $qty = $request->sell_qty[$key];
                $total_qty = $availableProduct - $qty;
                Product::where('id', $product_id)->update(['available_quantity' => $total_qty]);
            }

            Quotation::where('id', $quotationID)->update(['status' => 'Accepted']);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        return redirect()->route('quotations.index')->with('success', 'Successfully Sell Created');
    }

    public function quotationInvoice($id)
    {
        $quotation = Quotation::with('quotationItems.brand', 'quotationItems.productVariant.variant')->findOrFail($id);
        $data = [
            'quotation' => $quotation,
        ];

        $pdf = Pdf::loadView('adminPanel.quotations.invoice', $data);
        //      return view('adminPanel.pos.sell_invoice');
        //  return $pdf->download($fileName);
        return $pdf->stream('quotation_invoice.pdf');
    }

    public function addProductToQuotationList(Request $request)
    {
        $id = $request->id;

        $product = Product::with(['brand', 'productVariants.variant', 'productVariants'])->find($id);
        $brandList = DB::table('brands')->where('status', 1)->get();

        // Get the first productVariant (as default)
        $defaultVariant = $product->productVariants->first();
        $defaultPrice = $defaultVariant ? $defaultVariant->price : 1;
        $defaultQty = 1;

        $productRow =
            '

            <tr class="bg-light text-dark rounded" productID="' .
            $product->id .
            '">

                <input type="hidden" name="hiddenProductIDs[]" value="' .
            $product->id .
            '">
                <input type="hidden" name="hiddenProductPrices[]" value="' .
            $defaultPrice .
            '">
                <input type="hidden" name="hiddenProductQTYs[]" value="' .
            $defaultQty .
            '">
                <input type="hidden" name="hiddenProductDiscounts[]" value="0">
                <td>
                    <img class="pimgst" src="' .
            asset($product->image_path ?: 'assets/adminPanel/images/dummy.png') .
            '" alt="' .
            $product->name .
            '">
                </td>
                <td>' .
            $product->name .
            '</td>
                <td>
                    <select class="form-select form-select-sm w-100" name="brandSelect[]">
                        <option value="">Select Brand</option>
                        ' .
            $brandList
                ->map(function ($brand) use ($product) {
                    return '<option value="' .
                        $brand->id .
                        '" ' .
                        ($product->brand_id == $brand->id ? 'selected' : '') .
                        '>' .
                        $brand->name .
                        '</option>';
                })
                ->implode('') .
            '
                    </select>
                </td>
               <td>
                    <select class="form-select form-select-sm w-100 variantSelect"              
                     name="variantSelect[]" onchange="onVariantChange(this)">
                        <option value="" data-price="0">Custom</option>
                        ' .
            $product->productVariants
                ->map(function ($pv) {
                    return '<option value="' .
                        $pv->id .
                        '" data-price="' .
                        $pv->price .
                        '">' .
                        $pv->variant->pack_size .
                        '</option>';
                })
                ->implode('') .
            '
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm w-100 itemPrice" name="itemPrice[]" value="0" step="any" min="1" oninput="calculateTotal()">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm w-100 itemQtyInput" value="1" min="1" oninput="calculateTotal()">
                </td>
                <td class="itemTotalPrice">0</td>
                <td>
                    <i class="lni lni-trash" style="cursor: pointer;" onclick="removeItem(this)"></i>
                </td>
            </tr>';

        return response()->json([
            'productRow' => $productRow,
        ]);
    }
}
