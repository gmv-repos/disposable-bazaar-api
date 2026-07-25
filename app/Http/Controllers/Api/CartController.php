<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WishListResource;
use App\Models\Cart;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class CartController extends Controller
{
    //
    public function addCart(Request $request)
    {
        try {
            $input = $request->all();

            // Validate the request
            $validator = Validator::make($input, [
                'product_id' => 'required',
                'quantity' => 'nullable|integer',
                'pack_size' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => $validator->errors()->first(),
                        'data' => null,
                    ],
                    400,
                );
            }
            $product = Product::where('id', $input['product_id'])->where('deleted', 0)->where('status', 1)->first();

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

            $cart = new Cart();
            $cart->product_id = $product->id;
            $cart->user_id = Auth::user()->id;
            $cart->quantity = $input['quantity'] ?? 1;
            $cart->pack_size = $input['pack_size'] ?? 25;
            $cart->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Product Added to Cart Successfully',
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

    public function getCartList()
    {
        $userId = Auth::user()->id;

        // Fetch the cart items with related products and their variants
        $cartItems = Cart::with(['product.productVariants.variant'])
            ->where('user_id', $userId)
            ->get();

        // Transform the data to the desired structure
        $formattedCartItems = $cartItems->map(function ($cartItem) {
            return [
                'id' => $cartItem->id,
                'user_id' => $cartItem->user_id,
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'pack_size' => $cartItem->pack_size,
                'created_at' => $cartItem->created_at,
                'updated_at' => $cartItem->updated_at,
                'product' => [
                    'id' => $cartItem->product->id,
                    'name' => $cartItem->product->name,
                    'category_id' => $cartItem->product->category_id,
                    'subcategory_id' => $cartItem->product->subcategory_id,
                    'image_path' => $cartItem->product->image_path,
                    'code' => $cartItem->product->code,
                    'color' => $cartItem->product->color,
                    'size' => $cartItem->product->size,
                    'brand_id' => $cartItem->product->brand_id,
                    'supplier_id' => $cartItem->product->supplier_id,
                    'current_purchase_cost' => $cartItem->product->current_purchase_cost,
                    'current_sale_price' => $cartItem->product->current_sale_price,
                    'available_quantity' => $cartItem->product->available_quantity,
                    'discount_type' => $cartItem->product->discount_type,
                    'discount' => $cartItem->product->discount,
                    'is_popular' => $cartItem->product->is_popular,
                    'is_trending' => $cartItem->product->is_trending,
                    'product_video_url' => $cartItem->product->product_video_url,
                    'status' => $cartItem->product->status,
                    'product_variants' => $cartItem->product->productVariants->map(function ($variant) {
                        return [
                            'id' => $variant->id,
                            'pack_size' => $variant->variant->pack_size,
                            'price' => $variant->price,
                            'price_per_piece' => $variant->price_per_peice,
                        ];
                    }),
                ],
            ];
        });

        // Return the formatted response
        return response()->json([
            'status' => 'success',
            'message' => 'Cart Items Retrieved Successfully',
            'data' => $formattedCartItems,
        ]);
    }

    public function updateCart(Request $request, $cartId)
    {
        try {
            $input = $request->all();

            // Validate the request
            $validator = Validator::make($input, [
                'quantity' => 'nullable|integer',
                'pack_size' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => $validator->errors()->first(),
                        'data' => null,
                    ],
                    400,
                );
            }
            $cart = Cart::find($cartId);
            if (!$cart) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Cart Item Not Found',
                        'data' => null,
                    ],
                    404,
                );
            }
            $cart->quantity = $input['quantity'] ?? $cart->quantity;
            $cart->pack_size = $input['pack_size'] ?? $cart->pack_size;
            $cart->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Cart Item Updated Successfully',
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

    public function removeCart($cartId)
    {
        try {
            $cart = Cart::find($cartId);
            if (!$cart) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Cart Item Not Found',
                        'data' => null,
                    ],
                    404,
                );
            }
            $cart->delete();

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Cart Item Deleted Successfully',
                    'data' => null,
                ],
                200,
            );
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

    public function count(Request $request)
    {
        $count = Cart::where('user_id', Auth::user()->id)->count();
        return response()->json(
            [
                'status' => 'success',
                'message' => 'Cart Count Retrieved Successfully',
                'count' => $count,
            ],
            200,
        );
    }
}
