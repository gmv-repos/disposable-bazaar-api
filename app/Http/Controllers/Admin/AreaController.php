<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Area;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Expr\Array_;

use App\Imports\AreasImport;
use Maatwebsite\Excel\Facades\Excel;

class AreaController extends Controller
{
    protected $segment;
    public function __construct(Request $request)
    {
        $this->segment = $request->segment(1);
        $this->page = 'adminPanel.areas.';
    }

    public function index(Request $request)
    {
        $common_data = new Array_();
        $common_data->title = 'Area List';
        $areas = Area::get();
        return view($this->page . 'index', compact('common_data', 'areas'));
    }

    public function create()
    {
        $common_data = new Array_();
        $common_data->title = 'Add New Area';
        return view($this->page . 'create', compact('common_data'));
    }

    public function storeareas(Request $request)
    {
        $request->validate([
            'area_name' => 'required|string|max:255',
            'city_name' => 'required|string|max:255',
            'shipping_rate' => 'required|integer',
        ]);
        $area = new Area();

        $area->area_name = $request->input('area_name');
        $area->city_name = $request->input('city_name');
        $area->shipping_rate = $request->input('shipping_rate');
        $area->save();
        return redirect()->route('areas.list')->with('success', 'Area with Delivery Charges created successfully.');
    }

    public function areasEditInfo($id)
    {
        $common_data = new Array_();
        $common_data->title = 'Edit Area Detail';
        $area = Area::findOrFail($id);

        if (is_null($area)) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Area Not Found',
            ]);
        }
        return view('adminPanel.areas._areas_edit')->with(compact('area'))->render();
    }

    public function updateareas(Request $request)
    {
        $area = Area::findOrFail($request->id);
        Log::info('I am here');

        $request->validate([
            'area_name' => 'required|string|max:255',
            'city_name' => 'required|string|max:255',
            'shipping_rate' => 'required|integer',
        ]);

        $area->area_name = $request->input('area_name');
        $area->city_name = $request->input('city_name');
        $area->shipping_rate = $request->input('shipping_rate');
        $area->save();
        return redirect()->route('areas.list')->with('success', 'Area with Delivery Charges Updated successfully.');
    }

    public function areasDelete(Request $request)
    {
        $blog = Area::find($request->id);
        $blog->status = 0;
        $blog->save();
        return redirect()->back()->with('success', 'Area Successfully Deleted');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'excelfile' => 'required|mimes:xlsx,csv',
        ]);

        Excel::import(new AreasImport(), $request->file('excelfile'));

        return redirect()->back()->with('success', 'Areas imported successfully!');
    }
}
