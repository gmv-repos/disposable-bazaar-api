<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCategoryResource;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function categoryList(Request $request)
    {
        try {

            // $categoryList = ProductCategory::with(['categorySeoMetadata'])
            //     ->whereNull('parent_id')
            //     ->where('status', 1)
            //     ->where('deleted', 0)
            //     ->orderByRaw('ISNULL(serial_no), serial_no ASC')
            //     ->get();


            $sectionName = $request->input('sectionName', 'headerDropdown');

            $categoryList = ProductCategory::whereNull('parent_id')
                ->where('status', 1)
                ->where('deleted', 0)
                ->with(['childCategories' => function ($query) use ($sectionName) {
                    $query->withSortOrderForSection($sectionName)
                    ->with('sortOrders');
                }])
                ->withSortOrderForSection($sectionName)            
                ->with('sortOrders')            
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Product Categories retrieved successfully.',
                'data' => ProductCategoryResource::collection($categoryList),
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'An error occurred: ' . $e->getMessage(),
                ],
                500,
            ); // Use 500 status code for server errors
        }
    }

    public function categoryBySlug($slug)
    {
        try {
            $category = ProductCategory::where('slug', $slug)->where('status', 1)->where('deleted', 0)->first();
            if (!$category) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Product category not found.',
                    ],
                    404,
                );
            }
            return new ProductCategoryResource($category);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'An error occurred: ' . $e->getMessage(),
                ],
                500,
            ); // Use 500 status code for server errors
        }
    }
    public function allCategorySubcategory()
    {
        $categorySubcategory = ProductCategoryResource::collection(
            ProductCategory::where('status', 1)->where('deleted', 0)->get(),
        );
        return response()->json($categorySubcategory, 200);
    }

    public function popularCategory()
    {
        $popularCategory = ProductCategory::where('is_popular', 1)
            ->orderByRaw('CASE WHEN serial_no IS NULL THEN 1 ELSE 0 END')
            ->orderBy('serial_no')
            ->get();
        return response($popularCategory, 200);
    }
}
