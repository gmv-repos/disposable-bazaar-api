<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductColor;
use App\Models\ProductImage;
use App\Models\ProductSize;
use App\Exports\ProductsExport;
use App\Models\ProductSubCategory;
use App\Models\ProductVariant;
use App\Models\ProductOption;
use App\Models\Option;
use App\Models\Variants;
use App\Models\ProductVariantSize;
use App\Models\Sell;
use App\Models\Supplier;
use App\Models\ProductSeoMetadata;
use Carbon\Carbon;
use Google\Service\AndroidPublisher\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use PhpParser\Node\Expr\Array_;
use PDF;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Models\LidOption;
use App\Models\ProductLidOption;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductPriceUpdate;
use App\Imports\ProductsImport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\DB;
use App\Models\ProductPackagingOption;

class ProductController extends Controller
{
    public function productList()
    {
        $common_data = new Array_();
        $common_data->title = 'Product List';

        $productList = Product::orderBy('id', 'desc')->get();

        $productCategory = ProductCategory::where('status', 1)->where('deleted', 0)->get();
        $supplierList = Supplier::where('status', 1)->where('deleted', 0)->get();
        return view('adminPanel.product.product_list')->with(
            compact('common_data', 'productList', 'productCategory', 'supplierList'),
        );
    }

    public function ProductOrderDetails(Request $request)
    {
        $product = Product::with([
            'productCategory',
            'productImage',
            'productVariants.variant',
            'productOptions.size',
            'productOptions.option',
        ])->find($request->product_id);

        Log::info('Product Details Data: ' . json_encode($product));

        return view('adminPanel.product._product_detail')->with(compact('product'))->render();
    }

    public function ProductEdit(Request $request)
    {
        $productInfo = Product::with([
            'productCategory',
            'productImage',
            'productVariants.variant',
            'productVariants.variantSizes.size',
            'productOptions.size',
            'productOptions.option',
            'packagingOptions',
            'seoMetadata',
        ])->find($request->product_id);
        Log::info('Product Final Edit Data: ' . json_encode($productInfo));
        $productCategory = ProductCategory::where('status', 1)->where('deleted', 0)->get();
        $productSubcategory = ProductSubCategory::where('category_id', $productInfo->category_id)
            ->where('status', 1)
            ->where('deleted', 0)
            ->get();
        $supplierList = Supplier::where('status', 1)->where('deleted', 0)->get();
        $brand = Brand::get();
        $color = ProductColor::get();
        $sizes = ProductSize::get();
        $coloroptions = Option::get();
        $variants = Variants::get();
        $productLidOptions = ProductLidOption::where('product_id', $request->product_id)->get();
        $lidOptions = LidOption::get();
        $parentProducts = Product::all();
        $productSeoMetadata = $productInfo->seoMetadata;
        return view('adminPanel.product._edit_product')
            ->with(
                compact(
                    'productInfo',
                    'supplierList',
                    'productCategory',
                    'productSubcategory',
                    'supplierList',
                    'brand',
                    'color',
                    'sizes',
                    'coloroptions',
                    'variants',
                    'productSeoMetadata',
                    'lidOptions',
                    'productLidOptions',
                    'parentProducts',
                ),
            )
            ->render();
    }
    public function productEditDetails(Request $request)
    {
        Log::info(['Products Edit Data', $productInfo]);
        Log::info(['Products Option Data', $productInfo->productOptions]);
        Log::info(['Products Variants Data', $productInfo->productVariants]);
        Log::info(['Products Images Data', $productInfo->productImage]);

        return view('adminPanel.product._edit_product')
            ->with(
                compact(
                    'productInfo',
                    'supplierList',
                    'productCategory',
                    'productSubcategory',
                    'supplierList',
                    'brand',
                    'color',
                    'sizes',
                    'options',
                    'variants',
                ),
            )
            ->render();
    }
    public function productDelete(Request $request)
    {
        $product = Product::find($request->id);
        $product->deleted = 1;
        $product->save();
        return redirect()->back()->with('success', 'Product Successfully Deleted');
    }

    public function productRestore(Request $request)
    {
        $product = Product::find($request->id);
        $product->deleted = 0;
        $product->save();
        return redirect()->back()->with('success', 'Product Successfully Restore');
    }

