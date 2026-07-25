<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Models\CategoriesSortOrder;
use App\Models\CategorySeoMetadata;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\Array_;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;

class ProductCategoryController extends Controller
{
    public function productCategory()
    {

        $common_data = new Array_();
        $common_data->title = 'Product Category';
        $category = ProductCategory::with('categorySeoMetadata')
            ->orderByRaw('ISNULL(serial_no), serial_no ASC')
            ->get();

        $categoryDD = ProductCategory::whereNull('parent_id')
            ->orderByRaw('ISNULL(serial_no), serial_no ASC')
            ->get();

        return view('adminPanel.product_category.product_category')->with(
            compact('category', 'categoryDD', 'common_data'),
        );
    }

    public function productCategoryStore(Request $request)
    {
        $category = new ProductCategory();
        $category->parent_id = $request->parent_id;
        $category->name = $request->name;
        $category->serial_no = $request->serial_no;
        $category->slug = $request->slug;
        $category->note = $request->note;
        $category->image = $this->categoryIcon($request->banner_img);

        if ($request->hasFile('hero_banner_image')) {
            $file = $request->file('hero_banner_image');
            $filename = uniqid() . '_' . $file->getClientOriginalName();
            $path = 'category_icons/hero_banners/' . $filename;

            Storage::put($path, file_get_contents($file->getRealPath()));

            $category->hero_banner_image = Storage::url($path);
        }
        $category->created_at = Carbon::now();
        $category->save();

        $categoryId = $category->id;

        $categorySeoMetadata = new CategorySeoMetadata();
        $categorySeoMetadata->category_id = $categoryId;
        $categorySeoMetadata->meta_title = $request->input('meta_title');
        $categorySeoMetadata->canonical_url = $request->input('canonical_url');
        $categorySeoMetadata->focus_keyword = $request->input('focus_keyword');
        $categorySeoMetadata->redirect_301 = $request->input('redirect_301');
        $categorySeoMetadata->redirect_302 = $request->input('redirect_302');
        $categorySeoMetadata->schema = $request->input('schema');
        $categorySeoMetadata->meta_description = $request->input('meta_description');
        $categorySeoMetadata->save();

        return redirect()->back()->with('success', 'Successfully Added Category');
    }

    public function productCategoryUpdate(Request $request)
    {
        $category = ProductCategory::find($request->category_id);
        $category->parent_id = $request->parent_id;
        $category->name = $request->name;
        $category->serial_no = $request->serial_no;
        $category->slug = $request->slug;
        $category->note = $request->note;

        if ($request->updateImage) {
            $category->image = $this->categoryIcon($request->updateImage);
        }


        if ($request->hasFile('hero_banner_image')) {
            $file = $request->file('hero_banner_image');
            $filename = uniqid() . '_' . $file->getClientOriginalName();
            $path = 'category_icons/hero_banners/' . $filename;
            Storage::put($path, file_get_contents($file));
            $category->hero_banner_image = Storage::url($path);
        }

        if ($request->is_popular) {
            $category->is_popular = 1;
        } else {
            $category->is_popular = 0;
        }

        if ($request->is_active) {
            $category->status = 1;
        } else {
            $category->status = 0;
        }

        $category->save();

        $categorySeoMetadata = CategorySeoMetadata::where('category_id', $request->category_id)->first();

        if (is_null($categorySeoMetadata)) {
            $categorySeoMetadata = new CategorySeoMetadata();
            $categorySeoMetadata->category_id = $request->category_id;
        }

        $categorySeoMetadata->meta_title = $request->input('meta_title');
        $categorySeoMetadata->canonical_url = $request->input('canonical_url');
        $categorySeoMetadata->focus_keyword = $request->input('focus_keyword');
        $categorySeoMetadata->redirect_301 = $request->input('redirect_301');
        $categorySeoMetadata->redirect_302 = $request->input('redirect_302');
        $categorySeoMetadata->schema = $request->input('schema');
        $categorySeoMetadata->meta_description = $request->input('meta_description');
        $categorySeoMetadata->save();

        return redirect()->back()->with('success', 'Category Successfully Updated');
    }
    public function productCategoryInactive(Request $request)
    {
        $category = ProductCategory::find($request->id);
        $category->status = 0;
        $category->save();
        return redirect()->back()->with('success', 'Category Successfully Inactive');
    }

