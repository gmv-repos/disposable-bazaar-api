<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Discount;
use App\Models\Product;
use App\Models\ProductCategory as Category;
use App\Models\DiscountItem;
use Carbon\Carbon;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::get();
        $products = Product::all();
        $categories = Category::with('parentCategory')->get();

        return view('adminPanel.discounts.index', compact('discounts', 'products', 'categories'));
    }

    public function create()
    {
        $products = Product::all();
        $categories = Category::with('parentCategory')->get();

        return view('adminPanel.discounts.create', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $discount = Discount::create([
            'name' => $validated['name'],
            'discount_percentage' => $validated['discount_percentage'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'is_active' => 0,
        ]);

        $product_ids = $request->input('product_ids', []);

        foreach ($product_ids as $product_id) {
            $product = Product::find($product_id);
            DiscountItem::create([
                'discount_id' => $discount->id,
                'product_id' => $product->id,
                'category_id' => $product->category_id,
            ]);
        }

        return redirect()->route('discounts.index')->with('success', 'Discount created successfully!');
    }

    public function show($id)
    {
        $discount = Discount::with(['item.product', 'item.category'])->findOrFail($id);

        return view('adminPanel.discounts.show', compact('discount'));
    }

    public function edit($id)
    {
        $discount = Discount::with(['item.product', 'item.category'])->findOrFail($id);

        $products = Product::all();
        $categories = Category::with('parentCategory')->get();

        $selectedCategoryIds = $discount->item->pluck('category_id')->filter()->unique()->toArray();
        $selectedProductIds = $discount->item->pluck('product_id')->filter()->unique()->toArray();

        return view(
            'adminPanel.discounts.edit',
            compact('discount', 'products', 'categories', 'selectedCategoryIds', 'selectedProductIds'),
        );
    }

    public function update(Request $request, $id)
    {
        $discount = Discount::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'category_ids' => 'required|array',
            'product_ids' => 'required|array',
        ]);

        // Update discount fields
        $discount->update([
            'name' => $validated['name'],
            'discount_percentage' => $validated['discount_percentage'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]);

        // Delete old discount_items
        DiscountItem::where('discount_id', $discount->id)->delete();

        // Re-create discount_items for selected products
        foreach ($validated['product_ids'] as $product_id) {
            $product = Product::find($product_id);
            if ($product) {
                DiscountItem::create([
                    'discount_id' => $discount->id,
                    'product_id' => $product->id,
                    'category_id' => $product->category_id,
                ]);
            }
        }

        return redirect()->route('discounts.index')->with('success', 'Discount updated successfully!');
    }

    public function destroy($id)
    {
        $discount = Discount::with('item')->findOrFail($id);
        $discount->item()->delete();
        $discount->delete();
        return redirect()->route('discounts.index')->with('success', 'Discount deleted successfully!');
    }

    public function fetchProducts(Request $request)
    {
        $categoryIds = $request->input('category_ids', []);

        $products = Product::whereIn('category_id', $categoryIds)->orWhereIn('subcategory_id', $categoryIds)->get();
        return response()->json($products);
    }

    public function duplicate($id)
    {
        $original = Discount::with('item')->findOrFail($id);

        // Replicate and update fields
        $copy = $original->replicate();

        $start = Carbon::parse($copy->start_time)->addYear(1);
        $end = Carbon::parse($copy->end_time)->addYear(1);

        $copy->start_time = $start;
        $copy->end_time = $end;
        $copy->is_active = 0;

        $copy->save();

        // Now replicate the related discount_items with the new discount_id
        foreach ($original->item as $item) {
            $newItem = $item->replicate();
            $newItem->discount_id = $copy->id; // Now this is available
            $newItem->save();
        }

        return redirect()->back()->with('success', 'Discount duplicated successfully.');
    }
}