    public function productOption()
    {
        $common_data = new Array_();
        $common_data->title = 'Add Option';
        $productOption = Option::get();
        return view('adminPanel.product_options.product_option')->with(compact('common_data', 'productOption'));
    }
    public function productOptionStore(Request $request)
    {
        $sizelist = explode(',', $request->name);
        foreach ($sizelist as $size) {
            $productsize = new Option();
            $productsize->name = $size;
            $productsize->save();
        }
        return redirect()->back()->with('success', 'Product Option Successfully Created');
    }

    public function createProduct(Request $request)
    {
        $common_data = new Array_();
        $common_data->title = '';
        $productCategory = ProductCategory::where('status', 1)->where('deleted', 0)->get();
        $supplierList = Supplier::where('status', 1)->where('deleted', 0)->get();
        $brand = Brand::get();
        $color = ProductColor::get();
        $sizes = ProductSize::get();
        $options = Option::get();
        $variants = Variants::get();
        $lidOptions = LidOption::all();

        $parentProducts = Product::all();

        return view('adminPanel.product.create_product')->with(
            compact(
                'productCategory',
                'supplierList',
                'common_data',
                'brand',
                'color',
                'sizes',
                'options',
                'variants',
                'lidOptions',
                'parentProducts',
            ),
        );
    }

    public function productSizeUpdate(Request $request)
    {
        $productSize = ProductSize::find($request->id);
        $productSize->size = $request->size;
        $productSize->save();
        return redirect()->back()->with('success', 'Product Size Successfully Updated');
    }
    public function productColorUpdate(Request $request)
    {
        $productColor = ProductColor::find($request->id);
        $productColor->name = $request->name;
        $productColor->color_code = $request->color_code;
        $productColor->save();
        return redirect()->back()->with('success', 'Product Size Successfully Updated');
    }

    public function productColor()
    {
        $common_data = new Array_();
        $common_data->title = 'Add Color';
        $productColor = ProductColor::get();
        return view('adminPanel.product_color.product_color_list')->with(compact('common_data', 'productColor'));
    }

    public function productColorStore(Request $request)
    {
        $productcolor = new ProductColor();
        $productcolor->name = $request->name;
        $productcolor->color_code = $request->color_code;
        $productcolor->save();
        return redirect()->back()->with('success', 'Product Color Successfully Created');
    }

    public function productSize()
    {
        $common_data = new Array_();
        $common_data->title = 'Add Size';
        $productSize = ProductSize::get();
        return view('adminPanel.product_size.product_size')->with(compact('common_data', 'productSize'));
    }
    public function productSizeStore(Request $request)
    {
        $sizelist = explode(',', $request->size);
        foreach ($sizelist as $size) {
            $productsize = new ProductSize();
            $productsize->size = $size;
            $productsize->save();
        }
        return redirect()->back()->with('success', 'Product Size Successfully Created');
    }

