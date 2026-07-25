<?php

use App\Models\Offer_product_list;
use App\Models\Product;
use App\Models\Sell;
use App\Models\ReceiptVoucher;
use App\Models\PaymentVoucher;
use App\Models\PurchaseProductList;
use Carbon\Carbon;
use App\Enums\OrderStatus;

use Illuminate\Support\Facades\Auth;

// require_once __DIR__ . './countryList.php';

function getUploadPath()
{
    return public_path('storage');
}

function userCanAccess($access_id)
{
    $access_list = Auth::guard('admin')->user()->role->access_role_list;
    $accessListArray = explode(',', $access_list);

    return $result = in_array($access_id, $accessListArray);
}

function calculateCustomerCurrentBalance($id)
{
    $totalSellAmount = Sell::where('customer_id', $id)->sum('total_payable_amount');
    $totalReceiptAmount = ReceiptVoucher::where('customer_id', $id)->groupBy('customer_id')->sum('amount');
    $data['totalSellAmount'] = $totalSellAmount;
    $data['totalReceiptAmount'] = $totalReceiptAmount;
    return $data;
}

function calculateSupplierCurrentBalance($id)
{
    $totalPurchaseAmount = PurchaseProductList::where('supplier_id', $id)->sum('total_payable_amount');
    $totalPaymentAmount = PaymentVoucher::where('supplier_id', $id)->groupBy('supplier_id')->sum('amount');
    $data['totalPurchaseAmount'] = $totalPurchaseAmount;
    $data['totalPaymentAmount'] = $totalPaymentAmount;
    return $data;
}

function calculatePartyCurrentBalance($id)
{
    $totalSellAmount = Sell::where('customer_id', $id)->sum('total_payable_amount');

    $totalPurchaseAmount = PurchaseProductList::where('supplier_id', $id)->sum('total_payable_amount');

    $totalReceiptAmount = ReceiptVoucher::where('customer_id', $id)->sum('amount');

    $totalPaymentAmount = PaymentVoucher::where('supplier_id', $id)->sum('amount');

    $netBalance = $totalPurchaseAmount - $totalPaymentAmount - ($totalSellAmount - $totalReceiptAmount);

    $data['balance'] = $netBalance;

    return $data;
}

function calculateDiscount($item)
{
    $product = Product::find($item['id']);
    $currentDate = strtotime(Carbon::now()->format('Y-m-d'));
    $discount = 0;

    if ($product->discount > 0) {
        if ($product->discount_type == 0) {
            $discount = $product->discount;
        }
        if ($product->discount_type == 1) {
            $discount = ($product->discount * $product->current_sale_price) / 100;
        }
    }

    if ($item['offerId'] > 0) {
        $offerInfo = Offer_product_list::with('offerInfo')
            ->where('product_id', $item['id'])
            ->where('offer_id', $item['offerId'])
            ->first();
        $startDate = strtotime($offerInfo['offerInfo']->start_date);
        $endDate = strtotime($offerInfo['offerInfo']->end_date);

        if ($startDate <= $currentDate && $endDate >= $currentDate) {
            if ($offerInfo->offer_type == 0) {
                $discount = $offerInfo->offer_amount;
            }
            if ($offerInfo->offer_type == 1) {
                $discount = ($offerInfo->offer_amount * $product->current_sale_price) / 100;
            }
        }
    }
    return $discount;
}

function calculateOrder($order_items)
{
    $total_payable = 0;
    $discountTotal = 0;
    foreach ($order_items as $item) {
        $product = Product::find($item['id']);
        $discount = calculateDiscount($item);
        $unitPrice = $product->current_sale_price - $discount;
        $quantity = $item['quantity'];
        $total_price = $quantity * $unitPrice;
        $total_payable += $total_price;
        $discountTotal += $discount * $quantity;
    }
    return [$total_payable, $discountTotal];
}

if (!function_exists('countryList')) {
    function countryList()
    {
        return countryListData();
    }
}

if (!function_exists('percentageToAmount')) {
    function percentageToAmount(float $amount, float $percentage): float
    {
        return ($amount * $percentage) / 100;
    }
}

if (!function_exists('amountToPercentage')) {
    function amountToPercentage(float $amount, float $total): float
    {
        if ($amount == 0) {
            return 0;
        }
        return ($amount / $total) * 100;
    }
}

if (!function_exists('dateFormat')) {
    function dateFormat($strDate, $format = 'd-m-Y')
    {
        if (is_null($strDate)) {
            return 'N/A';
        }
        return \Carbon\Carbon::parse($strDate)->format($format);
    }
}

if (!function_exists('priceFormat')) {
    function priceFormat($amount)
    {
        return number_format($amount, 2);
    }
}

if (!function_exists('getOrderStatusBadge')) {
    function getOrderStatusBadge($status)
    {
        return OrderStatus::badge($status);
    }
}

if (!function_exists('ixiSelected')) {
    function ixiSelected($v1, $v2)
    {
        return $v1 == $v2 ? 'selected' : '';
    }
}