<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\Array_;
use Illuminate\Support\Str;
use App\Models\Page;

class PageController extends Controller
{
    public $pages = [
        ['id' => 1, 'name' => 'Shop'],
        ['id' => 14, 'name' => 'Bundles'],
        ['id' => 3, 'name' => 'Custom Packaging'],
        ['id' => 2, 'name' => 'Inquiry'],
        ['id' => 4, 'name' => 'Reviews'],
        ['id' => 5, 'name' => 'Cart'],
        // ['id' => 6, 'name' => 'Category'],
        ['id' => 7, 'name' => 'Home'],
        ['id' => 8, 'name' => 'About us'],
        ['id' => 9, 'name' => 'Contact Us'],
        ['id' => 10, 'name' => 'Blogs'],
        ['id' => 11, 'name' => 'Wishlist'],
        ['id' => 12, 'name' => 'Privacy Policy'],
        ['id' => 13, 'name' => 'Terms & Condition'],
        ['id' => 14, 'name' => 'Return Policy'],
    ];

    public function index()
    {
        $pages = Page::all();
        $common_data = new Array_();
        $common_data->title = 'Pages';
        return view('adminPanel.pages.index', compact('pages', 'common_data'));
    }

    public function create()
    {
        return view('adminPanel.pages.create', ['pages' => $this->pages]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'page_id' => 'required',
            'name' => 'required',
            'slug' => 'required',
        ]);

        $page_check = Page::where('page_id', $request->page_id)->first();
        if ($page_check) {
            return redirect()->back()->with('error', 'Page ID already exists.');
        }
        $page = new Page();
        $page->page_id = $request->input('page_id');
        $page->name = $request->input('name');
        $page->slug = $request->input('slug');
        $page->meta_title = $request->input('meta_title');
        $page->canonical_url = $request->input('canonical_url');
        $page->focus_keyword = $request->input('focus_keyword');
        $page->redirect_301 = $request->input('redirect_301');
        $page->redirect_302 = $request->input('redirect_302');
        $page->schema = $request->input('schema');
        $page->meta_description = $request->input('meta_description');
        $page->page_content = $request->input('page_content');
        $page->save();

        return redirect()->route('pages.list')->with('success', 'Page created successfully.');
    }

    public function edit($id)
    {
        $pages = $this->pages;
        $page = Page::findOrFail($id);
        return view('adminPanel.pages.edit', compact('page', 'pages'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required',
        ]);

        $page_check = Page::where('page_id', $request->page_id)->where('id', '!=', $id)->first();
        if ($page_check) {
            return redirect()->back()->with('error', 'Page ID already exists.');
        }

        $page = Page::findOrFail($id);
        $page->page_id = $request->input('page_id');
        $page->name = $request->input('name');
        $page->slug = $request->input('slug');
        $page->meta_title = $request->input('meta_title');
        $page->canonical_url = $request->input('canonical_url');
        $page->focus_keyword = $request->input('focus_keyword');
        $page->redirect_301 = $request->input('redirect_301');
        $page->redirect_302 = $request->input('redirect_302');
        $page->schema = $request->input('schema');
        $page->meta_description = $request->input('meta_description');
        $page->page_content = $request->input('page_content');
        $page->save();

        return redirect()->route('pages.list')->with('success', 'Page updated successfully.');
    }

    public function destroy($id)
    {
        $page = Page::find($id);
        $page->delete();
        return redirect()->route('pages.list')->with('success', 'Page deleted successfully.');
    }

    public function pageSlugValidate(Request $request)
    {
        $slug = $request->slug;

        if ($slug == '') {
            return;
        }
        $productId = $request->pageId;
        $count = Page::where('slug', $slug)->where('id', '!=', $productId)->count();
        if ($count > 0) {
            return response()->json(['error' => 'Slug already exists']);
        } else {
            return response()->json([
                'success' => 'Slug is available',
                'slug' => $slug,
            ]);
        }
    }
}
