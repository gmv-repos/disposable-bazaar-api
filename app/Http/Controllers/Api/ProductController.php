<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductSize;
use Illuminate\Support\Facades\Log;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\Review;
use App\Models\ReviewLike;
use App\Models\Option;
use App\Models\Variants;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function homeTrendingProduct(Request $request)
    {
        try {
            // Fetch the trending products with pagination
            $trending_products = Product::with('productImage', 'childProducts', 'seoMetadata')
                ->where('is_trending', 1)
                ->where('deleted', 0)
                ->where('status', 1)
                ->get();

            // Return the products as a collection of ProductResource
            return response()->json([
                'status' => 'suucess',
                'message' => 'Treanding Products Retrieved Successfully',
                'data' => ProductResource::collection($trending_products),
            ]);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes

            // Return a JSON response indicating the error
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'An error occurred while fetching trending products: ' . $e->getMessage(),
                ],
                500,
            ); // 500 status code for server errors
        }
    }

    public function categoryProduct(Request $request, $id)
    {
        try {
            // Fetch the category to ensure it exists
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

            $productsType = $request->input('type', null);


            //Filter Custom / Normal Product or All
            $category_product = Product::query()
                ->when($productsType === 'custom', fn($q) => $q->customizeable())
                ->when($productsType === 'normal', fn($q) => $q->normal())
                ->with(['productImage', 'childProducts', 'seoMetadata'])
                ->where(function ($q) use ($id) {
                    $q->where('category_id', $id)
                        ->orWhereHas('productCategory', function ($q) use ($id) {
                            $q->where('parent_id', $id);
                        });
                })
                ->where([
                    ['deleted', '=', 0],
                    ['status', '=', 1],
                ])
                ->get();



            // Transform the products to include only necessary fields
            $categoryProductTransformed = $category_product->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'unit_type' => $product->unit_type,
                    'slug' => $product->slug,
                    'category_id' => $product->category_id,
                    'subcategory_id' => $product->subcategory_id,
                    'code' => $product->code,
                    'color' => $product->color,
                    'size' => $product->size,
                    'current_purchase_cost' => $product->current_purchase_cost,
                    'current_sale_price' => $product->current_sale_price,
                    'available_quantity' => $product->available_quantity,
                    'discount' => $product->discount,
                    'description' => $product->description,
                    'is_popular' => $product->is_popular,
                    'is_trending' => $product->is_trending,
                    'order_limit' => $product->order_limit,
                    'stock_status' => $product->stock_status,
                    'is_customizeable' => $product->is_customizeable,
                    'seoMetadata' => $product->seoMetadata,
                    'product_image' => $product->productImage->map(function ($image) {
                        return [
                            'image' => $image->image, // Only return the image path
                        ];
                    }),
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Product By Category Retrieved Successfully',
                'data' => $categoryProductTransformed,
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

    public function categoryProductBySlug($slug)
    {
        try {
            // Fetch the category to ensure it exists
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
            // Fetch products in the specified category
            $category_product = Product::with('productImage', 'childProducts', 'seoMetadata')
                ->where('category_id', $id)
                ->where('deleted', 0)
                ->where('status', 1)
                ->get();

            // Transform the products to include only necessary fields
            $categoryProductTransformed = $category_product->map(function ($product) {
                return [
                    'id' => $product->id,
                    'unit_type' => $product->unit_type,
                    'is_customizeable' => $product->is_customizeable,
                    'childProducts' => $product->childProducts,
                    'name' => $product->name,
                    'category_id' => $product->category_id,
                    'subcategory_id' => $product->subcategory_id,
                    'code' => $product->code,
                    'color' => $product->color,
                    'size' => $product->size,
                    'current_purchase_cost' => $product->current_purchase_cost,
                    'current_sale_price' => $product->current_sale_price,
                    'available_quantity' => $product->available_quantity,
                    'discount' => $product->discount,
                    'description' => $product->description,
                    'is_popular' => $product->is_popular,
                    'is_trending' => $product->is_trending,
                    'order_limit' => $product->order_limit,
                    'stock_status' => $product->stock_status,
                    'seoMetadata' => $product->seoMetadata,
                    'product_image' => $product->productImage->map(function ($image) {
                        return [
                            'image' => $image->image, // Only return the image path
                        ];
                    }),
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Product By Category Retrieved Successfully',
                'data' => $categoryProductTransformed,
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

    public function subCategoryProduct($id)
    {
        try {
            // Fetch the sub-category by ID and check its status and deletion flag
            $sub_category = ProductSubCategory::where('id', $id)->where('deleted', 0)->where('status', 1)->first();

            if (!$sub_category) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Sub Category Not Found',
                        'data' => null,
                    ],
                    404,
                );
            }

            // Fetch products associated with the sub-category
            $subCategory_product = Product::where('subcategory_id', $id)
                ->where('deleted', 0)
                ->where('status', 1)
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Product By Sub Category Retrieved Successfully',
                'data' => ProductResource::collection($subCategory_product),
            ]);
        } catch (\Exception $e) {
            // Log the exception (optional, but recommended)

            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'An error occurred: ' . $e->getMessage(),
                ],
                500,
            ); // Use 500 status code for server errors
        }
    }

    public function homePopularProduct()
    {
        try {
            // Fetch the trending products with pagination
            $popular = Product::with(['productImage', 'childProducts', 'seoMetadata'])
                ->where('is_popular', 1)
                ->where('deleted', 0)
                ->where('status', 1)
                ->get();

            // Transform the popular products to only include necessary fields
            $popularTransformed = $popular->map(function ($product) {
                return [
                    'id' => $product->id,
                    'unit_type' => $product->unit_type,
                    'slug' => $product->slug,
                    'name' => $product->name,
                    'category_id' => $product->category_id,
                    'subcategory_id' => $product->subcategory_id,
                    'code' => $product->code,
                    'color' => $product->color,
                    'size' => $product->size,
                    'current_purchase_cost' => $product->current_purchase_cost,
                    'current_sale_price' => $product->current_sale_price,
                    'available_quantity' => $product->available_quantity,
                    'discount' => $product->discount,
                    'description' => $product->description,
                    'is_popular' => $product->is_popular,
                    'is_trending' => $product->is_trending,
                    'order_limit' => $product->order_limit,
                    'stock_status' => $product->stock_status,
                    'seoMetadata' => $product->seoMetadata,
                    'product_image' => $product->productImage->map(function ($image) {
                        return [
                            'image' => $image->image, // Only return the image path
                        ];
                    }),
                ];
            });

            // Return the products as a collection of ProductResource
            return response()->json([
                'status' => 'success', // Fixed typo from 'suucess' to 'success'
                'message' => 'Popular Products Retrieved Successfully',
                'data' => $popularTransformed,
            ]);
        } catch (\Exception $e) {
            // Return a JSON response indicating the error
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'An error occurred while fetching popular products: ' . $e->getMessage(),
                ],
                500,
            ); // 500 status code for server errors
        }
    }

    public function newArrivalProduct()
    {
        try {
            $newArrival = Product::with(['productImage', 'childProducts', 'seoMetadata'])
                ->orderBy('id', 'DESC')
                ->where('deleted', 0)
                ->where('status', 1)
                ->take(12)
                ->get();

            return response()->json([
                'status' => 'suucess',
                'message' => 'New Arrival Retrieved Successfully',
                'data' => ProductResource::collection($newArrival),
            ]);
        } catch (\Exception $e) {
            // Return a JSON response indicating the error
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'An error occurred while fetching New Arrival  products: ' . $e->getMessage(),
                ],
                500,
            ); // 500 status code for server errors
        }
    }

    public function productDetails($id)
    {
        try {
            // Fetch the product with its related categories and images
            $product = Product::with([
                'productCategory',
                'productImage',
                'childProducts',
                'productSubcategory',
                'productVariants.variant',
                'productVariants.brand',
                'productVariants.variantSizes.size',
                'productLidOptions.lidOption',
                'seoMetadata',
            ])
                ->where('id', $id)
                ->first();

            $activeDiscount = Discount::where('is_active', 1)
                ->whereHas('item', function ($query) use ($product) {
                    $query->where('product_id', $product->id)->where('category_id', $product->category_id);
                })
                ->first();

            // Check if the product was found
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

            // Transform the productVariants to match the required format
            $productVariants = $product->productVariants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'name' => $variant->variant->name,
                    'pack_size' => $variant->variant->pack_size,
                    'price' => $variant->price,
                    'price_per_piece' => $variant->price_per_peice,
                    'brand_id' => $variant->brand_id,
                    'brand' => $variant->brand,
                    'variantSizes' => $variant->variantSizes,
                ];
            });

            $productLidOptions = $product->productLidOptions->map(function ($productLidOption) {
                return [
                    'id' => $productLidOption->id,
                    'name' => $productLidOption->lidOption->name,
                    'price' => $productLidOption->price,
                    'image' => $productLidOption->lidOption->image,
                    'img_alt' => $productLidOption->lidOption->img_alt,
                    'img_name' => $productLidOption->lidOption->img_name,
                ];
            });

            // Fetch recommended products based on the category of the found product
            $recommendedProducts = Product::with([
                'productCategory',
                'productImage',
                'childProducts',
                'productVariants.variant',
                'productVariants.brand',
                'seoMetadata',
            ])
                ->where('category_id', $product->category_id)
                ->where('deleted', 0)
                ->where('status', 1)
                ->where('id', '!=', $product->id) // Exclude the current product
                ->take(5)
                ->get();

            // Transform the recommended products and their variants
            $recommendedProducts = $recommendedProducts->map(function ($recommendedProduct) {
                $recommendedProductVariants = $recommendedProduct->productVariants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'name' => $variant->variant->name,
                        'pack_size' => $variant->variant->pack_size,
                        'price' => $variant->price,
                        'price_per_piece' => $variant->price_per_peice,
                        'brand_id' => $variant->brand_id,
                        'brand' => $variant->brand,
                        'variantSizes' => $variant->variantSizes,
                    ];
                });

                return [
                    'id' => $recommendedProduct->id,
                    'name' => $recommendedProduct->name,
                    'slug' => $recommendedProduct->slug,
                    'category_id' => $recommendedProduct->category_id,
                    'image_path' => $recommendedProduct->image_path,
                    'image_alt' => $recommendedProduct->image_alt,
                    'image_name' => $recommendedProduct->image_name,
                    'code' => $recommendedProduct->code,
                    'price' => $recommendedProduct->current_sale_price,
                    'discount' => $recommendedProduct->discount,
                    'product_image' => $recommendedProduct->productImage,
                    'product_variants' => $recommendedProductVariants, // Include transformed variants
                ];
            });

            // Return the response with the transformed product and recommended products
            return response()->json([
                'status' => 'success',
                'message' => 'Product Details Retrieved Successfully',
                'data' => [
                    'product' => [
                        'id' => $product->id,
                        'unit_type' => $product->unit_type,
                        'is_customizeable' => $product->is_customizeable,
                        'childProducts' => $product->childProducts,
                        'name' => $product->name,
                        'category_id' => $product->category_id,
                        'category' => $product->productCategory ?? 'N/A',
                        'subcategory_id' => $product->subcategory_id,
                        'subCategory' => $product->productSubcategory ?? 'N/A',
                        'image_path' => $product->image_path,
                        'image_alt' => $product->image_alt,
                        'image_name' => $product->image_name,
                        'code' => $product->code,
                        'color' => $product->color,
                        'size' => $product->size,
                        'brand_id' => $product->brand_id,
                        'brand_name' => $product->brand->name ?? 'N/A',
                        'supplier_id' => $product->supplier_id,
                        'current_purchase_cost' => $product->current_purchase_cost,
                        'current_sale_price' => $product->current_sale_price,
                        'current_wholesale_price' => $product->current_wholesale_price,
                        'wholesale_minimum_qty' => $product->wholesale_minimum_qty,
                        'available_quantity' => $product->available_quantity,
                        'order_limit' => $product->order_limit,
                        'stock_status' => $product->stock_status,
                        // 'discount_type' => $product->discount_type,
                        // 'discount' => $product->discount,
                        'description' => $product->description,
                        'product_video_url' => $product->product_video_url,
                        'additional_information' => $product->additional_information,
                        'status' => $product->status,
                        'product_image' => $product->productImage,
                        'product_brands' => $productVariants->pluck('brand')->filter()->unique()->values()->toArray(),
                        'product_variants' => $productVariants, // Include transformed product variants
                        'product_lid_options' => $productLidOptions,
                        'activeDiscount' => $activeDiscount,
                    ],
                    'recommended_products' => $recommendedProducts, // Include recommended products with transformed variants
                    'seoMetadata' => $product->seoMetadata,
                ],
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
    public function productDetailsBySlug(Request $request)
    {
        $slug = $request->input('slug');

        $product = Product::normal()->where('slug', $slug)->first();

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ]);
        }

        return $this->productDetails($product->id);
    }

    public function sectionProductList(Request $request)
    {
        $subcategory_id = $request->subcategory_id;

        if ($request->type == 'trending') {
            return ProductResource::collection(
                Product::when($request->subcategory_id != 'All', function ($q) use ($subcategory_id) {
                    return $q->where('subcategory_id', $subcategory_id);
                })
                    ->where('deleted', 0)
                    ->where('status', 1)
                    ->where('is_trending', 1)
                    ->paginate(12),
            );
        }
        if ($request->type == 'popular') {
            return ProductResource::collection(
                Product::when($request->subcategory_id != 'All', function ($q) use ($subcategory_id) {
                    return $q->where('subcategory_id', $subcategory_id);
                })
                    ->where('deleted', 0)
                    ->where('status', 1)
                    ->where('is_popular', 1)
                    ->paginate(12),
            );
        }
        if ($request->type == 'newArrival') {
            return ProductResource::collection(
                Product::when($request->subcategory_id != 'All', function ($q) use ($subcategory_id) {
                    return $q->where('subcategory_id', $subcategory_id);
                })
                    ->where('deleted', 0)
                    ->where('status', 1)
                    ->orderBy('id', 'DESC')
                    ->paginate(12),
            );
        }
        if ($request->type == 'bestSell') {
            if ($subcategory_id == 'All') {
                return DB::table('sell_details')
                    ->join('products', 'sell_details.product_id', '=', 'products.id')
                    ->select('products.*', DB::raw('SUM(sell_details.sale_quantity) as total_sell'))
                    ->groupBy('sell_details.product_id')
                    ->orderBy('total_sell', 'DESC')
                    ->paginate(12);
            } else {
                return DB::table('sell_details')
                    ->join('products', 'sell_details.product_id', '=', 'products.id')
                    ->select('products.*', DB::raw('SUM(sell_details.sale_quantity) as total_sell'))
                    ->groupBy('sell_details.product_id')
                    ->where('products.subcategory_id', $subcategory_id)
                    ->orderBy('total_sell', 'DESC')
                    ->paginate(12);
            }
        }
    }

    public function relatedProductGet(Request $request)
    {
        $plist = explode(',', $request->productlist);

        $subcategorylist = [];
        $productlist = [$request->productlist];

        foreach ($plist as $key => $productid) {
            $subcategory = Product::where('id', $productid)->first();
            $sub = $subcategory->subcategory_id;
            $subcategorylist[] = $sub;
        }
        //        return response($subcategorylist,200);
        //        return $productlist;

        $productlist = ProductResource::collection(
            Product::whereNotIn('id', $plist)
                ->whereIn('subcategory_id', $subcategorylist)
                ->where('deleted', 0)
                ->where('status', 1)
                ->inRandomOrder()
                ->limit(10)
                ->get(),
        );

        return response($productlist, 200);
    }

    public function minMaxPrice()
    {
        $minPrice = Product::min('current_sale_price');
        $maxPrice = Product::max('current_sale_price');
        $price = ['min' => $minPrice, 'max' => $maxPrice];

        return response($price, 200);
    }

    public function priceRangeSrc(Request $request)
    {
        $min = $request->min;
        $max = $request->max;
        $color = $request->color;
        $size = $request->size;
        $type = $request->type;
        $category = $request->category_id;
        $sub_category = $request->sub_category_id;
        $brand_id = $request->brand_id;
        $srcorderType = $request->srcorderType; /* price_asc price_dsc name_asc name_dsc */

        $product = Product::where('status', 1)
            ->where('deleted', 0)
            ->when($category > 0, function ($q) use ($category) {
                return $q->where('category_id', '=', $category);
            })
            ->when($sub_category > 0, function ($q) use ($sub_category) {
                return $q->where('subcategory_id', '=', $sub_category);
            })
            ->when($brand_id > 0, function ($q) use ($brand_id) {
                return $q->where('brand_id', '=', $brand_id);
            })
            ->when($color != '0', function ($q) use ($color) {
                return $q->where('color', 'like', '%' . $color . '%');
            })
            ->when($size != '0', function ($q) use ($size) {
                return $q->where('size', 'like', '%' . $size . '%');
            })
            ->when($type == 'trending', function ($q) {
                return $q->where('is_trending', 1);
            })
            ->when($type == 'popular', function ($q) {
                return $q->where('is_popular', 1);
            })
            ->when($type == 'newArrival', function ($q) {
                return $q->orderBy('id', 'DESC');
            })
            ->when($min >= 0 && $max > 0, function ($q) use ($min, $max) {
                $q->whereBetween('current_sale_price', [$min, $max]);
            })
            ->when($srcorderType == 'price_asc', function ($q) {
                $q->orderBy('current_sale_price', 'asc');
            })
            ->when($srcorderType == 'price_dsc', function ($q) {
                $q->orderBy('current_sale_price', 'desc');
            })
            ->when($srcorderType == 'name_asc', function ($q) {
                $q->orderBy('name', 'asc');
            })
            ->when($srcorderType == 'name_dsc', function ($q) {
                $q->orderBy('name', 'desc');
            })
            ->get();

        $productList = ProductResource::collection($product);
        return response($productList, 200);
    }

    public function bestSellProduct()
    {
        $best_sell_listt = DB::table('sell_details')
            ->join('products', 'sell_details.product_id', '=', 'products.id')
            ->select('products.*', DB::raw('SUM(sell_details.sale_quantity) as total_sell'))
            ->groupBy('sell_details.product_id')
            ->orderBy('total_sell', 'DESC')
            ->take(12)
            ->get();
        return response($best_sell_listt, 200);
    }

    public function srcProductList(Request $request)
    {
        try {
            $query = Product::with(
                'productImage',
                'childProducts',
                'productVariants.variant',
                'productVariants.brand',
                'productLidOptions.lidOption',
                'seoMetadata',
            )
                ->where('status', 1)
                ->where('deleted', 0);

            //if not search then show normal product agr search hai to isse ignore kro
            if (!($request->has('search') && $request->boolean('search'))) {
                $query->normal();
            }

            // Apply filters based on provided input
            if ($request->has('name') && !empty($request->input('name'))) {
                $query->where('name', 'LIKE', '%' . $request->input('name') . '%');
            }

            if (
                ($request->has('price_from') && !empty($request->input('price_from'))) ||
                ($request->has('price_to') && !empty($request->input('price_to')))
            ) {
                $query->whereHas('productVariants', function ($q) use ($request) {
                    if ($request->has('price_from') && !empty($request->input('price_from'))) {
                        $q->where('price', '>=', $request->input('price_from'));
                    }
                    if ($request->has('price_to') && !empty($request->input('price_to'))) {
                        $q->where('price', '<=', $request->input('price_to'));
                    }
                });
            }


            $category = null;
            if ($request->has('subcategory_id') && !empty($request->input('subcategory_id'))) {
                $subcategory_id = $request->input('subcategory_id');
                $query->where('subcategory_id', $subcategory_id);
            } elseif ($request->has('category_id') && !empty($request->input('category_id'))) {
                $category_id = $request->input('category_id');

                $category = ProductCategory::with('categorySeoMetadata')->find($category_id);

                $query->where(function ($q) use ($category_id) {
                    $q->where('category_id', $category_id)->orWhereHas('productCategory', function ($q) use (
                        $category_id,
                    ) {
                        $q->where('parent_id', $category_id);
                    });
                });
            }

            if ($request->has('pack_size') && !empty($request->input('pack_size'))) {
                $packSize = $request->input('pack_size');
                $query->whereHas('productVariants', function ($q) use ($packSize) {
                    $q->whereHas('variant', function ($q) use ($packSize) {
                        $q->where('pack_size', $packSize);
                    });
                });
            }

            if ($request->has('brand_id') && !empty($request->input('brand_id'))) {
                $brand_id = $request->input('brand_id');
                $query->whereHas('productVariants', function ($q) use ($brand_id) {
                    $q->whereHas('brand', function ($q) use ($brand_id) {
                        $q->where('id', $brand_id);
                    });
                });
            }

            // if ($request->has('sort_by') && !empty($request->input('sort_by'))) {
            //     $sortBy = $request->input('sort_by');
            //     if ($sortBy == 2) {
            //         $query->orderBy('name', 'desc');
            //     } else {
            //         $query->orderBy('name', 'asc');
            //     }
            // }

            if ($request->has('size') && !empty($request->input('size'))) {
                $query->where('size', $request->input('size'));
            }

            $products = $query->orderByRaw('serial_no IS NULL, serial_no ASC')->get();

            $productsTransformed = $products->map(function ($product) {

                $productVariants = $product->productVariants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'name' => $variant->variant->name,
                        'pack_size' => $variant->variant->pack_size,
                        'price' => $variant->price,
                        'price_per_piece' => $variant->price_per_peice,
                        'brand_id' => $variant->brand_id,
                        'brand' => $variant->brand,
                        'variantSizes' => $variant->variantSizes,
                    ];
                });

                $productLidOptions = $product->productLidOptions->map(function ($productLidOption) {
                    return [
                        'id' => $productLidOption->id,
                        'name' => $productLidOption->lidOption?->name,
                        'price' => $productLidOption->price,
                        'image' => $productLidOption->lidOption?->image,
                        'img_alt' => $productLidOption->lidOption?->img_alt,
                        'img_name' => $productLidOption->lidOption?->img_name,
                    ];
                });

                // Return the product with formatted productVariants
                return [
                    'id' => $product->id,
                    'unit_type' => $product->unit_type,
                    'slug' => $product->slug,
                    'name' => $product->name,
                    'category_id' => $product->category_id,
                    'category' => $product->productCategory,
                    'categorySeoDetail' => $product->categorySeoDetail,
                    'current_sale_price' => $product->current_sale_price,
                    'product_image' => $product->productImage,
                    'product_brands' => $productVariants->pluck('brand')->filter()->unique()->values()->toArray(),
                    'product_variants' => $productVariants, // Include transformed product variants
                    'product_lid_options' => $productLidOptions,
                    'seo_metadata' => $product->seoMetadata,
                    'is_customizeable' => $product->is_customizeable,
                    'childProducts' => $product->childProducts,
                ];
            });

            $responseData = [
                'status' => 'success',
                'message' => 'Products Retrieved successfully',
                'data' => $productsTransformed, // Return transformed products with formatted variants
            ];

            if ($category) {
                $responseData['category'] = $category;
            }

            return response()->json($responseData, 200);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => $th->getMessage(),
                    'data' => null,
                ],
                500,
            );
        }
    }

    public function productSizList(Request $request)
    {
        $productSizList = Product::select('size')->whereNotNull('size')->where('status', 1)->where('deleted', 0)->get();
        return response()->json($productSizList);
    }
    public function productColorList(Request $request)
    {
        $productSizList = Product::select('color')
            ->whereNotNull('color')
            ->where('status', 1)
            ->where('deleted', 0)
            ->get();
        return response()->json($productSizList);
    }

    public function allColor()
    {
        $allColor = ProductColor::get();
        return response()->json($allColor);
    }

    public function allSize()
    {
        $allSize = ProductSize::get();
        return response()->json([
            'status' => 'success',
            'message' => 'All Size',
            'data' => $allSize,
        ]);
    }

    public function allOptions()
    {
        $alloptions = Option::get();
        return response()->json([
            'status' => 'success',
            'message' => 'All Options',
            'data' => $alloptions,
        ]);
    }

    public function allvariantss()
    {
        $alloptions = Variants::get();
        return response()->json([
            'status' => 'success',
            'message' => 'All Variants',
            'data' => $alloptions,
        ]);
    }

    public function review_add(Request $request)
    {
        $filePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('review_images', $fileName, 'public'); // Store the file
        }

        $data = $request->all();

        // Set the image only if a file was uploaded
        if ($filePath) {
            $data['image'] = $filePath;
        }
        if ($request->user_type == 1) {
            $data['user_id'] = 0;
        }
        $reviewsDetail = Review::create($data);
        $success = [
            'status' => 200,
            'message' => 'Successfully Created',
            'data' => $reviewsDetail,
        ];

        return response()->json($success);
    }

    public function all_reviews(Request $request)
    {
        $filter = $request->input('filter', 'mixed');

        // Fetch reviews based on the filter
        if ($filter === 'older') {
            $filteredReviews = Review::withCount([
                'likes as likes_count' => function ($query) {
                    $query->where('is_like', 1);
                },
                'likes as dislikes_count' => function ($query) {
                    $query->where('is_like', 0);
                },
            ])
                ->with([
                    'user' => function ($query) {
                        $query->select('id', 'photo'); // Select only the 'id' and 'photo' columns
                    },
                ])
                ->orderBy('created_at', 'asc')
                ->get(); // Older reviews first
        } elseif ($filter === 'recent') {
            $filteredReviews = Review::withCount([
                'likes as likes_count' => function ($query) {
                    $query->where('is_like', 1);
                },
                'likes as dislikes_count' => function ($query) {
                    $query->where('is_like', 0);
                },
            ])
                ->with([
                    'user' => function ($query) {
                        $query->select('id', 'photo'); // Select only the 'id' and 'photo' columns
                    },
                ])
                ->orderBy('created_at', 'desc')
                ->get(); // Most recent reviews first
        } else {
            // Default case: 'mixed' - Randomize the order of reviews
            $filteredReviews = Review::withCount([
                'likes as likes_count' => function ($query) {
                    $query->where('is_like', 1);
                },
                'likes as dislikes_count' => function ($query) {
                    $query->where('is_like', 0);
                },
            ])
                ->with([
                    'user' => function ($query) {
                        $query->select('id', 'photo'); // Select only the 'id' and 'photo' columns
                    },
                ])
                ->inRandomOrder()
                ->get();
        }

        // Count total filtered reviews
        $totalFilteredReviewsCount = $filteredReviews->count();

        // Count reviews by ratings
        $ratingCounts = [
            '5.000' => $filteredReviews->where('rating', 5.0)->count(),
            '4.000' => $filteredReviews->where('rating', 4.0)->count(),
            '3.000' => $filteredReviews->where('rating', 3.0)->count(),
            '2.000' => $filteredReviews->where('rating', 2.0)->count(),
            '1.000' => $filteredReviews->where('rating', 1.0)->count(),
        ];

        // Calculate the average rating for the filtered reviews
        $averageRating = $totalFilteredReviewsCount > 0 ? $filteredReviews->avg('rating') : 0; // If there are no reviews, set average to 0

        return response()->json([
            'status' => 'success',
            'message' =>
            $filter === 'older'
                ? 'Older Reviews List'
                : ($filter === 'recent'
                    ? 'Most Recent Reviews List'
                    : 'Mixed Reviews List'),
            'total_reviews_count' => $totalFilteredReviewsCount,
            'rating_counts' => $ratingCounts,
            'average_rating' => $averageRating,
            'data' => $filteredReviews,
        ]);
    }

    public function productReviewBySlug(Request $request)
    {
        $slug = $request->slug;
        $product = Product::where('slug', $slug)->first();

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ]);
        }

        return $this->product_review($product->id);
    }

    public function product_review($productId)
    {
        // Find the product by ID
        $product = Product::find($productId);

        // Check if the product exists
        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ]);
        }

        // Get all reviews for the product with like and dislike counts
        $productReviews = Review::withCount([
            'likes as likes_count' => function ($query) {
                $query->where('is_like', 1); // Count likes
            },
            'likes as dislikes_count' => function ($query) {
                $query->where('is_like', 0); // Count dislikes
            },
        ])
            ->with([
                'user' => function ($query) {
                    $query->select('id', 'photo'); // Select only the 'id' and 'photo' columns
                },
            ])
            ->where('product_id', $productId)
            ->get();

        // Count total reviews
        $totalReviewsCount = $productReviews->count();

        // Count reviews by rating
        $ratingCounts = [
            '5.000' => $productReviews->where('rating', 5.0)->count(),
            '4.000' => $productReviews->where('rating', 4.0)->count(),
            '3.000' => $productReviews->where('rating', 3.0)->count(),
            '2.000' => $productReviews->where('rating', 2.0)->count(),
            '1.000' => $productReviews->where('rating', 1.0)->count(),
        ];

        // Calculate the average rating
        $averageRating = $totalReviewsCount > 0 ? $productReviews->avg('rating') : 0; // If there are no reviews, set average to 0

        return response()->json([
            'status' => 'success',
            'message' => 'Product Reviews List',
            'total_reviews' => $totalReviewsCount,
            'rating_counts' => $ratingCounts,
            'average_rating' => number_format($averageRating, 3), // Format to 3 decimal places
            'data' => $productReviews, // Include likes and dislikes in data
        ]);
    }

    public function user_wise_reviews(Request $request)
    {
        $user_id = $request->input('user_id');
        $userWiseReviews = Review::where('user_id', $user_id)->get();
        return response()->json([
            'status' => 'suucess',
            'message' => 'User Wise Reviews List',
            'data' => $userWiseReviews,
        ]);
    }

    public function likeOrDislike(Request $request, $reviewId)
    {
        // Validate input (is_like should be 1 for like, 0 for dislike)
        $validated = $request->validate([
            'is_like' => 'required|boolean',
        ]);

        $userId = Auth::id();

        // Find the review
        $review = Review::findOrFail($reviewId);

        // Check if the user has already liked/disliked the review
        $existingLike = ReviewLike::where('review_id', $reviewId)->where('user_id', $userId)->first();

        if ($existingLike) {
            // If like/dislike already exists, update it
            $existingLike->is_like = $validated['is_like'];
            $existingLike->save();
        } else {
            // Otherwise, create a new like/dislike entry
            ReviewLike::create([
                'review_id' => $review->id,
                'user_id' => $userId,
                'is_like' => $validated['is_like'],
            ]);
        }

        // Return the updated like/dislike count
        $likesCount = ReviewLike::where('review_id', $reviewId)->where('is_like', 1)->count();
        $dislikesCount = ReviewLike::where('review_id', $reviewId)->where('is_like', 0)->count();

        return response()->json([
            'status' => 'success',
            'message' => 'Review like/dislike updated successfully',
            'likes_count' => $likesCount,
            'dislikes_count' => $dislikesCount,
        ]);
    }

    // Get the total likes and dislikes for a review
    public function getLikesAndDislikes($reviewId)
    {
        $review = Review::findOrFail($reviewId);

        $likesCount = ReviewLike::where('review_id', $reviewId)->where('is_like', 1)->count();
        $dislikesCount = ReviewLike::where('review_id', $reviewId)->where('is_like', 0)->count();

        return response()->json([
            'status' => 'success',
            'message' => 'Likes and dislikes retrieved successfully',
            'likes' => $likesCount,
            'dislikes' => $dislikesCount,
        ]);
    }

    public function srcCustomizeProductList(Request $request)
    {
        try {
            // Start with the base query, exclude 'productReviews' from being loaded
            $query = Product::customizeable()->with(
                'productImage',
                'productVariants.variant',
                'productVariants.brand',
                'productOptions',
                'packagingOptions',
                'seoMetadata',
            )
                ->where('status', 1)
                ->where('deleted', 0);

            // Apply filters based on provided input
            if ($request->has('name') && !empty($request->input('name'))) {
                $query->where('name', 'LIKE', '%' . $request->input('name') . '%');
            }

            if ($request->has('price_from') && !empty($request->input('price_from'))) {
                $query->where('current_sale_price', '>=', $request->input('price_from'));
            }

            if ($request->has('price_to') && !empty($request->input('price_to'))) {
                $query->where('current_sale_price', '<=', $request->input('price_to'));
            }

            if ($request->filled('category_id') && is_array($request->input('category_id'))) {
                $query->whereIn('category_id', $request->input('category_id'));
            }

            // Filter by Size
            if ($request->has('size_id') && !empty($request->input('size_id'))) {
                $query->whereHas('productOptions', function ($query) use ($request) {
                    $query->where('size_id', $request->input('size_id'));
                });
            }

            // Filter by Option
            if ($request->has('option_id') && !empty($request->input('option_id'))) {
                $query->whereHas('productOptions', function ($query) use ($request) {
                    $query->where('option_id', $request->input('option_id'));
                });
            }

            // Filter by Rating
            if ($request->has('rating') && !empty($request->input('rating'))) {
                $inputRating = $request->input('rating');

                // Filter by average rating based on product reviews
                $query->whereHas('productReviews', function ($q) use ($inputRating) {
                    $q->selectRaw('AVG(rating) as avg_rating, product_id')
                        ->groupBy('product_id')
                        ->havingRaw('ROUND(AVG(rating)) = ?', [$inputRating]);
                });
            }

            if ($request->has('pack_size') && !empty($request->input('pack_size'))) {
                $packSize = $request->input('pack_size');
                $query->whereHas('productVariants', function ($q) use ($packSize) {
                    $q->whereHas('variant', function ($q) use ($packSize) {
                        $q->where('pack_size', $packSize);
                    });
                });
            }

            if ($request->has('brand_id') && !empty($request->input('brand_id'))) {
                $brand_id = $request->input('brand_id');
                $query->whereHas('productVariants', function ($q) use ($brand_id) {
                    $q->whereHas('brand', function ($q) use ($brand_id) {
                        $q->where('brand', $brand_id);
                    });
                });
            }

            // Apply sorting
            if ($request->has('sort_by') && !empty($request->input('sort_by'))) {
                $sortBy = $request->input('sort_by');
                if ($sortBy == 2) {
                    $query->orderBy('name', 'desc');
                } else {
                    $query->orderBy('name', 'asc');
                }
            }

            // Execute the query and get results
            $products = $query->orderByRaw('serial_no IS NULL, serial_no ASC')->get();

            // Map over each product to transform productVariants and calculate ratings
            $productsTransformed = $products->map(function ($product) {
                // Calculate average rating
                if ($product->productReviews->count() > 0) {
                    $averageRating = $product->productReviews->avg('rating');
                    $averageRating = round($averageRating, 1); // Round to 1 decimal place
                } else {
                    $averageRating = null;
                }

                // Transform the productVariants to match the required format
                $productVariants = $product->productVariants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'name' => $variant->variant->name,
                        'pack_size' => $variant->variant->pack_size,
                        'price' => $variant->price,
                        'price_per_piece' => $variant->price_per_peice,
                        'brand_id' => $variant->brand_id,
                        'brand' => $variant->brand,
                        'variantSizes' => $variant->variantSizes,
                    ];
                });

                $productLidOptions = $product->productLidOptions->map(function ($productLidOption) {
                    return [
                        'id' => $productLidOption->id,
                        'name' => $productLidOption->lidOption->name,
                        'price' => $productLidOption->price,
                        'image' => $productLidOption->lidOption->image,
                        'img_alt' => $productLidOption->lidOption->img_alt,
                        'img_name' => $productLidOption->lidOption->img_name,
                    ];
                });

                // Return a transformed product array
                return [
                    'id' => $product->id,
                    'unit_type' => $product->unit_type,
                    'slug' => $product->slug,
                    'name' => $product->name,
                    'category_id' => $product->category_id,
                    'current_sale_price' => $product->current_sale_price,
                    'product_image' => $product->productImage,
                    'is_customizeable' => $product->is_customizeable,
                    'average_rating' => $averageRating, // Include the average rating
                    'product_brands' => $productVariants->pluck('brand')->filter()->unique()->values()->toArray(),
                    'product_variants' => $productVariants, // Include transformed product variants
                    'product_lid_options' => $productLidOptions,
                ];
            });

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Products Retrieved successfully',
                    'data' => $productsTransformed, // Return transformed products
                ],
                200,
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => $th->getMessage(),
                    'data' => null,
                ],
                500,
            );
        }
    }

    public function customizeproductDetails($id)
    {
        try {
            // Fetch the product with its related categories, images, variants, and options
            $product = Product::customizeable()->with([
                'productCategory',
                'productSubCategory',
                'productImage',
                'childProducts',
                'productVariants.variant',
                'productVariants.brand',
                'productOptions.size',
                'productOptions.option',
                'packagingOptions',
                'seoMetadata',
            ])
                ->where('id', $id)
                ->first();

            // Check if the product was found
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

            // Transform the productVariants to match the required format
            $productVariants = $product->productVariants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'name' => $variant->variant->name,
                    'pack_size' => $variant->variant->pack_size,
                    'price' => $variant->price,
                    'price_per_piece' => $variant->price_per_peice,
                    'brand_id' => $variant->brand_id,
                    'brand' => $variant->brand,
                    'variantSizes' => $variant->variantSizes,
                ];
            });

            $productLidOptions = $product->productLidOptions->map(function ($productLidOption) {
                return [
                    'id' => $productLidOption->id,
                    'name' => $productLidOption->lidOption->name,
                    'price' => $productLidOption->price,
                    'image' => $productLidOption->lidOption->image,
                    'img_alt' => $productLidOption->lidOption->img_alt,
                    'img_name' => $productLidOption->lidOption->img_name,
                ];
            });

            // Transform the productOptions to match the required format
            $productOptions = $product->productOptions->map(function ($option) {
                return [
                    'id' => $option->id,
                    'options_price' => $option->options_price,
                    'size' => $option->size->size, // Access the size name
                    'option' => $option->option->name, // Access the option name
                ];
            });

            $activeDiscount = Discount::where('is_active', 1)
                ->whereHas('item', function ($query) use ($product) {
                    $query->where('product_id', $product->id)->where('category_id', $product->category_id);
                })
                ->first();

            // Fetch recommended products based on the category of the found product
            $recommendedProducts = Product::with([
                'productCategory',
                'productImage',
                'childProducts',
                'productVariants.variant',
                'productVariants.brand',
                'seoMetadata',
            ])
                ->where('category_id', $product->category_id)
                ->where('deleted', 0)
                ->where('status', 1)
                ->where('id', '!=', $product->id) // Exclude the current product
                ->take(5)
                ->get();

            // Transform the recommended products and their variants
            $recommendedProducts = $recommendedProducts->map(function ($recommendedProduct) {
                $recommendedProductVariants = $recommendedProduct->productVariants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'name' => $variant->variant->name,
                        'pack_size' => $variant->variant->pack_size,
                        'price' => $variant->price,
                        'price_per_piece' => $variant->price_per_peice,
                        'brand_id' => $variant->brand_id,
                        'brand' => $variant->brand,
                        'variantSizes' => $variant->variantSizes,
                    ];
                });

                return [
                    'id' => $recommendedProduct->id,
                    'slug' => $recommendedProduct->slug,
                    'name' => $recommendedProduct->name,
                    'category_id' => $recommendedProduct->category_id,
                    'image_path' => $recommendedProduct->image_path,
                    'image_alt' => $recommendedProduct->image_alt,
                    'image_name' => $recommendedProduct->image_name,
                    'code' => $recommendedProduct->code,
                    'price' => $recommendedProduct->current_sale_price,
                    'discount' => $recommendedProduct->discount,
                    'product_image' => $recommendedProduct->productImage,
                    'product_variants' => $recommendedProductVariants, // Include transformed variants
                ];
            });

            // Return the response with the transformed product variants and options
            return response()->json([
                'status' => 'success',
                'message' => 'Product Details Retrieved Successfully',
                'data' => [
                    'product' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'unit_type' => $product->unit_type,
                        'slug' => $product->slug,
                        'category_id' => $product->category_id,
                        'category' => $product->productCategory,
                        'subcategory_id' => $product->subcategory_id,
                        'subCategory' => $product->productSubCategory,
                        'image_path' => $product->image_path,
                        'image_alt' => $product->image_alt,
                        'image_name' => $product->image_name,
                        'code' => $product->code,
                        'color' => $product->color,
                        'size' => $product->size,
                        'brand_id' => $product->brand_id,
                        'brand_name' => $product->brand->name ?? null,
                        'supplier_id' => $product->supplier_id,
                        'current_purchase_cost' => $product->current_purchase_cost,
                        'current_sale_price' => $product->current_sale_price,
                        'current_wholesale_price' => $product->current_wholesale_price,
                        'wholesale_minimum_qty' => $product->wholesale_minimum_qty,
                        'available_quantity' => $product->available_quantity,
                        'discount_type' => $product->discount_type,
                        'discount' => $product->discount,
                        'product_video_url' => $product->product_video_url,
                        'additional_information' => $product->additional_information,
                        'description' => $product->description,
                        'order_limit' => $product->order_limit,
                        'stock_status' => $product->stock_status,
                        'status' => $product->status,
                        'product_image' => $product->productImage,
                        'childProducts' => $product->childProducts,
                        'product_brands' => $productVariants->pluck('brand')->filter()->unique()->values()->toArray(),
                        'product_variants' => $productVariants, // Include transformed product variants
                        'product_lid_options' => $productLidOptions,
                        'product_options' => $productOptions, // Include transformed product options
                        'packaging_options' => $product->packagingOptions, // Include transformed product options
                        'activeDiscount' => $activeDiscount,
                    ],
                    'recommended_products' => $recommendedProducts,
                ],
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

    public function customizeproductDetailsBySlug(Request $request)
    {
        try {
            $slug = $request->input('slug');
            $product = Product::customizeable()->where('slug', $slug)->first();

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

            return $this->customizeproductDetails($product->id);
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


    //Global Product Search

    public function globalProductSearch(Request $request)
    {
        try {
            $query = Product::query()
                ->activeAndNotDeleted()
                ->with([
                    'productImage',
                    'productVariants.variant',
                    'productVariants.brand',
                    'productLidOptions.lidOption',
                    'seoMetadata',
                ]);

            // Name filter
            if ($request->filled('name')) {
                $query->where('name', 'LIKE', '%' . $request->input('name') . '%');
            }

            // Price filter via variants
            if ($request->filled('price_from') || $request->filled('price_to')) {
                $query->whereHas('productVariants', function ($q) use ($request) {
                    if ($request->filled('price_from')) {
                        $q->where('price', '>=', $request->input('price_from'));
                    }
                    if ($request->filled('price_to')) {
                        $q->where('price', '<=', $request->input('price_to'));
                    }
                });
            }

            // Category/Subcategory filter
            $category = null;
            if ($request->filled('subcategory_id')) {
                $query->where('subcategory_id', $request->input('subcategory_id'));
            } elseif ($request->filled('category_id')) {
                $categoryId = $request->input('category_id');
                $category = ProductCategory::with('categorySeoMetadata')->find($categoryId);

                $query->where(function ($q) use ($categoryId) {
                    $q->where('category_id', $categoryId)
                        ->orWhereHas('productCategory', fn($q) => $q->where('parent_id', $categoryId));
                });
            }

            // Pack size filter
            if ($request->filled('pack_size')) {
                $query->whereHas('productVariants.variant', function ($q) use ($request) {
                    $q->where('pack_size', $request->input('pack_size'));
                });
            }

            // Brand filter
            if ($request->filled('brand_id')) {
                $query->whereHas('productVariants.brand', function ($q) use ($request) {
                    $q->where('id', $request->input('brand_id'));
                });
            }

            // Size filter
            if ($request->filled('size')) {
                $query->where('size', $request->input('size'));
            }

            // Sorting
            if ($request->filled('sort_by')) {
                $query->orderBy('name', $request->input('sort_by') == 2 ? 'desc' : 'asc');
            }

            // Fetch products
            $products = $query->get();

            // Transform products
            $productsTransformed = $products->map(function ($product) {
                $productVariants = $product->productVariants->map(fn($variant) => [
                    'id' => $variant->id,
                    'name' => $variant->variant->name,
                    'pack_size' => $variant->variant->pack_size,
                    'price' => $variant->price,
                    'price_per_piece' => $variant->price_per_peice,
                    'brand_id' => $variant->brand_id,
                    'brand' => $variant->brand,
                    'variantSizes' => $variant->variantSizes,
                ]);

                $productLidOptions = $product->productLidOptions->map(fn($option) => [
                    'id' => $option->id,
                    'name' => $option->lidOption->name,
                    'price' => $option->price,
                    'image' => $option->lidOption->image,
                    'img_alt' => $option->lidOption->img_alt,
                    'img_name' => $option->lidOption->img_name,
                ]);

                return [
                    'id' => $product->id,
                    'unit_type' => $product->unit_type,
                    'slug' => $product->slug,
                    'name' => $product->name,
                    'category_id' => $product->category_id,
                    'category' => $product->productCategory,
                    'categorySeoDetail' => $product->categorySeoDetail,
                    'current_sale_price' => $product->current_sale_price,
                    'product_image' => $product->productImage,
                    'product_brands' => $productVariants->pluck('brand')->filter()->unique()->values()->toArray(),
                    'product_variants' => $productVariants,
                    'product_lid_options' => $productLidOptions,
                    'seo_metadata' => $product->seoMetadata,
                    'is_customizeable' => $product->is_customizeable,
                ];
            });

            // Prepare response
            $responseData = [
                'status' => 'success',
                'message' => 'Products Retrieved successfully',
                'data' => $productsTransformed,
            ];

            if ($category) {
                $responseData['category'] = $category;
            }

            return response()->json($responseData, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    // public function categoryProducts(Request $request, $type, $slug)
    // {
    //     try {
    //         // Get category
    //         $category = ProductCategory::where('slug', $slug . '/')
    //             ->with('categorySeoMetadata')
    //             ->firstOrFail();

    //         // Start query with relationships
    //         $query = Product::query()
    //             ->activeAndNotDeleted()
    //             ->with([
    //                 'productImage',
    //                 'productVariants.variant',
    //                 'productVariants.brand',
    //                 'productLidOptions.lidOption',
    //                 'seoMetadata',
    //                 'productCategory',
    //                 'categorySeoDetail',
    //             ]);

    //         // Apply customization filter based on route type
    //         if ($type === 'customization') {
    //             $query->customizeable();
    //         } elseif ($type === 'shop') {
    //             $query->normal();
    //         }


    //         // Name filter
    //         if ($request->filled('name')) {
    //             $query->where('name', 'LIKE', '%' . $request->input('name') . '%');
    //         }

    //         // Price filter via variants
    //         if ($request->filled('price_from') || $request->filled('price_to')) {
    //             $query->whereHas('productVariants', function ($q) use ($request) {
    //                 if ($request->filled('price_from')) {
    //                     $q->where('price', '>=', $request->input('price_from'));
    //                 }
    //                 if ($request->filled('price_to')) {
    //                     $q->where('price', '<=', $request->input('price_to'));
    //                 }
    //             });
    //         }

    //         // Pack size filter
    //         if ($request->filled('pack_size')) {
    //             $query->whereHas('productVariants.variant', function ($q) use ($request) {
    //                 $q->where('pack_size', $request->input('pack_size'));
    //             });
    //         }

    //         // Brand filter
    //         if ($request->filled('brand_id')) {
    //             $query->whereHas('productVariants.brand', function ($q) use ($request) {
    //                 $q->where('id', $request->input('brand_id'));
    //             });
    //         }

    //         // Size filter
    //         if ($request->filled('size')) {
    //             $query->where('size', $request->input('size'));
    //         }

    //         // Sorting
    //         if ($request->filled('sort_by')) {
    //             $query->orderBy('name', $request->input('sort_by') == 2 ? 'desc' : 'asc');
    //         }

    //         // Fetch products
    //         $products = $query->get();

    //         // Transform data for response
    //         $productsTransformed = $products->map(function ($product) {
    //             $productVariants = $product->productVariants->map(fn($variant) => [
    //                 'id' => $variant->id,
    //                 'pack_size' => $variant->variant->pack_size,
    //                 'price' => $variant->price,
    //                 'price_per_piece' => $variant->price_per_peice,
    //                 'brand_id' => $variant->brand_id,
    //                 'brand' => $variant->brand,
    //             ]);

    //             $productLidOptions = $product->productLidOptions->map(fn($option) => [
    //                 'id' => $option->id,
    //                 'name' => $option->lidOption->name,
    //                 'price' => $option->price,
    //                 'image' => $option->lidOption->image,
    //                 'img_alt' => $option->lidOption->img_alt,
    //                 'img_name' => $option->lidOption->img_name,
    //             ]);

    //             return [
    //                 'id' => $product->id,
    //                 'unit_type' => $product->unit_type,
    //                 'slug' => $product->slug,
    //                 'name' => $product->name,
    //                 'category_id' => $product->category_id,
    //                 'category' => $product->productCategory,
    //                 'categorySeoDetail' => $product->categorySeoDetail,
    //                 'current_sale_price' => $product->current_sale_price,
    //                 'product_image' => $product->productImage,
    //                 'product_brands' => $productVariants->pluck('brand')->filter()->unique()->values()->toArray(),
    //                 'product_variants' => $productVariants,
    //                 'product_lid_options' => $productLidOptions,
    //                 'seo_metadata' => $product->seoMetadata,
    //                 'is_customizeable' => $product->is_customizeable,
    //             ];
    //         });

    //         // Prepare response
    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Products retrieved successfully',
    //             'data' => $productsTransformed,
    //             'category' => $category,
    //         ], 200);
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $th->getMessage(),
    //             'data' => null,
    //         ], 500);
    //     }
    // }
}
