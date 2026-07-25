<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\WebsiteSetting;
use App\Models\ExpenseAccount;
use App\Models\BankAccount;
use App\Models\CashAccount;
use Illuminate\Validation\Rule;
use DB;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        // Get filter data from the request
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $expenseAccountId = $request->input('expense_account_id');

        // Build the base query
        $query = DB::table('expenses as e')
            ->select(
                'e.*',
                'ea.account_name as expense_account_name',
                'ba.account_name as bank_name',
                'ba.account_number',
                'ca.account_name as cash_name',
            )
            ->join('expense_accounts as ea', 'ea.id', '=', 'e.expense_account_id')
            ->leftJoin('bank_and_cash_accounts as ba', 'ba.id', '=', 'e.bank_account_id')
            ->leftJoin('bank_and_cash_accounts as ca', 'ca.id', '=', 'e.cash_account_id');

        // Filter by date range if provided
        if ($fromDate && $toDate) {
            $query = $query->whereBetween('expense_date', [$fromDate, $toDate]);
        }

        // Filter by expense account if provided
        if ($expenseAccountId) {
            $query = $query->where('expense_account_id', $expenseAccountId);
        }

        // Fetch filtered expenses
        $expenses = $query->get();

        // Calculate filtered summaries
        $todayExpense = $query->whereDate('expense_date', today())->sum('amount');
        $monthExpense = $query->whereMonth('expense_date', now()->month)->sum('amount');
        $totalExpense = $query->sum('amount');
        $expenseAccounts = ExpenseAccount::with('parent')->get();

        return view(
            'adminPanel.expenses.index',
            compact(
                'expenses',
                'todayExpense',
                'monthExpense',
                'totalExpense',
                'fromDate',
                'toDate',
                'expenseAccountId',
                'expenseAccounts',
            ),
        );
    }

    public function expenseAccountCreate()
    {
        $expenseAccounts = ExpenseAccount::get();
        return view('adminPanel.expenses.expenseAccountCreate', compact('expenseAccounts'));
    }

    public function expenseAccountstore(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'parent_id' => 'nullable|integer|exists:expense_accounts,id',
            'account_name' => [
                'required',
                'string',
                Rule::unique('expense_accounts')->where(function ($query) use ($request) {
                    return $query->where('parent_id', $request->parent_id ?? 0);
                }),
            ],
        ]);

        // Create the ExpenseAccount instance and set attributes manually
        $expenseAccount = new ExpenseAccount();
        $expenseAccount->parent_id = $validated['parent_id'] ?? 0;
        $expenseAccount->account_name = $validated['account_name'];
        $expenseAccount->save();

        // Redirect with success message
        return redirect()
            ->route('expense.expenseAccountIndex')
            ->with('success', 'Expense Account created successfully!');
    }

    public function expenseAccountIndex()
    {
        $expenseAccounts = ExpenseAccount::with('parent')->get();
        return view('adminPanel.expenses.expenseAccountIndex', compact('expenseAccounts'));
    }

    public function expenseAccountEdit($id)
    {
        $expenseAccount = ExpenseAccount::findOrFail($id);
        $expenseAccounts = ExpenseAccount::with('parent')->get();
        return view('adminPanel.expenses.expenseAccountEdit', compact('expenseAccount', 'expenseAccounts'));
    }

    public function expenseAccountUpdate(Request $request, $id)
    {
        // Find the ExpenseAccount instance by its ID
        $expenseAccount = ExpenseAccount::findOrFail($id);

        // Validate the incoming request
        $validated = $request->validate([
            'parent_id' => 'nullable|integer|exists:expense_accounts,id',
            'account_name' => [
                'required',
                'string',
                Rule::unique('expense_accounts')->where(function ($query) use ($request, $expenseAccount) {
                    return $query->where('parent_id', $request->parent_id ?? 0)->where('id', '!=', $expenseAccount->id); // Exclude current record
                }),
            ],
        ]);

        // Update the ExpenseAccount instance with validated attributes
        $expenseAccount->parent_id = $validated['parent_id'] ?? 0;
        $expenseAccount->account_name = $validated['account_name'];
        $expenseAccount->save();

        // Redirect with success message
        return redirect()
            ->route('expense.expenseAccountIndex')
            ->with('success', 'Expense Account updated successfully!');
    }

    public function expenseAccountActive($id)
    {
        // Find the ExpenseAccount instance by ID
        $expenseAccount = ExpenseAccount::findOrFail($id);

        // Directly set the status field manually
        $expenseAccount->status = 1; // Active
        $expenseAccount->save();

        // Redirect back with a success message
        return redirect()
            ->route('expense.expenseAccountIndex')
            ->with('success', 'Expense Account activated successfully!');
    }

    public function expenseAccountInactive($id)
    {
        // Find the ExpenseAccount instance by ID
        $expenseAccount = ExpenseAccount::findOrFail($id);

        // Directly set the status field manually
        $expenseAccount->status = 2; // Inactive
        $expenseAccount->save();

        // Redirect back with a success message
        return redirect()
            ->route('expense.expenseAccountIndex')
            ->with('success', 'Expense Account deactivated successfully!');
    }

    public function create()
    {
        // $expenseTypes = WebsiteSetting::getSettingByKey('expenseTypes');
        // $expenseTypes = json_decode($expenseTypes->value);
        $expenseTypes = ['Bill Payments', 'Maintenance', 'Salaries', 'Water Bills', 'Rent', 'Tea and Refreshments'];
        $expenseAccounts = ExpenseAccount::with('parent')->where('status', 1)->get();
        $bankAccounts = BankAccount::where('status', 1)->where('type', 1)->get();
        $cashAccounts = CashAccount::where('status', 1)->where('type', 2)->get();
        return view(
            'adminPanel.expenses.create',
            compact('expenseTypes', 'expenseAccounts', 'bankAccounts', 'cashAccounts'),
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_account_id' => 'required|integer|exists:expense_accounts,id',
            'bank_account_id' => 'nullable|integer|exists:bank_and_cash_accounts,id',
            'cash_account_id' => 'nullable|integer|exists:bank_and_cash_accounts,id',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable|string|max:500',
        ]);

        // Custom validation to ensure only one of the two is filled
        if (empty($validated['bank_account_id']) && empty($validated['cash_account_id'])) {
            return back()
                ->withErrors(['bank_account_id' => 'Either bank or cash account must be selected.'])
                ->withInput();
        }

        if (!empty($validated['bank_account_id']) && !empty($validated['cash_account_id'])) {
            return back()
                ->withErrors(['bank_account_id' => 'Only one of bank or cash account can be selected at a time.'])
                ->withInput();
        }

        $expense = Expense::create([
            'expense_account_id' => $validated['expense_account_id'],
            'bank_account_id' => $validated['bank_account_id'] ?? 0,
            'cash_account_id' => $validated['cash_account_id'] ?? 0,
            'expense_date' => $validated['expense_date'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
        ]);

        $tData = [
            'transaction_id' => $expense->id,
            'bank_id' => $validated['bank_account_id'] ?? 0,
            'cash_id' => $validated['cash_account_id'] ?? 0,
            'transaction_type' => 5,
            'voucher_no' => '-',
            'transaction_date' => $validated['expense_date'],
            'gross_amount' => 0,
            'discount_amount' => 0,
            'payable_amount' => 0,
            'receiveable_amount' => 0,
            'receipt_amount' => 0,
            'paid_amount' => 0,
            'expense_amount' => $validated['amount'],
            'particular' => $validated['description'],
        ];

        DB::table('transaction')->insert($tData);

        return redirect()->route('expenses.index')->with('success', 'Expense created successfully!');
    }

    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        $expenseAccounts = ExpenseAccount::with('parent')->get();
        $bankAccounts = BankAccount::where('status', 1)->where('type', 1)->get();
        $cashAccounts = CashAccount::where('status', 1)->where('type', 2)->get();
        return view('adminPanel.expenses.edit', compact('expense', 'expenseAccounts', 'bankAccounts', 'cashAccounts'));
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        $validated = $request->validate([
            'expense_account_id' => 'required|integer|exists:expense_accounts,id',
            'bank_account_id' => 'nullable|integer|exists:bank_and_cash_accounts,id',
            'cash_account_id' => 'nullable|integer|exists:bank_and_cash_accounts,id',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'nullable|string|max:500',
        ]);

        // Custom validation to ensure only one of the two is filled
        if (empty($validated['bank_account_id']) && empty($validated['cash_account_id'])) {
            return back()
                ->withErrors(['bank_account_id' => 'Either bank or cash account must be selected.'])
                ->withInput();
        }

        if (!empty($validated['bank_account_id']) && !empty($validated['cash_account_id'])) {
            return back()
                ->withErrors(['bank_account_id' => 'Only one of bank or cash account can be selected at a time.'])
                ->withInput();
        }

        $expense->update([
            'expense_account_id' => $validated['expense_account_id'],
            'bank_account_id' => $validated['bank_account_id'] ?? 0,
            'cash_account_id' => $validated['cash_account_id'] ?? 0,
            'expense_date' => $validated['expense_date'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
        ]);

        $tData = [
            'bank_id' => $validated['bank_account_id'],
            'transaction_type' => 5,
            'voucher_no' => '-',
            'transaction_date' => $validated['expense_date'],
            'gross_amount' => 0,
            'discount_amount' => 0,
            'payable_amount' => 0,
            'receiveable_amount' => 0,
            'receipt_amount' => 0,
            'paid_amount' => 0,
            'expense_amount' => $validated['amount'],
            'particular' => $validated['description'],
        ];
        DB::table('transaction')->where('transaction_id', $id)->where('transaction_type', 5)->update($tData);

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully!');
    }

    public function expenseActive($id)
    {
        // Find the ExpenseAccount instance by ID
        $expense = Expense::findOrFail($id);

        // Directly set the status field manually
        $expense->status = 1; // Active
        $expense->save();

        // Redirect back with a success message
        return redirect()->route('expenses.index')->with('success', 'Expense activated successfully!');
    }

    public function expenseInactive($id)
    {
        // Find the ExpenseAccount instance by ID
        $expense = Expense::findOrFail($id);

        // Directly set the status field manually
        $expense->status = 0; // Inactive
        $expense->save();

        // Redirect back with a success message
        return redirect()->route('expenses.index')->with('success', 'Expense deactivated successfully!');
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);

        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully!');
    }
}
