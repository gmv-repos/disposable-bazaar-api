<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Bundle;
use App\Models\BundleItem;
use App\Models\BundleImage;
use App\Models\Product;
use App\Models\Brand;
use Illuminate\Support\Facades\Storage;

class BundleController extends Controller
{
    public function index()
    {
        $bundles = Bundle::all();
        return view('adminPanel.bundles.index', compact('bundles'));
    }

    public function create()
    {
        return view('adminPanel.bundles.create');
    }

    public function store(Request $request)
    {
        $bundleName = $request->input('bundleName');
        $bundleSlug = $request->input('bundleSlug');
        $description = $request->input('description');
        $bundleDiscount = $request->input('bundleDiscount', 0);

        $productIDs = $request->input('hiddenProductIDs');
        $productBrandIDs = $request->input('productBrands');
        $productVariantIDs = $request->input('productVariants');
        $productLids = $request->input('productLids');
        $productPrices = $request->input('productPrices');
        $productQTYs = $request->input('productQTYs');

        DB::beginTransaction();

        try {
            $bundleTotal = 0;

            $bundleItemsIDs = [];

            foreach ($productIDs as $key => $value) {
                $itemQTY = $productQTYs[$key];

                $productLidID = $productLids[$key];

                $totalPrice = $productPrices[$key] * $itemQTY;

                $bundleItem = new BundleItem();

                $bundleItem->product_id = $productIDs[$key];
                $bundleItem->brand_id = $productBrandIDs[$key];
                $bundleItem->product_lid_option_id = $productLidID;
                $bundleItem->product_variant_id =
                    $productVariantIDs[$key] == 'custom' ? null : $productVariantIDs[$key];
                $bundleItem->product_lid_option_qty = null;
                $bundleItem->quantity = $itemQTY;
                $bundleItem->price = $productPrices[$key];
                $bundleItem->discount = 0;
                $bundleItem->total = $totalPrice;
                $bundleItem->save();

                $bundleItemsIDs[] = $bundleItem->id;

                $bundleTotal += $productPrices[$key] * $itemQTY;
            }

            $referenceCode = 'BUN-' . date('Ymd-His');

            $payableAmount = $bundleTotal - $bundleDiscount;

            $bundle = Bundle::create([
                'reference_code' => $referenceCode,
                'name' => $bundleName,
                'slug' => $bundleSlug,
                'total_amount' => $bundleTotal,
                'discount_amount' => $bundleDiscount,
                'delivery_charges' => 0,
                'payable_amount' => $payableAmount,
                'description' => $description,
                'meta_title' => $request->meta_title,
                'canonical_url' => $request->canonical_url,
                'focus_keyword' => $request->focus_keyword,
                'redirect_301' => $request->redirect_301,
                'redirect_302' => $request->redirect_302,
                'schema' => $request->schema,
            ]);

            BundleItem::whereIn('id', $bundleItemsIDs)->update(['bundle_id' => $bundle->id]);

            if ($request->hasFile('bundleImages')) {
                $images = $request->file('bundleImages');
                foreach ($images as $index => $img) {
                    $path = $this->saveBundleImage($img);
                    if ($index === 0) {
                        Bundle::where('id', $bundle->id)->update(['main_image' => $path]);
                    }
                    $bundleImage = new BundleImage();
                    $bundleImage->bundle_id = $bundle->id;
                    $bundleImage->image = $path;
                    $bundleImage->save();
                }
            }

            DB::commit();
            return redirect()->route('bundles.index')->with('success', 'Bundle created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Bundle creation failed: ' . $e->getMessage());

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $bundle = Bundle::with(
            'bundleItems.product.productVariants.variant',
            'bundleItems.product.productLidOptions.lidOption',
            'bundleItems.brand',
        )->find($id);

        return view('adminPanel.bundles.edit', [
            'bundle' => $bundle,
            'brands' => Brand::where('status', 1)->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $bundle = Bundle::findOrFail($id);

        DB::beginTransaction();

        try {
            $bundle->update([
                'name' => $request->input('bundleName'),
                'slug' => $request->input('bundleSlug'),
                'description' => $request->input('description'),
                'discount_amount' => $request->input('bundleDiscount', 0),
                'meta_title' => $request->meta_title,
                'canonical_url' => $request->canonical_url,
                'focus_keyword' => $request->focus_keyword,
                'redirect_301' => $request->redirect_301,
                'redirect_302' => $request->redirect_302,
                'schema' => $request->schema,
            ]);

            $bundle->bundleItems()->delete(); // Clear existing items

            $productIDs = $request->input('hiddenProductIDs');
            $productBrandIDs = $request->input('productBrands');
            $productVariantIDs = $request->input('productVariants');
            $productLids = $request->input('productLids');
            $productPrices = $request->input('productPrices');
            $productQTYs = $request->input('productQTYs');

            $bundleTotal = 0;

            foreach ($productIDs as $key => $value) {
                $totalPrice = $productPrices[$key] * $productQTYs[$key];

                $bundleItem = new BundleItem([
                    'product_id' => $productIDs[$key],
                    'brand_id' => $productBrandIDs[$key],
                    'product_variant_id' => $productVariantIDs[$key] === 'custom' ? null : $productVariantIDs[$key],
                    'product_lid_option_id' => $productLids[$key],
                    'quantity' => $productQTYs[$key],
                    'price' => $productPrices[$key],
                    'discount' => 0,
                    'total' => $totalPrice,
                ]);

                $bundle->bundleItems()->save($bundleItem);
                $bundleTotal += $totalPrice;
            }

            $payableAmount = $bundleTotal - $request->input('bundleDiscount', 0);

            $bundle->update([
                'total_amount' => $bundleTotal,
                'payable_amount' => $payableAmount,
            ]);

            if ($request->hasFile('bundleImages')) {
                $images = $request->file('bundleImages');
                foreach ($images as $index => $img) {
                    $path = $this->saveBundleImage($img);
                    if ($index === 0) {
                        Bundle::where('id', $bundle->id)->update(['main_image' => $path]);
                    }
                    $bundleImage = new BundleImage();
                    $bundleImage->bundle_id = $bundle->id;
                    $bundleImage->image = $path;
                    $bundleImage->save();
                }
            }

            DB::commit();
            return redirect()->route('bundles.index')->with('success', 'Bundle updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $bundle = Bundle::findOrFail($id);
        $bundle->delete();
        return redirect()->route('bundles.index')->with('success', 'Bundle deleted successfully!');
    }

    public function show($id)
    {
        $bundle = Bundle::with([
            'bundleItems',
            'bundleItems.productVariant',
            'bundleItems.productLidOption',
            'bundleItems.brand',
        ])->find($id);
        return view('adminPanel.bundles.show', compact('bundle'));
    }

    public function searchProductForBundle(Request $request)
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
    public function addProductToBundle(Request $request)
    {
        $id = $request->id;

        $product = Product::with(['productVariants.variant', 'productLidOptions.lidOption'])->find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found.'], 404);
        }

        $brands = Brand::where('status', 1)->get();

        $productRow = view('adminPanel.bundles.bundleProductRow', [
            'product' => $product,
            'brands' => $brands,
        ])->render();

        return response()->json([
            'productRow' => $productRow,
        ]);
    }

    public function saveBundleImage($image)
    {
        if ($image) {
            // Get the extension using Laravel's method
            $ext = $image->getClientOriginalExtension();

            $filename = 'bundle-' . time() . rand(1000, 9999) . '.' . $ext;

            $path = 'bundle_images/' . $filename;

            Storage::put($path, file_get_contents($image->getRealPath()));

            return Storage::url($path);
        }

        return null;
    }
}