    public function storeProduct(Request $request)
    {

        DB::beginTransaction();

        try {
            $product = new Product();
            $product->serial_no = $request->serial_no;
            $product->parent_product_id = $request->parent_product_id;
            $product->name = $request->name;
            $product->no_of_piece_qty_in_carton = $request->no_of_piece_qty_in_carton;
            $product->slug = $request->slug;
            $product->category_id = $request->category_id;
            $product->subcategory_id = 0; // Modify if subcategory is provided
            $product->color = is_array($request->color) ? implode(',', $request->color) : '';
            $product->size = implode(',', $request->size ?? []);
            $product->brand_id = $request->brand_id;
            $product->supplier_id = $request->supplier_id;
            $product->current_purchase_cost = $request->current_purchase_cost ?? $request->current_sale_price;
            $product->current_sale_price = $request->current_sale_price;
            $product->current_wholesale_price = $request->current_wholesale_price;
            $product->wholesale_minimum_qty = $request->wholesale_minimum_qty;
            $product->available_quantity = $request->available_quantity;
            $product->discount_type = $request->discount_type;
            $product->discount = $request->discount;
            $product->unit_type = $request->unit_type;
            $product->description = $request->description;
            $product->product_video_url = $request->product_video_url;
            $product->additional_information = $request->additional_information;
            $product->is_popular = $request->is_popular ? 1 : 0;
            $product->is_trending = $request->is_trending ? 1 : 0;
            $product->stock_alert = $request->stock_alert;
            $product->order_limit = $request->order_limit;
            $product->code = $request->sku_code;
            $product->is_customizeable = $request->is_customizeable ? 1 : 0;

            // Handle image upload

            $image = null;
            if ($request->has('product_img')) {
                $image = $request->product_img[0];
            }

            if ($image) {
                $product->image_path = $this->productImageSave($image);
                $product->image_alt = $request->product_img_alt[0];
                $product->image_name = $request->product_img_name[0];
            }

            // Set created_at and save the product
            $product->created_at = Carbon::now();
            $product->save();

            // Set the product code
            $lastProductId = Product::orderBy('id', 'desc')->first()->id;

            // Store product images if any
            if ($request->has('product_img') && count($request->product_img) > 0) {
                foreach ($request->product_img as $key => $imagedata) {
                    if ($key == 0) {
                        continue;
                    }

                    $productImage = new ProductImage();
                    $productImage->product_id = $product->id;
                    if ($imagedata) {
                        $productImage->image = $this->productImageSave($imagedata);
                        $productImage->image_alt = $request->product_img_alt[$key];
                        $productImage->image_name = $request->product_img_name[$key];
                        $productImage->save();
                    }
                }
            }

            // Handle Product Variants
            if ($request->has('pack_size') && count($request->pack_size) > 0) {
                foreach ($request->pack_size as $index => $packSizeId) {
                    $price = $request->price[$index] ?? 0;

                    $v_brand_id = is_null($request->v_brand_ids[$index])
                        ? $request->brand_id
                        : $request->v_brand_ids[$index];

                    $s_status = $request->stock_status[$index];

                    $variant = Variants::find($packSizeId);

                    if ($variant) {
                        $packSize = intval($variant->pack_size);
                        $price = intval($price);
                        $pricePerPiece = number_format($price / $packSize, 2, '.', ''); // Format as decimal


                        $productVariant = ProductVariant::create([
                            'serial_no' => $request->variant_serial_no[$index],
                            'product_id' => $product->id,
                            'variant_id' => $packSizeId,
                            'price' => $price,
                            'price_per_peice' => $pricePerPiece,
                            'brand_id' => $v_brand_id,
                            'status' => 1,
                            'stock_status' => $s_status,
                        ]);

                        if (isset($request->variant_sizes[$index])) {

                            foreach ($request->variant_sizes[$index] as $pvs) {
                                ProductVariantSize::create([
                                    'product_variant_id' => $productVariant->id,
                                    'size_id' => $pvs['size_id'],
                                    'description' => $pvs['description'],
                                ]);
                            }
                        }
                    }
                }
            }

            // ** Handle Sizes, Options, and Prices for ProductOption **
            if ($request->has('size') && $request->has('option') && $request->has('price_size')) {
                foreach ($request->size as $index => $sizeId) {
                    $optionId = $request->option[$index] ?? null; // Ensure the option ID exists
                    $optionPrice = $request->price_size[$index] ?? 0; // Ensure we have the price

                    if ($sizeId && $optionId) {
                        ProductOption::create([
                            'product_id' => $product->id,
                            'size_id' => $sizeId,
                            'option_id' => $optionId,
                            'options_price' => $optionPrice,
                            'status' => 1, // Active status
                        ]);
                    } else {
                        Log::warning('Missing size or option data, skipping entry.');
                    }
                }
            } else {
                Log::warning('Size, option, or price_size missing in the request');
            }

            // Handle Product Lid Options, Prices and Stock
            $productLidOptions = $request->input('productLidOption');
            $productLidOptionPrices = $request->input('productLidOptionPrice');

            if ($request->has('productLidOption') && count($request->productLidOption) > 0) {
                foreach ($productLidOptions as $index => $productLidOptionId) {
                    if (!empty($productLidOptionId)) {
                        ProductLidOption::create([
                            'product_id' => $lastProductId,
                            'lid_option_id' => $productLidOptionId,
                            'price' => $productLidOptionPrices[$index],
                        ]);
                    }
                }
            }

            // Product Packaging Options
            $productPackagingOptions = $request->input('productPackagingOptions', []);

            $filteredProductPackagingOptions = collect($productPackagingOptions)
                ->reject(function ($item) {
                    return collect($item)->contains(fn($value) => $value === null || trim($value) === '');
                })
                ->map(function ($item) use ($lastProductId) {
                    return [
                        'product_id'     => $lastProductId,
                        'print_location' => $item['print_location'],
                        'side_option'    => $item['side_option'],
                        'price'          => $item['price'],
                    ];
                })
                ->values()
                ->all();


            if (!empty($filteredProductPackagingOptions)) {
                ProductPackagingOption::insert($filteredProductPackagingOptions);
            }


            $productSeoMetadata = new ProductSeoMetadata();
            $productSeoMetadata->product_id = $lastProductId;
            $productSeoMetadata->meta_title = $request->input('meta_title');
            $productSeoMetadata->canonical_url = $request->input('canonical_url');
            $productSeoMetadata->focus_keyword = $request->input('focus_keyword');
            $productSeoMetadata->redirect_301 = $request->input('redirect_301');
            $productSeoMetadata->redirect_302 = $request->input('redirect_302');
            $productSeoMetadata->schema = $request->input('schema');
            $productSeoMetadata->meta_description = $request->input('meta_description');
            $productSeoMetadata->save();

            DB::commit();
            return redirect()->back()->with('success', 'Product Successfully Created');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product Creation Failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Product Creation Failed');
        }
    }

