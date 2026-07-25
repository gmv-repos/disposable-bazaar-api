<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\Array_;
use App\Models\ProductCategory;
use App\Models\Blog;
use App\Models\BlogImage;
use App\Models\BlogsSeoMetadata;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class BlogController extends Controller
{
    protected $segment;
    public function __construct(Request $request)
    {
        $this->segment = $request->segment(1);
        $this->page = 'adminPanel.blogs.';
    }

    public function index(Request $request)
    {
        if ($this->segment == 'api') {
            // Define pagination parameters, e.g., items per page
            $perPage = $request->input('per_page', 15); // Default to 15 items per page

            // Paginate the query
            $paginatedBlogs = Blog::with(['productCategory', 'blogImage', 'blogSeoMetadata']) // Eager load relationships
                ->where('status', 1)
                ->orderBy('date', 'desc')
                ->paginate($perPage);

            // Map the paginated results to the desired format
            $blogs = $paginatedBlogs->getCollection()->map(function ($blog) {
                return [
                    'id' => $blog->id,
                    'category' => $blog->productCategory ? $blog->productCategory->name : null,
                    'date' => $blog->date,
                    'title' => $blog->title,
                    'body' => $blog->body,
                    'slug' => $blog->slug,
                    'main_image' => $blog->image,
                    'status' => $blog->status,
                    'created_at' => $blog->created_at,
                    'updated_at' => $blog->updated_at,
                    'inner_image' => $blog->blogImage->pluck('image'),
                    'blogSeoMetadata' => $blog->blogSeoMetadata,
                ];
            });

            // Return the paginated response with metadata
            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Retrieved successfully',
                    'data' => $blogs,
                    'pagination' => [
                        'total' => $paginatedBlogs->total(),
                        'current_page' => $paginatedBlogs->currentPage(),
                        'per_page' => $paginatedBlogs->perPage(),
                        'last_page' => $paginatedBlogs->lastPage(),
                        'from' => $paginatedBlogs->firstItem(),
                        'to' => $paginatedBlogs->lastItem(),
                        'prev_page_url' => $paginatedBlogs->previousPageUrl(),
                        'next_page_url' => $paginatedBlogs->nextPageUrl(),
                        'first_page_url' => $paginatedBlogs->url(1),
                        'last_page_url' => $paginatedBlogs->url($paginatedBlogs->lastPage()),
                    ],
                ],
                200,
            );
        } else {
            $common_data = new Array_();
            $common_data->title = 'Blog List';
            $blogs = Blog::with(['productCategory', 'blogImage'])->get();
            return view($this->page . 'index', compact('common_data', 'blogs'));
        }
        // $posts = Post::all();
        // return view('posts.index', compact('posts'));
    }

    public function show($id)
    {
        try {
            // Fetch the blog with its related category and images
            $blog = Blog::with([
                'productCategory:id,name', // Only select 'id' and 'name' from the productCategory
                'blogImage:id,blog_id,image', // Only select 'id', 'blog_id', and 'image' from blogImages
                'blogSeoMetadata',
            ])->findOrFail($id);

            // If blog is null, although findOrFail will throw an exception if not found
            if (is_null($blog)) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Blog Not Found',
                ]);
            }

            // Prepare the blog data
            $blogData = [
                'id' => $blog->id,
                'category' => $blog->productCategory ? $blog->productCategory->name : null,
                'date' => $blog->date,
                'title' => $blog->title,
                'body' => $blog->body,
                'slug' => $blog->slug,
                'images' => $blog->blogImage->pluck('image'), // Collect all image paths for the blog
                'status' => $blog->status,
                'created_at' => $blog->created_at,
                'updated_at' => $blog->updated_at,
                'blogSeoMetadata' => $blog->blogSeoMetadata,
            ];

            // Fetch recommended blogs based on the same category
            $recommendedBlogs = Blog::with(['productCategory:id,name'])
                ->where('category_id', $blog->category_id) // Use the same category ID
                ->where('id', '!=', $blog->id) // Exclude the current blog
                ->where('status', 1) // Assuming status 1 means active
                ->take(5) // Limit the number of recommended blogs
                ->get();

            // Prepare response
            if ($this->segment == 'api') {
                return response()->json(
                    [
                        'status' => 'success',
                        'message' => 'Blogs Retrieved successfully',
                        'data' => [
                            'blog' => $blogData,
                            'recommended_blogs' => $recommendedBlogs,
                        ],
                    ],
                    200,
                );
            } else {
                // Handle non-API requests here if needed
            }
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
    public function showBySlug(Request $request)
    {
        try {
            $slug = $request->input('slug');
            // Fetch the blog with its related category and images
            $blog = Blog::with([
                'productCategory:id,name', // Only select 'id' and 'name' from the productCategory
                'blogImage:id,blog_id,image', // Only select 'id', 'blog_id', and 'image' from blogImages
                'blogSeoMetadata',
            ])
                ->where('slug', $slug)
                ->first();

            // If blog is null, although findOrFail will throw an exception if not found
            if (is_null($blog)) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Blog Not Found',
                ]);
            }

            // Prepare the blog data
            $blogData = [
                'id' => $blog->id,
                'category' => $blog->productCategory ? $blog->productCategory->name : null,
                'date' => $blog->date,
                'title' => $blog->title,
                'body' => $blog->body,
                'slug' => $blog->slug,
                'images' => $blog->blogImage->pluck('image'), // Collect all image paths for the blog
                'status' => $blog->status,
                'created_at' => $blog->created_at,
                'updated_at' => $blog->updated_at,
                'blogSeoMetadata' => $blog->blogSeoMetadata,
            ];

            // Fetch recommended blogs based on the same category
            $recommendedBlogs = Blog::with(['productCategory:id,name'])
                ->where('category_id', $blog->category_id) // Use the same category ID
                ->where('id', '!=', $blog->id) // Exclude the current blog
                ->where('status', 1) // Assuming status 1 means active
                ->take(5) // Limit the number of recommended blogs
                ->get();

            // Prepare response
            if ($this->segment == 'api') {
                return response()->json(
                    [
                        'status' => 'success',
                        'message' => 'Blogs Retrieved successfully',
                        'data' => [
                            'blog' => $blogData,
                            'recommended_blogs' => $recommendedBlogs,
                        ],
                    ],
                    200,
                );
            } else {
                // Handle non-API requests here if needed
            }
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

    public function categoryWise($categoryId)
    {
        try {
            $blog = Blog::with([
                'productCategory:id,name', // Only select 'id' and 'name' from the productCategory
                'blogImage:id,blog_id,image', // Only select 'id', 'blog_id', and 'image' from blogImages
                'blogSeoMetadata',
            ])
                ->where('category_id', $categoryId)
                ->get();
            if (is_null($blog)) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Category Not Found',
                ]);
            }
            if ($this->segment == 'api') {
                return response()->json(
                    [
                        'status' => 'success',
                        'message' => 'Category Wise Blogs Retrieved successfully',
                        'data' => $blog,
                    ],
                    200,
                );
            } else {
            }
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

    public function create()
    {
        $common_data = new Array_();
        $common_data->title = 'Add New Blog';
        $productCategory = ProductCategory::where('status', 1)->where('deleted', 0)->get();
        return view($this->page . 'create', compact('common_data', 'productCategory'));
    }

    public function storeBlog(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable',
            'body' => 'required',
            'date' => 'required',
            'image' => 'nullable|string', // Adjust validation based on your requirements
        ]);
        $image = $request->blog_img[0];
        $blog = new Blog();

        $blog->title = $request->input('title');
        $blog->slug = $request->input('slug');
        $blog->category_id = $request->input('category_id');
        $blog->body = $request->input('body');
        $blog->date = $request->input('date');
        $blog->status = 1;
        if (isset($image) && $image != '' && $image != null) {
            $blog->image = $this->blogImageSave($image);
        }
        // if($request->input('blog_img')){
        //     $blog->image = $this->blogImageSave($request->input('blog_img'));
        // }
        $blog->save();

        foreach ($request->blog_img as $key => $imagedata) {
            if ($key != 0) {
                $blogImage = new BlogImage();
                $blogImage->blog_id = $blog->id;
                $image = $imagedata;
                if (isset($image) && $image != '' && $image != null) {
                    $blogImage->image = $this->blogImageSave($image);
                    $blogImage->save();
                }
            }
        }

        $blogsSeoMetadata = new BlogsSeoMetadata();
        $blogsSeoMetadata->blog_id = $blog->id;
        $blogsSeoMetadata->meta_title = $request->input('meta_title');
        $blogsSeoMetadata->canonical_url = $request->input('canonical_url');
        $blogsSeoMetadata->focus_keyword = $request->input('focus_keyword');
        $blogsSeoMetadata->redirect_301 = $request->input('redirect_301');
        $blogsSeoMetadata->redirect_302 = $request->input('redirect_302');
        $blogsSeoMetadata->schema = $request->input('schema');
        $blogsSeoMetadata->meta_description = $request->input('meta_description');
        $blogsSeoMetadata->save();

        return redirect()->route('blogs.list')->with('success', 'Blog created successfully.');
    }

    public function blogImageSave($image)
    {
        if ($image) {
            // Extract file extension
            $ext = explode('/', mime_content_type($image))[1];

            // Generate unique file name
            $filename = 'blog_images-' . time() . rand(1000, 9999) . '.' . $ext;

            // Define the path in storage
            $path = 'blog_images/' . $filename;

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

    public function blogEditInfo($id)
    {
        $common_data = new Array_();
        $common_data->title = 'Edit Blog Detail';
        $blog = Blog::with([
            'productCategory:id,name', // Only select 'id' and 'name' from the productCategory
            'blogImage:id,blog_id,image', // Only select 'id', 'blog_id', and 'image' from blogImages
            'blogSeoMetadata',
        ])->findOrFail($id);

        // If blog is null, although findOrFail will throw an exception if not found
        if (is_null($blog)) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Blog Not Found',
            ]);
        }
        $productCategory = ProductCategory::where('status', 1)->get();
        return view('adminPanel.blogs._blog_edit')->with(compact('blog', 'productCategory'))->render();
    }

    public function updateBlog(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'category_id' => 'nullable',
            'body' => 'nullable|string',
            'image' => 'nullable|string',
        ]);
        $image = $request->blog_img[0];
        // Find the blog by ID
        $blog = Blog::findOrFail($request->input('id'));

        // Update the blog details
        $blog->date = $request->input('date');
        $blog->title = $request->input('title');
        $blog->slug = $request->input('slug');
        $blog->category_id = $request->input('category_id');
        $blog->body = $request->input('body');
        if (isset($image) && $image != '' && $image != null) {
            if ($blog->image) {
                Storage::delete($blog->image);
            }
            $blog->image = $this->blogImageSave($image);
        }
        // Save the updated blog
        $blog->save();
        if (isset($image) && $image != '' && $image != null) {
            foreach ($request->blog_img as $key => $imagedata) {
                if ($key != 0) {
                    $blog->blogImage()->delete();
                    $blogImage = new BlogImage();
                    $blogImage->blog_id = $blog->id;
                    $image = $imagedata;
                    if (isset($image) && $image != '' && $image != null) {
                        $blogImage->image = $this->blogImageSave($image);
                        $blogImage->save();
                    }
                }
            }
        }

        $blogsSeoMetadata = BlogsSeoMetadata::where('blog_id', $blog->id)->first();

        if (is_null($blogsSeoMetadata)) {
            $blogsSeoMetadata = new BlogsSeoMetadata();
            $blogsSeoMetadata->blog_id = $blog->id;
        }

        $blogsSeoMetadata->meta_title = $request->input('meta_title');
        $blogsSeoMetadata->canonical_url = $request->input('canonical_url');
        $blogsSeoMetadata->focus_keyword = $request->input('focus_keyword');
        $blogsSeoMetadata->redirect_301 = $request->input('redirect_301');
        $blogsSeoMetadata->redirect_302 = $request->input('redirect_302');
        $blogsSeoMetadata->schema = $request->input('schema');
        $blogsSeoMetadata->meta_description = $request->input('meta_description');
        $blogsSeoMetadata->save();

        // Redirect with a success message
        return redirect()->route('blogs.list')->with('success', 'Blog updated successfully.');
    }

    public function blogDelete(Request $request)
    {
        $blog = Blog::find($request->id);
        $blog->status = 0;
        $blog->save();
        return redirect()->back()->with('success', 'Blog Successfully Deleted');
    }

    public function blogRestore(Request $request)
    {
        $blog = Blog::find($request->id);
        $blog->status = 1;
        $blog->save();
        return redirect()->back()->with('success', 'Blog Successfully Restore');
    }
}
