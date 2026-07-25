<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Variants;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Expr\Array_;

class VariantController extends Controller
{
    protected $segment;
    public function __construct(Request $request)
    {
        $this->segment = $request->segment(1);
        $this->page = 'adminPanel.variants.';
    }

    public function index(Request $request)
    {
        $common_data = new Array_();
        $common_data->title = 'Variant List';
        $variants = Variants::get();
        return view($this->page . 'index', compact('common_data', 'variants'));
    }

    public function create()
    {
        $common_data = new Array_();
        $common_data->title = 'Add New Variant';
        return view($this->page . 'create', compact('common_data'));
    }

    public function storevariants(Request $request)
    {
        try {
            //code...
            $request->validate([
                'name' => 'required|string|max:255',
                'pack_size' => 'required|string|max:255',
            ]);
            $variant = new Variants();
            $variant->name = $request->input('name');
            $variant->pack_size = $request->input('pack_size');
            $variant->save();
            return redirect()->route('variants.list')->with('success', 'Variant created successfully.');
        } catch (\Throwable $th) {
            //throw $th;
            Log::error('Error in VariantController@storevariants: ' . $th->getMessage());
            return redirect()->route('variants.list')->with('error', 'Error occurred while creating variant.');
        }
    }

    public function variantsEditInfo($id)
    {
        $common_data = new Array_();
        $common_data->title = 'Edit Variant Detail';
        $variant = Variants::findOrFail($id);

        if (is_null($variant)) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Variant Not Found',
            ]);
        }
        return view('adminPanel.variants._Variants_edit')->with(compact('variant'))->render();
    }

    public function updatevariants(Request $request)
    {
        try {
            //code...
            $request->validate([
                'id' => 'required|exists:variants,id',
                'name' => 'required|string|max:255',
                'pack_size' => 'required|string|max:255',
            ]);
            $variant = Variants::find($request->id);
            $variant->name = $request->input('name');
            $variant->pack_size = $request->input('pack_size');
            $variant->save();
            return redirect()->route('variants.list')->with('success', 'Variant updated successfully.');
        } catch (\Throwable $th) {
            //throw $th;
            Log::error('Error in VariantController@updatevariants: ' . $th->getMessage());
            return redirect()->route('variants.list')->with('error', 'Error occurred while updating variant.');
        }
    }
}