    public function productCategoryActive(Request $request)
    {
        $category = ProductCategory::find($request->id);
        $category->status = 1;
        $category->save();
        return redirect()->back()->with('success', 'Category Successfully Active');
    }

    public function categoryIcon($image)
    {
        if ($image) {
            // Extract file extension
            $ext = explode('/', mime_content_type($image))[1];

            // Generate unique file name
            $filename = 'category_icons-' . time() . rand(1000, 9999) . '.' . $ext;

            // Define the path in storage
            $path = 'category_icons/' . $filename;

            // Create an instance of Image
            $imageInstance = Image::make($image)
                ->resize(400, 400) // Resize image
                ->brightness(8) // Adjust brightness
                ->contrast(11) // Adjust contrast
                ->sharpen(5) // Sharpen image
                ->encode('webp', 70); // Encode image as WebP

            // Save image to storage
            Storage::put($path, (string) $imageInstance);

            // Return the URL to access the image
            return Storage::url($path);
        }

        return null; // Return null if image is not provided
    }

    public function productCategorySlugValidate(Request $request)
    {
        $slug = $request->slug;

        if ($slug == '') {
            return;
        }
        $id = $request->id;
        $count = ProductCategory::where('slug', $slug)->where('id', '!=', $id)->count();
        if ($count > 0) {
            return response()->json(['error' => 'Slug already exists']);
        } else {
            return response()->json([
                'success' => 'Slug is available',
                'slug' => $slug,
            ]);
        }
    }
    public function productCategorySerialNoValidate(Request $request)
    {
        $serial_no = $request->serial_no;

        if ($serial_no == '') {
            return;
        }
        $id = $request->id;

        $parent_id = $request->parent_id;

        $query = ProductCategory::where('serial_no', $serial_no)->where('id', '!=', $id);

        if (!empty($parent_id)) {
            $query->where('parent_id', $parent_id);
        } else {
            $query->whereNull('parent_id');
        }

        $count = $query->count();

        if ($count > 0) {
            return response()->json(['error' => 'serial no already exists']);
        } else {
            return response()->json([
                'success' => 'Serial no is available',
                'serial_no' => $serial_no,
            ]);
        }
    }

    public function showCategoriesSortOrders()
    {
        $common_data = new Array_();
        $common_data->title = 'Categories Sort Orders';

        $categories = ProductCategory::whereNull('parent_id')
            ->where('status', 1)
            ->where('deleted', 0)
            ->with(['childCategories', 'sortOrders', 'childCategories.sortOrders'])
            ->get();

        return view('adminPanel.product_category.categories_sort_orders', compact('categories', 'common_data'));
    }

    public function storeCategoriesSortOrders(Request $request)
    {
        $sortingData = $request->input('sorting', []);

        try {
            DB::transaction(function () use ($sortingData) {

                $rows = [];

                foreach ($sortingData as $categoryId => $sections) {

                    foreach ($sections as $sectionName => $data) {

                        $rows[] = [
                            'category_id'  => (int) $categoryId,
                            'section_name' => $sectionName,
                            'sort_order'   => isset($data['sort_order']) && $data['sort_order'] !== ''
                                ? (int) $data['sort_order']
                                : 0,

                            'is_visible'   => isset($data['is_visible']) ? 1 : 0,

                            'created_at'   => now(),
                            'updated_at'   => now(),
                        ];
                    }
                }

                if (!empty($rows)) {
                    CategoriesSortOrder::upsert(
                        $rows,
                        ['category_id', 'section_name'],
                        ['sort_order', 'is_visible', 'updated_at']
                    );
                }
            });

            return back()->with('success', 'Category visibility & sort order updated successfully.');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Failed to update category settings.');
        }
    }
}
