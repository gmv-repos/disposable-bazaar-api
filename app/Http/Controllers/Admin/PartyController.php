<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Party;
use App\Models\Supplier;
use App\Models\PosCustomer;
use Illuminate\Support\Facades\DB;

class PartyController extends Controller
{
    public function index()
    {
        $parties = Party::all();
        return view('adminPanel.party.index', compact('parties'));
    }

    public function create()
    {
        return view('adminPanel.party.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone_one' => 'nullable|string',
            'phone_two' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $party = Party::create($request->all());

            Supplier::create([
                'id' => $party->id,
                'supplier_name' => $request->name,
                'email' => $request->email,
                'supplier_phone_one' => $request->phone_one,
                'supplier_phone_two' => $request->phone_two,
                'supplier_address' => $request->address,
            ]);

            PosCustomer::create([
                'id' => $party->id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone_one,
                'phone_two' => $request->phone_two,
                'address' => $request->address,
            ]);

            DB::commit();

            return redirect()->route('admin.parties.index')->with('success', 'Party created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create party: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $party = Party::findOrFail($id);
        return view('adminPanel.party.edit', compact('party'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone_one' => 'nullable|string',
            'phone_two' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $party = Party::findOrFail($id);
            $party->update($request->all());

            Supplier::updateOrCreate(
                ['id' => $party->id],
                [
                    'supplier_name' => $request->name,
                    'email' => $request->email,
                    'supplier_phone_one' => $request->phone_one,
                    'supplier_phone_two' => $request->phone_two,
                    'supplier_address' => $request->address,
                ],
            );

            PosCustomer::updateOrCreate(
                ['id' => $party->id],
                [
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone_one,
                    'phone_two' => $request->phone_two,
                    'address' => $request->address,
                ],
            );

            DB::commit();

            return redirect()->route('admin.parties.index')->with('success', 'Party updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with(['error' => 'Failed to update party: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $party = Party::findOrFail($id);

            Supplier::where('id', $party->id)->delete();
            PosCustomer::where('id', $party->id)->delete();
            $party->delete();

            DB::commit();

            return redirect()->route('admin.parties.index')->with('success', 'Party deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to delete party: ' . $e->getMessage()]);
        }
    }
}
