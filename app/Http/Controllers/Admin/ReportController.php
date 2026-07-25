<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sell_details;
use App\Models\OrderDetails;
use App\Models\Brand;
use App\Models\PurchaseReceive;
use App\Models\PurchaseReceiveItem;
use App\Models\Supplier;
use App\Models\Stock;
use App\Models\StockLog;
use App\Models\Party;
use App\Exports\PartyLedgerExport;
use App\Exports\LedgerExportFromView;
use Carbon\Carbon;
use Google\Service\AndroidPublisher\Resource\Purchases;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpParser\Node\Expr\Array_;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function customerLedgerReport(Request $request)
    {
        $common_data = new Array_();
        $common_data->title = 'Customer Ledger Report';
        $posCustomers = DB::table('pos_customers')->get();

        $filterCustomerId = $request->input('filterCustomerId');
        $filterFromDate = $request->input('filterFromDate');
        $filterToDate = $request->input('filterToDate');

        $filterData = [
            'filterCustomerId' => $filterCustomerId,
            'filterFromDate' => $filterFromDate,
            'filterToDate' => $filterToDate,
        ];

        if ($request->ajax() || $request->action == 'pdf' || $request->action == 'excel') {
            $balanceDetail = DB::connection('mysql')
                ->table('transaction as t')
                ->selectRaw(
                    '
                SUM(t.receiveable_amount) AS totalReceiveableAmount,
                SUM(t.receipt_amount) AS totalReceiptAmount,
                (SUM(t.receiveable_amount) - SUM(t.receipt_amount)) AS balanceAmount
            ',
                )
                ->where('t.transaction_date', '<', $filterFromDate)
                ->where('t.customer_id', '=', $filterCustomerId)
                ->whereIn('t.transaction_type', [1, 2])
                ->first();

            $getCustomerLedger = DB::table('transaction as t')
                ->leftJoin('pos_customers as poc', 't.customer_id', '=', 'poc.id')
                ->select('t.*', 'poc.name as customer_name')
                ->whereIn('transaction_type', [1, 2])
                ->where('t.customer_id', $filterCustomerId)
                ->whereBetween('t.transaction_date', [$filterFromDate, $filterToDate])
                ->get();

            if ($request->ajax()) {
                return view(
                    'adminPanel.report.customer_ledger_report_ajax',
                    compact('getCustomerLedger', 'balanceDetail', 'filterData'),
                );
            }

            if ($request->action == 'pdf') {
                $pdf = Pdf::loadView(
                    'adminPanel.report.customer_ledger_report_pdf',
                    compact('common_data', 'posCustomers', 'filterData', 'getCustomerLedger', 'balanceDetail'),
                );
                return $pdf->stream('customer_ledger_report_pdf');
            }

            if ($request->action == 'excel') {
                return Excel::download(
                    new LedgerExportFromView(
                        compact('common_data', 'posCustomers', 'filterData', 'getCustomerLedger', 'balanceDetail'),
                        'adminPanel.report.customer_ledger_report_excel',
                    ),
                    'customer_ledger_report_excel.xlsx',
                );
            }
        }

        return view('adminPanel.report.customer_ledger_report')->with(
            compact('common_data', 'posCustomers', 'filterData'),
        );
    }

    public function supplierLedgerReport(Request $request)
    {
        $common_data = new Array_();
        $common_data->title = 'Customer Ledger Report';
        $suppliers = DB::table('suppliers')->get();

        if ($request->ajax() || $request->action == 'pdf' || $request->action == 'excel') {
            $filterSupplierId = $request->input('filterSupplierId');
            $filterFromDate = $request->input('filterFromDate');
            $filterToDate = $request->input('filterToDate');

            $balanceDetail = DB::connection('mysql')
                ->table('transaction as t')
                ->selectRaw(
                    '
                SUM(t.payable_amount) AS totalReceiveableAmount,
                SUM(t.receipt_amount) AS totalReceiptAmount,
                (SUM(t.payable_amount) - SUM(t.receipt_amount)) AS balanceAmount
            ',
                )
                ->where('t.transaction_date', '<', $filterFromDate)
                ->where('t.supplier_id', '=', $filterSupplierId)
                ->whereIn('t.transaction_type', [3, 4])
                ->first();

            $getSupplierLedger = DB::table('transaction as t')
                ->leftJoin('suppliers as s', 't.supplier_id', '=', 's.id')
                ->select('t.*', 's.supplier_name')
                ->whereIn('transaction_type', [3, 4])
                ->where('t.supplier_id', $filterSupplierId)
                ->whereBetween('t.transaction_date', [$filterFromDate, $filterToDate])
                ->get();

            if ($request->ajax()) {
                return view(
                    'adminPanel.report.supplier_ledger_report_ajax',
                    compact('getSupplierLedger', 'balanceDetail'),
                );
            }

            if ($request->action == 'pdf') {
                $pdf = Pdf::loadView(
                    'adminPanel.report.supplier_ledger_report_pdf',
                    compact('common_data', 'suppliers', 'getSupplierLedger', 'balanceDetail'),
                );
                return $pdf->stream('supplier_ledger_report_pdf');
            }

            if ($request->action == 'excel') {
                return Excel::download(
                    new LedgerExportFromView(
                        compact('common_data', 'suppliers', 'getSupplierLedger', 'balanceDetail'),
                        'adminPanel.report.supplier_ledger_report_excel',
                    ),
                    'supplier_ledger_report.xlsx',
                );
            }
        }

        return view('adminPanel.report.supplier_ledger_report')->with(compact('common_data', 'suppliers'));
    }

    public function bankLedgerReport(Request $request)
    {
        $common_data = new Array_();
        $common_data->title = 'Bank Ledger Report';
        $banks = DB::table('bank_and_cash_accounts')->where('type', 1)->get();

        if ($request->ajax() || $request->action == 'pdf' || $request->action == 'excel') {
            $filterBankId = $request->input('filterBankId');
            $filterFromDate = $request->input('filterFromDate');
            $filterToDate = $request->input('filterToDate');

            $balanceDetail = DB::table('transaction as t')
                ->selectRaw(
                    '
                    SUM(CASE WHEN t.transaction_type = 2 THEN t.receipt_amount ELSE 0 END) AS totalReceiptAmount,
                    SUM(CASE WHEN t.transaction_type = 4 AND t.transaction_date < ? THEN t.receipt_amount ELSE 0 END) AS totalPaidAmount,
                    SUM(CASE WHEN t.transaction_type = 5 AND t.transaction_date < ? THEN t.expense_amount ELSE 0 END) AS totalExpenseAmount,
                    SUM(CASE WHEN t.transaction_type = 21 AND t.transaction_date < ? THEN t.amount_in ELSE 0 END) AS totalAmountIn,
                    SUM(CASE WHEN t.transaction_type = 22 AND t.transaction_date < ? THEN t.amount_out ELSE 0 END) AS totalAmountOut,
                    SUM(CASE WHEN t.transaction_type = 23 AND t.transaction_date < ? THEN t.extra_trx_amount ELSE 0 END) AS totalExtraIn,
                    SUM(CASE WHEN t.transaction_type = 24 AND t.transaction_date < ? THEN t.extra_trx_amount ELSE 0 END) AS totalExtraOut
                ',
                    array_fill(0, 6, $filterFromDate),
                )
                ->where('t.bank_id', $filterBankId)
                ->where('t.transaction_date', '<', $filterFromDate)
                ->whereIn('t.transaction_type', [2, 4, 5, 21, 22, 23, 24])
                ->first();

            $getBankLedger = DB::table('transaction as t')
                ->leftJoin('suppliers as s', 't.supplier_id', '=', 's.id')
                ->leftJoin('pos_customers as poc', 't.customer_id', '=', 'poc.id')
                ->select('t.*', 's.supplier_name', 'poc.name as customer_name')
                ->where('t.bank_id', $filterBankId)
                ->whereIn('t.transaction_type', [2, 4, 5, 21, 22, 23, 24])
                ->whereBetween('t.transaction_date', [$filterFromDate, $filterToDate])
                ->get();

            if ($request->ajax()) {
                return view('adminPanel.report.bank_ledger_report_ajax', compact('getBankLedger', 'balanceDetail'));
            }

            if ($request->action == 'pdf') {
                $pdf = Pdf::loadView(
                    'adminPanel.report.bank_ledger_report_pdf',
                    compact('common_data', 'banks', 'getBankLedger', 'balanceDetail'),
                );
                return $pdf->stream('bank_ledger_report_pdf');
            }

            if ($request->action == 'excel') {
                return Excel::download(
                    new LedgerExportFromView(
                        compact('common_data', 'banks', 'getBankLedger', 'balanceDetail'),
                        'adminPanel.report.bank_ledger_report_excel',
                    ),
                    'bank_ledger_report_excel.xlsx',
                );
            }
        }

        return view('adminPanel.report.bank_ledger_report')->with(compact('common_data', 'banks'));
    }

    public function cashLedgerReport(Request $request)
    {
        $common_data = new Array_();
        $common_data->title = 'Cash Ledger Report';
        $cashs = DB::table('bank_and_cash_accounts')->where('type', 2)->get();

        if ($request->ajax() || $request->action == 'pdf' || $request->action == 'excel') {
            $filterCashId = $request->input('filterCashId');
            $filterFromDate = $request->input('filterFromDate');
            $filterToDate = $request->input('filterToDate');

            $balanceDetail = DB::table('transaction as t')
                ->selectRaw(
                    '
                    SUM(CASE WHEN t.transaction_type = 2 THEN t.receipt_amount ELSE 0 END) AS totalReceiptAmount,
                    SUM(CASE WHEN t.transaction_type = 4 AND t.transaction_date < ? THEN t.receipt_amount ELSE 0 END) AS totalPaidAmount,
                    SUM(CASE WHEN t.transaction_type = 5 AND t.transaction_date < ? THEN t.expense_amount ELSE 0 END) AS totalExpenseAmount,
                    SUM(CASE WHEN t.transaction_type = 21 AND t.transaction_date < ? THEN t.amount_in ELSE 0 END) AS totalAmountIn,
                    SUM(CASE WHEN t.transaction_type = 22 AND t.transaction_date < ? THEN t.amount_out ELSE 0 END) AS totalAmountOut,
                    SUM(CASE WHEN t.transaction_type = 23 AND t.transaction_date < ? THEN t.extra_trx_amount ELSE 0 END) AS totalExtraIn,
                    SUM(CASE WHEN t.transaction_type = 24 AND t.transaction_date < ? THEN t.extra_trx_amount ELSE 0 END) AS totalExtraOut
                ',
                    array_fill(0, 6, $filterFromDate),
                )

                ->where('t.cash_id', $filterCashId)
                ->where('t.transaction_date', '<', $filterFromDate)
                ->whereIn('t.transaction_type', [2, 4, 5, 21, 22, 23, 24])
                ->first();

            $getCashLedger = DB::table('transaction as t')
                ->leftJoin('suppliers as s', 't.supplier_id', '=', 's.id')
                ->leftJoin('pos_customers as poc', 't.customer_id', '=', 'poc.id')
                ->select('t.*', 's.supplier_name', 'poc.name as customer_name')
                ->where('t.cash_id', $filterCashId)
                ->whereIn('t.transaction_type', [2, 4, 5, 21, 22, 23, 24])
                ->whereBetween('t.transaction_date', [$filterFromDate, $filterToDate])
                ->get();

            if ($request->ajax()) {
                return view('adminPanel.report.cash_ledger_report_ajax', compact('getCashLedger', 'balanceDetail'));
            }

            if ($request->action == 'pdf') {
                $pdf = Pdf::loadView(
                    'adminPanel.report.cash_ledger_report_pdf',
                    compact('common_data', 'cashs', 'getCashLedger', 'balanceDetail'),
                );
                return $pdf->stream('cash_ledger_report_pdf');
            }

            if ($request->action == 'excel') {
                return Excel::download(
                    new LedgerExportFromView(
                        compact('common_data', 'cashs', 'getCashLedger', 'balanceDetail'),
                        'adminPanel.report.cash_ledger_report_excel',
                    ),
                    'cash_ledger_report_excel.xlsx',
                );
            }
        }

        return view('adminPanel.report.cash_ledger_report')->with(compact('common_data', 'cashs'));
    }

    public function partyLedgerReport(Request $request)
    {
        $action = $request->input('action', null);
        $filterPartyId = $request->input('filterPartyId', 1);

        $filterFromDate = $request->filled('filterFromDate')
            ? $request->input('filterFromDate')
            : now()->subMonth()->toDateString();

        $filterToDate = $request->filled('filterToDate') ? $request->input('filterToDate') : now()->toDateString();

        $allowedTransactionTypes = [1, 2, 3, 4];

        $openingBalance = DB::table('transaction as t')
            ->selectRaw(
                '
            SUM(CASE WHEN t.transaction_type = 1 THEN t.receiveable_amount ELSE 0 END) AS totalSellAmount,
            SUM(CASE WHEN t.transaction_type = 3 THEN t.payable_amount ELSE 0 END) AS totalPurchaseAmount,
            SUM(CASE WHEN t.transaction_type = 2 THEN t.receipt_amount ELSE 0 END) AS totalReceiptAmount,
            SUM(CASE WHEN t.transaction_type = 4 THEN t.receipt_amount ELSE 0 END) AS totalPaymentAmount,
            (
                SUM(CASE WHEN t.transaction_type = 1 THEN t.receiveable_amount ELSE 0 END) -
                SUM(CASE WHEN t.transaction_type = 2 THEN t.receipt_amount ELSE 0 END) -
                SUM(CASE WHEN t.transaction_type = 3 THEN t.payable_amount ELSE 0 END) +
                SUM(CASE WHEN t.transaction_type = 4 THEN t.receipt_amount ELSE 0 END)
            ) AS openingBalance
        ',
            )
            ->where(function ($query) use ($filterPartyId) {
                $query->where('t.customer_id', $filterPartyId)->orWhere('t.supplier_id', $filterPartyId);
            })
            ->whereIn('t.transaction_type', $allowedTransactionTypes)
            ->where('t.transaction_date', '<', $filterFromDate)
            ->first();

        $ledgerEntries = DB::table('transaction as t')
            ->leftJoin('pos_customers as c', 't.customer_id', '=', 'c.id')
            ->leftJoin('suppliers as s', 't.supplier_id', '=', 's.id')
            ->select(
                't.id',
                't.transaction_date',
                't.transaction_type',
                't.receiveable_amount',
                't.payable_amount',
                't.receipt_amount',
                DB::raw('COALESCE(c.name, s.supplier_name) as party_name'),
                't.particular',
            )
            ->where(function ($query) use ($filterPartyId) {
                $query->where('t.customer_id', $filterPartyId)->orWhere('t.supplier_id', $filterPartyId);
            })
            ->whereIn('t.transaction_type', $allowedTransactionTypes)
            ->whereBetween('t.transaction_date', [$filterFromDate, $filterToDate])
            ->orderBy('t.transaction_date')
            ->orderBy('t.id')
            ->get();

        $runningBalance = $openingBalance->openingBalance ?? 0;
        $ledgerWithRunningBalance = $ledgerEntries->map(function ($entry) use (&$runningBalance) {
            $receivable = $entry->receiveable_amount ?? 0;
            $payable = $entry->payable_amount ?? 0;
            $receipt = $entry->receipt_amount ?? 0;

            switch ($entry->transaction_type) {
                case 1:
                    $runningBalance += $receivable;
                    break;
                case 2:
                    $runningBalance -= $receipt;
                    break;
                case 3:
                    $runningBalance -= $payable;
                    break;
                case 4:
                    $runningBalance += $receipt;
                    break;
            }

            return (object) [
                'id' => $entry->id,
                'transaction_date' => $entry->transaction_date,
                'transaction_type' => $entry->transaction_type,
                'receiveable_amount' => $receivable,
                'payable_amount' => $payable,
                'receipt_amount' => $receipt,
                'party_name' => $entry->party_name,
                'remarks' => $entry->particular,
                'running_balance' => -1 * $runningBalance,
            ];
        });

        $closingBalance = $ledgerWithRunningBalance->isNotEmpty()
            ? $ledgerWithRunningBalance->last()->running_balance
            : -1 * ($openingBalance->openingBalance ?? 0);

        $data = [
            'openingBalance' => -1 * ($openingBalance->openingBalance ?? 0),
            'closingBalance' => $closingBalance,
            'totalSellAmount' => $openingBalance->totalSellAmount ?? 0,
            'totalPurchaseAmount' => $openingBalance->totalPurchaseAmount ?? 0,
            'totalReceiptAmount' => $openingBalance->totalReceiptAmount ?? 0,
            'totalPaymentAmount' => $openingBalance->totalPaymentAmount ?? 0,
            'ledgerEntries' => $ledgerWithRunningBalance,
            'fromDate' => $filterFromDate,
            'toDate' => $filterToDate,
        ];

        if ($action == 'search') {
            $html = view('adminPanel.report.party_ledger_table', $data)->render();
            return response()->json(['html' => $html]);
        }

        if ($action == 'pdf') {
            $pdf = Pdf::loadView('adminPanel.report.party_ledger_table', $data);
            return $pdf->download('party_ledger_report.pdf');
        }

        if ($action == 'excel') {
            return Excel::download(new PartyLedgerExport($data), 'party_ledger_report.xlsx');
        }

        $data['parties'] = Party::all();
        return view('adminPanel.report.party_ledger', $data);
    }

    public function sellReport(Request $request)
    {
        $common_data = new Array_();
        $common_data->title = 'Sell Report';
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');

        if (strtotime($request->startdate) && strtotime($request->enddate)) {
            $from = date('Y-m-d', strtotime($request->startdate));
            $to = date('Y-m-d 23:59:59', strtotime($request->enddate));
            $sellProduct = DB::table('sell_details')
                ->join('products', 'sell_details.product_id', '=', 'products.id')
                ->whereBetween('sell_details.created_at', [$from, $to])
                ->select('products.*', DB::raw('SUM(sell_details.sale_quantity) as total_sell'))
                ->groupBy('sell_details.product_id')
                ->orderBy('total_sell', 'DESC')
                ->get();
        } else {
            $sellProduct = DB::table('sell_details')
                ->join('products', 'sell_details.product_id', '=', 'products.id')
                ->select('products.*', DB::raw('SUM(sell_details.sale_quantity) as total_sell'))
                ->groupBy('sell_details.product_id')
                ->orderBy('total_sell', 'DESC')
                ->get();
        }
        $filterData = [
            'startdate' => $startdate,
            'enddate' => $enddate,
        ];
        if ($request->action == 'pdf') {
            $pdf = Pdf::loadView(
                'adminPanel.report.sell_report_pdf',
                compact('sellProduct', 'common_data', 'filterData'),
            );
            return $pdf->stream('sell_report_pdf');
        }

        return view('adminPanel.report.sell_report')->with(compact('sellProduct', 'common_data', 'filterData'));
    }

    public function sellProfitReport(Request $request)
    {
        $common_data = new Array_();
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');
        $common_data->title = 'Sell Profit Report';
        if (strtotime($request->startdate) && strtotime($request->enddate)) {
            $from = date('Y-m-d', strtotime($request->startdate));
            $to = date('Y-m-d 23:59:59', strtotime($request->enddate));

            $sellProduct = DB::table('sell_details')
                ->join('products', 'sell_details.product_id', '=', 'products.id')
                ->whereBetween('sell_details.created_at', [$from, $to])
                ->select(
                    'products.*',
                    DB::raw('SUM(sell_details.sale_quantity) as total_sell'),
                    DB::raw('SUM(sell_details.unit_product_cost) as total_cost'),
                    DB::raw('SUM(sell_details.unit_sell_price) as total_sell_price'),
                )
                ->groupBy('sell_details.product_id')
                ->orderBy('total_sell_price', 'DESC')
                ->get();
        } else {
            $sellProduct = DB::table('sell_details')
                ->join('products', 'sell_details.product_id', '=', 'products.id')
                ->select(
                    'products.*',
                    DB::raw('SUM(sell_details.sale_quantity) as total_sell'),
                    DB::raw('SUM(sell_details.unit_product_cost) as total_cost'),
                    DB::raw('SUM(sell_details.unit_sell_price) as total_sell_price'),
                )
                ->groupBy('sell_details.product_id')
                ->orderBy('total_sell_price', 'DESC')
                ->get();
        }
        $filterData = [
            'startdate' => $startdate,
            'enddate' => $enddate,
        ];
        if ($request->action == 'pdf') {
            $pdf = Pdf::loadView(
                'adminPanel.report.profit_report_pdf',
                compact('sellProduct', 'common_data', 'filterData'),
            );
            return $pdf->stream('profit_report_pdf');
        }
        return view('adminPanel.report.profit_report')->with(compact('sellProduct', 'common_data', 'filterData'));
    }

    public function productReport(Request $request)
    {
        $productID = $request->input('product');

        $reportDataQuery = Product::with('orderDetails', 'sellDetails');

        if ($productID) {
            $reportDataQuery->where('id', $productID);
        }

        $reportData = $reportDataQuery->get();

        $allProducts = Product::all();

        $data = compact('allProducts', 'reportData');

        return view('adminPanel.report.product_report', $data);
    }

    public function brandReport(Request $request)
    {
        $brandID = $request->input('brand');

        $reportDataQuery = Brand::with('products');

        if ($brandID) {
            $reportDataQuery->where('id', $brandID);
        }

        $reportData = $reportDataQuery->get();

        $allBrands = Brand::all();
        $data = compact('allBrands', 'reportData');

        return view('adminPanel.report.brand_report', $data);
    }

    public function simpleStockReport(Request $request)
    {
        $allProducts = Product::all();

        $query = DB::table('products as p')
            ->leftJoin('purchase_receive_items as pri', function ($join) use ($request) {
                $join->on('pri.product_id', '=', 'p.id');
                if ($request->from_date && $request->to_date) {
                    $join->whereBetween('pri.created_at', [$request->from_date, $request->to_date]);
                }
            })
            ->leftJoin('order_details as od', function ($join) use ($request) {
                $join->on('od.product_id', '=', 'p.id');
                if ($request->from_date && $request->to_date) {
                    $join->whereBetween('od.created_at', [$request->from_date, $request->to_date]);
                }
            })
            ->leftJoin('sell_details as sd', function ($join) use ($request) {
                $join->on('sd.product_id', '=', 'p.id');
                if ($request->from_date && $request->to_date) {
                    $join->whereBetween('sd.created_at', [$request->from_date, $request->to_date]);
                }
            });

        if ($request->filled('product_id')) {
            $query->where('p.id', $request->product_id);
        }

        $reportData = $query
            ->select(
                'p.name as product_name',
                'p.available_quantity as stock',
                DB::raw(
                    'COALESCE(SUM(pri.total_cost_amount) / NULLIF(SUM(pri.total_qty), 0), 0) as avg_purchase_price',
                ),
                DB::raw('COALESCE((
                    COALESCE(SUM(od.product_sub_total), 0) + COALESCE(SUM(sd.total_payable_amount), 0)
                ) / NULLIF(
                    COALESCE(SUM(od.total_pieces), 0) + COALESCE(SUM(sd.sale_quantity), 0), 0
                ), 0) as avg_sell_price'),
            )
            ->groupBy('p.id', 'p.name', 'p.available_quantity')
            ->get();

        if ($request->action == 'pdf') {
            $pdf = Pdf::loadView('adminPanel.report.simple_stock_report_pdf', compact('reportData'));
            return $pdf->stream('simple_stock_report.pdf');
        }

        return view('adminPanel.report.simple_stock_report', compact('reportData', 'allProducts', 'request'));
    }

    public function detailStockReport(Request $request)
    {
        $allProducts = Product::all();

        $query = StockLog::with(['product', 'brand', 'stock']);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [$request->from_date . ' 00:00:00', $request->to_date . ' 23:59:59']);
        }

        $logs = $query->get();

        $reportData = $logs
            ->groupBy('product_id')
            ->map(function ($productLogs) {
                $product = $productLogs->first()->product;

                $brandWise = $productLogs->groupBy('brand_id')->map(function ($brandLogs) {
                    $brand = $brandLogs->first()->brand;

                    $qty = $brandLogs->sum(function ($log) {
                        return $log->type === StockLog::TYPE_IN ? $log->qty : -$log->qty;
                    });

                    return (object) [
                        'name' => $brand ? $brand->name : 'N/A',
                        'qty' => $qty,
                    ];
                });

                $totalQty = $brandWise->sum('qty');

                return (object) [
                    'product_name' => $product ? $product->name : 'Unknown Product',
                    'brandWise' => $brandWise->values(),
                    'total_qty' => $totalQty,
                ];
            })
            ->values();

        if ($request->action == 'pdf') {
            $pdf = Pdf::loadView('adminPanel.report.detail_stock_report_pdf', compact('reportData'));
            return $pdf->stream('detail_stock_report.pdf');
        }

        return view('adminPanel.report.detail_stock_report', compact('reportData', 'allProducts', 'request'));
    }
    // public function detailStockReport(Request $request)
    // {

    //     $allProducts = Product::all();

    //     $query = DB::table('products as p')
    //         ->leftJoin('purchase_receive_items as pri', function ($join) use ($request) {
    //             $join->on('pri.product_id', '=', 'p.id');
    //             if ($request->from_date && $request->to_date) {
    //                 $join->whereBetween('pri.created_at', [$request->from_date, $request->to_date]);
    //             }
    //         })
    //         ->leftJoin('sell_details as sd', function ($join) use ($request) {
    //             $join->on('sd.product_id', '=', 'p.id');
    //             if ($request->from_date && $request->to_date) {
    //                 $join->whereBetween('sd.created_at', [$request->from_date, $request->to_date]);
    //             }
    //         })
    //         ->leftJoin('brands as b_pri', 'pri.brand_id', '=', 'b_pri.id')
    //         ->leftJoin('brands as b_sd', 'sd.brand_id', '=', 'b_sd.id');

    //     if ($request->filled('product_id')) {
    //         $query->where('p.id', $request->product_id);
    //     }

    //     $reportData = $query
    //         ->select(
    //             'p.name as product_name',
    //             'p.available_quantity as stock',
    //             DB::raw('COALESCE(SUM(pri.total_cost_amount) / NULLIF(SUM(pri.total_qty), 0), 0) as avg_purchase_price'),
    //             DB::raw('COALESCE((
    //                 COALESCE(SUM(od.product_sub_total), 0) + COALESCE(SUM(sd.total_payable_amount), 0)
    //             ) / NULLIF(
    //                 COALESCE(SUM(od.total_pieces), 0) + COALESCE(SUM(sd.sale_quantity), 0), 0
    //             ), 0) as avg_sell_price'),
    //             DB::raw("GROUP_CONCAT(DISTINCT b_pri.name SEPARATOR ' | ') as purchase_brands"),
    //             DB::raw("GROUP_CONCAT(DISTINCT b_sd.name SEPARATOR ' | ') as sell_brands")
    //         )
    //         ->leftJoin('order_details as od', function ($join) use ($request) {
    //             $join->on('od.product_id', '=', 'p.id');
    //             if ($request->from_date && $request->to_date) {
    //                 $join->whereBetween('od.created_at', [$request->from_date, $request->to_date]);
    //             }
    //         })
    //         ->groupBy('p.id', 'p.name', 'p.available_quantity')
    //         ->get();

    //     if ($request->action == 'pdf') {
    //         $pdf = Pdf::loadView('adminPanel.report.detail_stock_report_pdf', compact('reportData'));
    //         return $pdf->stream('detail_stock_report.pdf');
    //     }

    //     return view(
    //         'adminPanel.report.detail_stock_report',
    //         compact(
    //             'reportData',
    //             'allProducts',
    //             'request'
    //         )
    //     );
    // }

    public function variantReport(Request $request)
    {
        $data = ['page' => 'Hello World'];
        return view('adminPanel.report.variant_report', $data);
    }

    public function purchaseReport(Request $request)
    {
        $supplierID = $request->input('supplier');
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');
        $allSuppliers = Supplier::all();
        $query = PurchaseReceive::with('prItems.product', 'supplier', 'purchase');
        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->supplier);
        }
        // Filter by Date Range
        if ($request->filled('startdate') && $request->filled('enddate')) {
            $query->whereBetween('created_at', [$request->startdate . ' 00:00:00', $request->enddate . ' 23:59:59']);
        } elseif ($request->filled('startdate')) {
            $query->whereDate('created_at', '>=', $request->startdate);
        } elseif ($request->filled('enddate')) {
            $query->whereDate('created_at', '<=', $request->enddate);
        }
        $allpurchase = $query->get();
        $filterData = [
            'supplier' => $supplierID,
            'startdate' => $startdate,
            'enddate' => $enddate,
        ];
        $data = compact('allSuppliers', 'allpurchase', 'filterData');
        if ($request->action == 'pdf') {
            $pdf = Pdf::loadView('adminPanel.report.purchase_report_pdf', $data);
            return $pdf->stream('purchase_report_pdf.pdf');
        }
        return view('adminPanel.report.purchase_report', $data);
    }
    public function orderReport(Request $request)
    {
        $allProducts = Product::all();
        $productID = $request->input('product');
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');
        $query = OrderDetails::with('order', 'product');
        if ($request->filled('product')) {
            $query->where('product_id', $request->product);
        }
        if ($request->filled('startdate')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->startdate);
            });
        }
        if ($request->filled('enddate')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->enddate);
            });
        }
        $allOrderDetails = $query
            ->get()
            ->groupBy('product_id')
            ->map(function ($group) {
                $product = $group->first()->product;
                $totalQty = $group->sum('qty');
                $unitPrice = $group->first()->product_sub_total / ($group->first()->qty ?: 1);
                $totalPrice = $group->sum('product_sub_total');
                return [
                    'product_name' => $product->name ?? 'N/A',
                    'product_code' => $product->code ?? 'N/A',
                    'total_qty' => $totalQty,
                    'unit_price' => number_format($unitPrice, 2),
                    'total_price' => number_format($totalPrice, 2),
                ];
            });

        $filterData = [
            'product' => $productID,
            'startdate' => $startdate,
            'enddate' => $enddate,
        ];
        $data = compact('allProducts', 'allOrderDetails', 'filterData');
        if ($request->action == 'pdf') {
            $pdf = Pdf::loadView('adminPanel.report.order_report_pdf', $data);
            return $pdf->stream('order_report_pdf.pdf');
        }
        return view('adminPanel.report.order_report', $data);
    }

    public function sellOrderReport(Request $request)
    {
        $allProducts = Product::all();
        $productID = $request->input('product');
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');
        // Use correct relationship methods
        $query = Sell_details::with('sellInfo', 'productInfo');

        // Filter by product
        if ($request->filled('product')) {
            $query->where('product_id', $request->product);
        }
        // Filter by start date
        if ($request->filled('startdate')) {
            $query->whereHas('sellInfo', function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->startdate);
            });
        }
        // Filter by end date
        if ($request->filled('enddate')) {
            $query->whereHas('sellInfo', function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->enddate);
            });
        }

        // Group and summarize
        $allSellOrderDetails = $query
            ->get()
            ->groupBy('product_id')
            ->map(function ($group) {
                $product = $group->first()->productInfo;
                $totalQty = $group->sum('sale_quantity');
                $unitPrice = $group->first()->unit_sell_price;
                $totalPrice = $group->sum(function ($item) {
                    return $item->unit_sell_price * $item->sale_quantity;
                });

                return [
                    'product_name' => $product->name ?? 'N/A',
                    'product_code' => $product->code ?? 'N/A',
                    'total_qty' => $totalQty,
                    'unit_price' => number_format($unitPrice, 2),
                    'total_price' => number_format($totalPrice, 2),
                ];
            });

        $filterData = [
            'product' => $productID,
            'startdate' => $startdate,
            'enddate' => $enddate,
        ];
        $data = compact('allProducts', 'allSellOrderDetails', 'filterData');
        if ($request->action == 'pdf') {
            $pdf = Pdf::loadView('adminPanel.report.sell_order_report_pdf', $data);
            return $pdf->stream('sell_order_report_pdf.pdf');
        }
        return view('adminPanel.report.sell_order_report', compact('allProducts', 'allSellOrderDetails', 'filterData'));
    }

    public function stockSummaryReport(Request $request)
    {
        $allProducts = Product::all();
        $productId = $request->product;
        $startdate = $request->startdate ?? '2000-01-01';
        $endDate = $request->enddate ?? now()->format('Y-m-d');

        $productCondition = $productId ? 'WHERE p.id = ?' : '';

        $sql = "
            SELECT 
                p.*, 
                (
                    SELECT SUM(sale_quantity) 
                    FROM sell_details 
                    WHERE product_id = p.id 
                    AND created_at BETWEEN ? AND ?
                ) AS total_sale_qty,
                (
                    SELECT SUM(qty) 
                    FROM order_details 
                    WHERE product_id = p.id 
                    AND created_at BETWEEN ? AND ?
                ) AS total_order_qty,
                (
                    SELECT SUM(total_qty) 
                    FROM purchase_receive_items 
                    WHERE product_id = p.id 
                    AND created_at BETWEEN ? AND ?
                ) AS total_purchase_qty
            FROM products p
            $productCondition
        ";

        $bindings = [$startdate, $endDate, $startdate, $endDate, $startdate, $endDate];
        if ($productId) {
            $bindings[] = $productId;
        }
        $data = DB::select($sql, $bindings);
        if ($request->action == 'pdf') {
            $allProducts = $data;
            $pdf = Pdf::loadView('adminPanel.report.stock_summary_report_pdf', compact('allProducts', 'request'));
            return $pdf->stream('stock_summary_report_pdf.pdf');
        }

        return view(
            'adminPanel.report.stockSummaryReport',
            compact('allProducts', 'startdate', 'endDate', 'data', 'productId'),
        );
    }

    public function sellAndOrderReport(Request $request)
    {
        $query = StockLog::select(
            'stock_logs.product_id',
            'products.name as product_name',
            DB::raw('SUM(stock_logs.qty) as total_qty'),
        )
            ->join('products', 'products.id', '=', 'stock_logs.product_id')
            ->where('stock_logs.type', StockLog::TYPE_OUT);

        if ($request->filled('fromDate') && $request->filled('toDate')) {
            $query->whereDate('stock_logs.created_at', '>=', $request->fromDate);
            $query->whereDate('stock_logs.created_at', '<=', $request->toDate);
        }

        $reportData = $query->groupBy('stock_logs.product_id', 'products.name')->get();

        return view('adminPanel.report.sell_and_order_report', compact('reportData'));
    }

    public function x(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        return $ledger = DB::table('stock_logs')
            ->select([
                'stock_logs.created_at as date',
                'products.name as product',
                'stock_logs.type',
                'stock_logs.qty',
                DB::raw(
                    "
                        SUM(CASE 
                            WHEN stock_logs.type = '" .
                        StockLog::TYPE_IN .
                        "' THEN qty
                            WHEN stock_logs.type = '" .
                        StockLog::TYPE_OUT .
                        "' THEN -qty
                            ELSE 0
                        END) 
                        OVER (PARTITION BY stock_logs.product_id ORDER BY stock_logs.created_at) AS balance
                    ",
                ),
            ])
            ->join('products', 'products.id', '=', 'stock_logs.product_id')
            ->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('stock_logs.created_at', [$fromDate, $toDate]);
            })
            ->orderBy('products.id')
            ->orderBy('stock_logs.created_at')
            ->get();
    }

    // public function stockSummaryReportPdf()
    // {
    //     $allProducts = Product::all();
    //     $pdf = Pdf::loadView('adminPanel.report.stock_summary_report_pdf', [
    //         'allProducts' => $allProducts
    //     ]);

    //     return $pdf->stream('stock_summary_report.pdf');
    // }

    // public function stockSummaryReport(Request $request)
    // {
    //     $allProducts = Product::all();
    //     $productId = $request->product;
    //     $startDate = $request->startdate;
    //     $endDate = $request->enddate;
    //     // Build query
    //     $query = PurchaseReceiveItem::with('product');

    //     if ($productId) {
    //         $query->where('product_id', $productId);
    //     }

    //     if ($startDate && $endDate) {
    //         $query->whereBetween('created_at', [$startDate, $endDate]);
    //     }

    //     $purchaseItems = $query->get();

    //     // Get unique product IDs
    //     $productIds = $purchaseItems->pluck('product_id')->unique();

    //     // Fetch related Sell_details
    //     $sellQuery = Sell_details::whereIn('product_id', $productIds);
    //     if ($startDate && $endDate) {
    //         $sellQuery->whereBetween('created_at', [$startDate, $endDate]);
    //     }
    //     $sellDetails = $sellQuery
    //         ->select('product_id')
    //         ->selectRaw('SUM(sale_quantity) as total_sale_quantity')
    //         ->groupBy('product_id')
    //         ->get()
    //         ->keyBy('product_id');

    //     // Fetch related OrderDetails
    //     $orderQuery = OrderDetails::whereIn('product_id', $productIds);
    //     if ($startDate && $endDate) {
    //         $orderQuery->whereBetween('created_at', [$startDate, $endDate]);
    //     }
    //     $orderDetails = $orderQuery
    //         ->select('product_id')
    //         ->selectRaw('SUM(qty) as total_order_qty')
    //         ->groupBy('product_id')
    //         ->get()
    //         ->keyBy('product_id');

    //     // Prepare item ledger data
    //     $itemLedgerData = [];

    //     foreach ($purchaseItems->groupBy('product_id') as $productId => $items) {
    //         $product = $items->first()->product;
    //         $totalPurchaseQty = $items->sum('total_qty');
    //         $totalSellQty = $sellDetails[$productId]->total_sale_quantity ?? 0;
    //         $totalOrderQty = $orderDetails[$productId]->total_order_qty ?? 0;

    //         $itemLedgerData[] = [
    //             'product_name'   => $product->name ?? 'N/A',
    //             'product_code'   => $product->code ?? 'N/A',
    //             'purchased_qty'  => $totalPurchaseQty,
    //             'sold_qty'       => $totalSellQty,
    //             'ordered_qty'    => $totalOrderQty,
    //         ];
    //     }

    //     return view('adminPanel.report.stockSummaryReport', compact('allProducts', 'itemLedgerData', 'productId', 'startDate', 'endDate'));
    // }
}
