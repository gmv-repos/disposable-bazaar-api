<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Area;
use Illuminate\Support\Facades\Log;
class AreaController extends Controller
{
    //

    public function index(Request $request)
    {
        $areas = Area::get();
        return response()->json([
            'status' => 'success',
            'message' => 'Areas Retrived Successfully With Delievery Charegs',
            'data' => $areas,
        ]);
    }
}