    public function imageDelete(Request $request)
    {
        ProductImage::where('id', $request->id)->delete();
        return 'success';
    }

    public function productImageSave($image)
    {
        if (isset($image) && $image != '' && $image != null) {
            // Use the getClientMimeType() method to get the MIME type
            $mimeType = $image->getClientMimeType();
            $ext = explode('/', $mimeType)[1]; // Extract the file extension from the MIME type

            // Generate a unique file name
            $filename = 'product_images-' . time() . rand(1000, 9999) . '.' . $ext;

            // Define the path in storage
            $path = 'product_images/' . $filename;

            // Create an instance of Image
            // $imageInstance = Image::make($image->getRealPath()) // Get the real path of the uploaded file
            //     ->resize(400, 400) // Resize image
            //     ->brightness(8)   // Adjust brightness
            //     ->contrast(11)    // Adjust contrast
            //     ->sharpen(5)      // Sharpen image
            //     ->encode('webp', 70); // Encode image as WebP

            // Save image to storage
            // Storage::put($path, (string) $imageInstance);
            Storage::put($path, file_get_contents($image->getRealPath()));

            // Return the URL to access the image
            return Storage::url($path);
        }

        return null; // Return null if image is not provided
    }

    public function updateProduct(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $product = Product::findOrFail($id);

            // Update product fields
            $product->serial_no = $request->serial_no;
            $product->name = $request->name;
            $product->parent_product_id = $request->parent_product_id;
            $product->slug = $request->slug;
            $product->category_id = $request->category_id;
            $product->subcategory_id = $request->subcategory_id ?? 0; // Modify if subcategory is provided
            $product->color = $request->color;
            $product->size = is_array($request->size) ? implode(',', $request->size) : '';
            $product->brand_id = $request->brand_id;
            $product->supplier_id = $request->supplier_id;
            $product->current_purchase_cost = $request->current_purchase_cost ?? $request->current_sale_price;
            $product->current_sale_price = $request->current_sale_price;
            $product->current_wholesale_price = $request->current_wholesale_price;
            $product->wholesale_minimum_qty = $request->wholesale_minimum_qty;
            $product->available_quantity = $request->available_quantity;
            $product->discount_type = $request->discount_type;
            $product->discount = $request->discount;
            $product->unit_type = $request->unit_type;
            $product->description = $request->description;
            $product->product_video_url = $request->product_video_url;
            $product->additional_information = $request->additional_information;
            $product->is_popular = $request->is_popular ? 1 : 0;
            $product->is_trending = $request->is_trending ? 1 : 0;
            $product->stock_alert = $request->stock_alert;
            $product->order_limit = $request->order_limit;
            $product->code = $request->sku_code;
            $product->is_customizeable = $request->is_customizeable ? 1 : 0;
            // Handle image update
            if ($request->has('main_image')) {
                $image = $request->main_image;
                if ($image) {
                    if ($product->image_path) {
                        Storage::delete($product->image_path);
                    }
                    $product->image_path = $this->productImageSave($image);
                }
            }

            $product->image_alt = $request->main_img_alt;
            $product->image_name = $request->main_img_name;

            $product->updated_at = Carbon::now();
            $product->save();

            // Update product images if any
            if ($request->has('images')) {
                // ProductImage::where('product_id', $product->id)->delete();
                foreach ($request->images as $key => $imagedata) {
                    $productImage = new ProductImage();
                    $productImage->product_id = $product->id;
                    if ($imagedata) {
                        $productImage->image = $this->productImageSave($imagedata);
                    }

                    $productImage->image_alt = $request->img_alt[$key];
                    $productImage->image_name = $request->img_name[$key];
                    $productImage->save();
                }
            }

            if (!is_null($request->image_id) && count($request->image_id) > 0) {
                foreach ($request->image_id as $key => $img_id) {
                    $productImage = ProductImage::find($img_id);
                    if (!is_null($productImage)) {
                        $productImage->product_id = $product->id;

                        $productImage->image_alt = $request->img_alt[$key];
                        $productImage->image_name = $request->img_name[$key];
                        $productImage->save();
                    }
                }
            }

            // Handle Product Variants update

            if (is_null($request->pack_size)) {
                $existingVariantIds = ProductVariant::where('product_id', $product->id)->pluck('id');
                if ($existingVariantIds->isNotEmpty()) {
                    ProductVariantSize::whereIn('product_variant_id', $existingVariantIds)->delete();
                }
                ProductVariant::where('product_id', $product->id)->delete();
            }

            if ($request->has('pack_size') && count($request->pack_size) > 0) {
                // Delete existing variants
                $existingVariantIds = ProductVariant::where('product_id', $product->id)->pluck('id');
                if ($existingVariantIds->isNotEmpty()) {
                    ProductVariantSize::whereIn('product_variant_id', $existingVariantIds)->delete();
                }
                ProductVariant::where('product_id', $product->id)->delete();

                // Recreate variants
                foreach ($request->pack_size as $index => $packSizeId) {
                    $price = $request->price[$index] ?? 0;

                    // if variant brand is null then default brand
                    $v_brand_id = is_null($request->v_brand_ids[$index])
                        ? $request->brand_id
                        : $request->v_brand_ids[$index];

                    $s_status = $request->stock_status[$index];

                    $variant = Variants::find($packSizeId);

                    if ($variant) {
                        $packSize = $variant->pack_size;
                        $pricePerPiece = number_format($price / $packSize, 2, '.', '');

                        $productVariant = ProductVariant::create([
                            'serial_no' => $request->variant_serial_no[$index],
                            'product_id' => $product->id,
                            'variant_id' => $packSizeId,
                            'price' => $price,
                            'price_per_peice' => $pricePerPiece,
                            'brand_id' => $v_brand_id,
                            'status' => 1,
                            'stock_status' => $s_status,
                        ]);

                        if (isset($request->variant_sizes[$index])) {
                            foreach ($request->variant_sizes[$index] as $pvs) {
                                ProductVariantSize::create([
                                    'product_variant_id' => $productVariant->id,
                                    'size_id' => $pvs['size_id'],
                                    'description' => $pvs['description'],
                                ]);
                            }
                        }
                    }
                }
            }

            // Handle Sizes, Options, and Prices for ProductOption

            if (is_null($request->size)) {
                ProductOption::where('product_id', $product->id)->delete();
            }

            if ($request->has('size') && $request->has('option') && $request->has('options_price')) {
                // Delete existing product options
                ProductOption::where('product_id', $product->id)->delete();

                // Recreate product options
                foreach ($request->size as $index => $sizeId) {
                    $optionId = $request->option[$index] ?? null;
                    $optionPrice = $request->options_price[$index] ?? 0;

                    if ($sizeId && $optionId) {
                        ProductOption::create([
                            'product_id' => $product->id,
                            'size_id' => $sizeId,
                            'option_id' => $optionId,
                            'options_price' => $optionPrice,
                            'status' => 1,
                        ]);
                    }
                }
            } else {
                Log::warning('Size, option, or price_size missing in the request');
            }

            $oldProductLidOptionIDs = $request->input('oldProductLidOptionIDs', []);
            $productLidOptions = $request->input('productLidOption', []);
            $productLidOptionPrices = $request->input('productLidOptionPrice');

            ProductLidOption::where('product_id', $product->id)->whereNotIn('id', $oldProductLidOptionIDs)->delete();

            if ($request->has('productLidOption') && count($productLidOptions) > 0) {
                foreach ($productLidOptions as $index => $productLidOptionId) {
                    if (!empty($productLidOptionId)) {
                        ProductLidOption::updateOrCreate(
                            [
                                'id' => $oldProductLidOptionIDs[$index] ?? null,
                            ],
                            [
                                'product_id' => $product->id,
                                'lid_option_id' => $productLidOptionId,
                                'price' => $productLidOptionPrices[$index],
                            ],
                        );
                    }
                }
            }


            // Product Packaging Options
            $productPackagingOptions = $request->input('productPackagingOptions', []);

            // Filter out invalid rows
            $filteredProductPackagingOptions = collect($productPackagingOptions)
                ->reject(function ($item) {
                    return collect($item)->contains(fn($value) => $value === null || trim($value) === '');
                })
                ->values()
                ->all();

            ProductPackagingOption::where('product_id', $product->id)->delete();

            $insertData = collect($filteredProductPackagingOptions)
                ->map(function ($item) use ($product) {
                    return [
                        'product_id'     => $product->id,
                        'print_location' => $item['print_location'],
                        'side_option'    => $item['side_option'],
                        'price'          => $item['price'],
                    ];
                })->all();

            // Insert new/updated options
            if (!empty($insertData)) {
                ProductPackagingOption::insert($insertData);
            }

            ProductSeoMetadata::updateOrCreate(
                ['product_id' => $id],
                [
                    'meta_title' => $request->input('meta_title', ''),
                    'canonical_url' => $request->input('canonical_url', ''),
                    'focus_keyword' => $request->input('focus_keyword', ''),
                    'redirect_301' => $request->input('redirect_301', ''),
                    'redirect_302' => $request->input('redirect_302', ''),
                    'schema' => $request->input('schema', ''),
                    'meta_description' => $request->input('meta_description', ''),
                ],
            );

            DB::commit();

            return redirect()->route('admin.product.list')->with('success', 'Product Successfully Updated');
        } catch (\Exception $e) {

            DB::rollBack();
            Log::error('Product Update Failed: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function removeProductImage(Request $request)
    {
        $imgID = $request->imgID;
        $pID = $request->pID;

        if ($imgID == 'main') {
            $product = Product::find($pID);
            Storage::delete($product->image_path);
            $product->image_path = null;
            $product->save();
        } else {
            $image = ProductImage::find($imgID);
            if ($image) {
                Storage::delete($image->image);
                $image->delete();
            }
        }
        return response()->json(['success', 'Product Image Successfully Removed']);
    }

    public function productBarcodeGenerate(Request $request)
    {
        $product = Product::find($request->product_id);

        $data = [
            'product' => $product,
            'qty' => $request->barcode_qty,
        ];

        $pdf = PDF::loadView('adminPanel.product.barcode_generate', $data);
        //      return view('adminPanel.pos.sell_invoice');
        //      return $pdf->download('buy_invoice.pdf');
        return $pdf->stream('buy_invoice.pdf');
    }

    public function productVariant()
    {
        $common_data = new Array_();
        $common_data->title = 'Add Variant';
        $variants = Variants::get();
        return view('adminPanel.variants.variants')->with(compact('common_data', 'variants'));
    }

    public function productVariantStore(Request $request)
    {
        $productsize = new Variants();
        $productsize->name = $request->name;
        $productsize->pack_size = $request->pack_size;
        $productsize->save();
        return redirect()->back()->with('success', 'Product Variant Successfully Created');
    }

    public function productVariantUpdate(Request $request)
    {
        $variant = Variants::find($request->id);
        $variant->name = $request->name;
        $variant->pack_size = $request->pack_size;
        $variant->save();
        return redirect()->back()->with('success', 'Product Variant Successfully Updated');
    }

    public function productSlugValidate(Request $request)
    {
        $slug = $request->slug;

        if ($slug == '') {
            return;
        }
        $productId = $request->productId;
        $count = Product::where('slug', $slug)->where('id', '!=', $productId)->count();
        if ($count > 0) {
            return response()->json(['error' => 'Slug already exists']);
        } else {
            return response()->json([
                'success' => 'Slug is available',
                'slug' => $slug,
            ]);
        }
    }

    public function productsImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        Excel::import(new ProductsImport(), $request->file('file'));

        return back()->with('success', 'Products imported successfully.');
    }

    public function ProductExport(Request $request): BinaryFileResponse
    {
        $productsToExport = $request->input('productsToExport', []); // IDs selected
        $columns = $request->input('columns', []); // Columns selected

        // If no products selected, export all
        $productsToExport = empty($productsToExport) ? null : $productsToExport;

        // If no columns selected, export all default columns
        if (empty($columns)) {
            $columns = [
                'name',
                'slug',
                'category_id',
                'subcategory_id',
                'image_path',
                'code',
                'brand_id',
                'available_quantity',
                'is_popular',
                'is_trending',
                'additional_information',
                'status',
                'stock_alert',
                'order_limit',
                'no_of_piece_qty_in_carton',
            ];
        }

        return Excel::download(new ProductsExport($productsToExport, $columns), 'products.xlsx');
    }


    public function priceImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
        ]);

        Excel::import(new ProductPriceUpdate(), $request->file('file'));

        return redirect()->back()->with('success', 'Product prices updated successfully!');
    }

    public function searchProductByNameBrand(Request $request)
    {
        $searchTerm = $request->searchTerm;

        if ($searchTerm == '') {
            return response()->json(['searchResult' => []]);
        }

        $searchResult = Product::with('brand')
            ->where('name', 'like', '%' . $searchTerm . '%')
            ->orWhereHas('brand', function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%');
            })
            ->get();

        return response()->json([
            'searchResult' => $searchResult,
        ]);
    }
    public function getProductByID(Request $request)
    {
        $id = $request->id;

        $product = Product::with('brand')->find($id);

        $productRow =
            '

        <tr class="bg-light text-dark rounded" productID="' .
            $product->id .
            '">

            <input type="hidden" name="hiddenProductIDs[]" value="' .
            $product->id .
            '">
            <input type="hidden" name="hiddenProductQTYs[]" value="1">
            <input type="hidden" name="hiddenProductDiscounts[]" value="0">
            <td>
                ' .
            $product->name .
            '
            </td>
            <td class="itemPrice">
                ' .
            $product->current_sale_price .
            '
            </td>
            <td>
                <input type="number" class="form-control itemQtyInput" value="1" min="1" oninput="calculateTotal()">
            </td>
            <td class="itemTotalPrice">
                0
            </td>
            <td>
                <i class="lni lni-trash" style="cursor: pointer;" onclick="removeItem(this)"></i>
            </td>
        </tr>

        ';

        return response()->json([
            'productRow' => $productRow,
        ]);
    }

    public function saveNewPackSize(Request $request)
    {
        $existing = Variants::where('pack_size', $request->pack_size)->first();

        if ($existing) {
            return response()->json(
                [
                    'message' => 'Pack size already exists.',
                ],
                409,
            );
        }

        $packSize = new Variants();
        $packSize->name = $request->name;
        $packSize->pack_size = $request->pack_size;
        $packSize->save();

        return response()->json($packSize, 200);
    }

    public function toggleStockStatus(Request $request)
    {
        $product = Product::findOrFail($request->product_id);

        $product->stock_status = $product->stock_status == 1 ? 0 : 1;
        $product->save();

        return response()->json(
            [
                'stock_status' => $product->stock_status,
            ],
            200,
        );
    }

    public function productVariantsExport(Request $request): BinaryFileResponse
    {
        $productIds = [];

        if ($request->filled('product_ids')) {
            $productIds = explode(',', $request->product_ids);
        }

        return Excel::download(
            new \App\Exports\ProductVariantExport($productIds),
            'product-variants.xlsx'
        );
    }

    public function productVariantsImport(Request $request)
    {

        Excel::import(new \App\Imports\ProductVariantImport, $request->file('file'));

        return redirect()->back()->with('success', 'Product prices updated successfully!');
    }
}
