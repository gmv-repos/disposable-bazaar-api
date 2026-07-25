<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WishListResource;
use App\Models\Wishlist;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishListController extends Controller
{
    public function addWishList($productId)
    {
        try {
            $product = Product::where('id', $productId)->where('deleted', 0)->where('status', 1)->first();

            if (!$product) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Product Not Found',
                        'data' => null,
                    ],
                    404,
                );
            }

            $wishlist = new Wishlist();
            $wishlist->product_id = $product->id;
            $wishlist->user_id = Auth::user()->id;
            $wishlist->date = Carbon::now();
            $wishlist->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Product Added to Wishlist Successfully',
                'data' => null,
            ]);
        } catch (\Exception $e) {
            //throw $th;
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'An error occurred: ' . $e->getMessage(),
                    'data' => null,
                ],
                500,
            );
        }
    }
    public function getWishList()
    {
        try {
            $userId = Auth::user()->id;

            // Eager load the productInfo and its variants
            $wishlist = Wishlist::with('productInfo.productVariants')->where('user_id', $userId)->get();

            // Format the response data manually
            $wishlistData = $wishlist
                ->map(function ($item) {
                    $product = $item->productInfo;

                    // Check if productInfo exists to avoid null errors
                    if (!$product) {
                        return null; // If the product doesn't exist, return null for this wishlist item
                    }

                    return [
                        'id' => $item->id,
                        'product_id' => $product->id,
                        'name' => $product->name,
                        'category_id' => $product->category_id,
                        'subcategory_id' => $product->subcategory_id,
                        'image_path' => $product->image_path,
                        'supplier_id' => $product->supplier_id,
                        'code' => $product->code,
                        'brand_id' => $product->brand_id,
                        'current_sale_price' => round($product->current_sale_price),
                        'current_purchase_cost' => $product->current_purchase_cost,
                        'current_wholesale_price' => $product->current_wholesale_price,
                        'wholesale_minimum_qty' => $product->wholesale_minimum_qty,
                        'previous_wholesale_price' => $product->previous_wholesale_price,
                        'previous_sale_price' => $product->previous_sale_price,
                        'previous_purchase_cost' => $product->previous_purchase_cost,
                        'available_quantity' => $product->available_quantity,
                        'discount_type' => $product->discount_type,
                        'discount' => $product->discount,
                        'unit_type' => $product->unit_type,
                        'description' => $product->description,
                        'offer_amount' => 0,
                        'offer_type' => 0,
                        'is_popular' => $product->is_popular,
                        'is_trending' => $product->is_trending,
                        'status' => $product->status,
                        'created_at' => $product->created_at,
                        'updated_at' => $product->updated_at,
                        'deleted' => $product->deleted,
                        'variants' => $product->productVariants->map(function ($variant) {
                            return [
                                'id' => $variant->id,
                                'pack_size' => $variant->variant ? $variant->variant->pack_size : null,
                                'price' => $variant->price,
                                'price_per_piece' => $variant->price_per_peice,
                                'status' => $variant->status,
                            ];
                        }),
                    ];
                })
                ->filter(); // Remove null entries in case any productInfo is missing

            return response()->json([
                'status' => 'success',
                'message' => 'Wishlist Retrieved Successfully',
                'data' => $wishlistData,
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

    public function count(Request $request)
    {
        $count = Wishlist::where('user_id', Auth::user()->id)->count();

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Wishlist Count Retrieved Successfully',
                'count' => $count,
            ],
            200,
        );
    }

    public function removeFromWishList($id)
    {
        try {
            $wishlist = Wishlist::find($id);

            if (!$wishlist) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Product Not Found in Wishlist',
                        'data' => null,
                    ],
                    404,
                );
            }
            $wishlist->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Product Removed from Wishlist Successfully',
                'data' => null,
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'An error occurred: ' . $e->getMessage(),
                    'data' => null,
                ],
                500,
            );
        }
    }
}