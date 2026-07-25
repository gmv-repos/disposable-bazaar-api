<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductSubCategory;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductSubcategoryController extends Controller
{
    public function categoryWiseSubcategory($id)
    {
        try {
            $category = ProductCategory::where('id', $id)->where('deleted', 0)->where('status', 1)->first();

            if (!$category) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Category Not Found',
                        'data' => null,
                    ],
                    404,
                );
            }
            $subcategoryList = ProductSubCategory::where('category_id', $id)
                ->where('deleted', 0)
                ->where('status', 1)
                ->get();
            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Subcategories fetched successfully',
                    'data' => $subcategoryList,
                ],
                200,
            );
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

    public function categoryWiseSubcategoryBySlug($slug)
    {
        try {
            $category = ProductCategory::where('slug', $slug)->where('deleted', 0)->where('status', 1)->first();

            if (!$category) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Category Not Found',
                        'data' => null,
                    ],
                    404,
                );
            }

            $id = $category->id;

            $subcategoryList = ProductSubCategory::where('category_id', $id)
                ->where('deleted', 0)
                ->where('status', 1)
                ->get();
            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Subcategories fetched successfully',
                    'data' => $subcategoryList,
                ],
                200,
            );
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

    public function allSubcategory()
    {
        $subcategoryList = ProductSubCategory::where('deleted', 0)
            ->where('status', 1)
            ->select('id', 'name', 'category_id')
            ->get();
        return response()->json($subcategoryList);
    }
}
