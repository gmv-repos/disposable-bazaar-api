<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bundle;

class BundleController extends Controller
{
    public function index()
    {
        $bundles = Bundle::with(['bundleItems', 'bundleItems.product'])
            ->where('status', '1')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Bundles fetched successfuly',
            'data' => $bundles,
        ]);
    }

    public function getByID($id)
    {
        $bundle = Bundle::with(['bundleItems', 'bundleItems.product', 'bundleImages'])
            ->where('status', '1')
            ->where('id', $id)
            ->first();

        if (is_null($bundle)) {
            return response()->json([
                'success' => false,
                'message' => 'Bundle Not Found',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Bundle fetched successfuly',
            'data' => $bundle,
        ]);
    }

    public function getBySlug(Request $request)
    {
        $bundle = Bundle::where('slug', $request->slug)->first();

        if (is_null($bundle)) {
            return response()->json([
                'success' => false,
                'message' => 'Bundle Not Found',
            ]);
        }

        return $this->getByID($bundle->id);
    }
}
