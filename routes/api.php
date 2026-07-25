<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CashOnDeliveryController;
use App\Http\Controllers\Api\CompanyInfoController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\PopularProductController;
use App\Http\Controllers\Api\ProductCategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductSubcategoryController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\StorageController;
use App\Http\Controllers\api\StripePaymentController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AreaController;
use App\Http\Controllers\Api\UserOrderController;
use App\Http\Controllers\Api\WishListController;
use App\Http\Controllers\Api\SubscriberController;
use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\GoogleController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\BundleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('login/google', [AuthController::class, 'redirectToGoogle']);
Route::post('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::post('auth/facebook/callback', [AuthController::class, 'handleFacebookCallback']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'status' => 'success',
        'message' => 'User details retrieved successfully',
        'data' => [
            'name' => $request->user()->name,
            'email' => $request->user()->email,
            'phone' => $request->user()->phone,
            'address' => $request->user()->address,
            'photo' => $request->user()->photo,
        ],
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    //Auth Private Routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/updateUser', [AuthController::class, 'updateUser']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('user/changePassword', [AuthController::class, 'changePassword']);

    //Auth Private Routes
    //User Private Shiping Address Routes
    Route::post('user/shipping/billing/address', [UserController::class, 'userAddress']);
    Route::get('user/shipping/billing/address/get', [UserController::class, 'userAddressGet']);
    //User Private Shiping Address Routes
    //User Private Wishlist Routes
    Route::post('user/wishlist/{productId}/add', [WishListController::class, 'addWishList']);
    Route::get('user/wishlist/get', [WishListController::class, 'getWishList']);
    Route::get('user/wishlist/count', [WishListController::class, 'count']);
    Route::post('user/wishlist/delete/{id}', [WishListController::class, 'removeFromWishList']);
    //User Private Wishlist Routes
    //Order Crud Routes
    Route::get('user/order/list', [UserOrderController::class, 'orderList']);
    Route::get('user/order/details/{id}', [UserOrderController::class, 'orderDetails']);
    Route::post('stripe/payment', [StripePaymentController::class, 'stripePayment']);
    Route::post('cashOnDelivery/payment', [CashOnDeliveryController::class, 'cashOnDeliveryOrder']);
    Route::get('user/order/cancel', [UserOrderController::class, 'cancel']);
    Route::post('user/order/add', [UserOrderController::class, 'placeOrder']);

    Route::prefix('order')->group(function () {
        Route::get('/list', [OrderController::class, 'index'])->name('order.list');
        Route::get('/track/{orderNo}', [OrderController::class, 'show'])->name('order.show');
        Route::post('/cancelOrder/{orderid}', [OrderController::class, 'cancelOrder'])->name('order.cancelOrder');
    });

    Route::post('/reviews/{id}/like', [ProductController::class, 'likeOrDislike']);

    Route::prefix('cart')->group(function () {
        Route::get('/index', [CartController::class, 'getCartList'])->name('cart.list');
        Route::post('/store', [CartController::class, 'addCart'])->name('cart.store');
        Route::get('/count', [CartController::class, 'count'])->name('cart.count');
        Route::post('/remove/{cartId}', [CartController::class, 'e']);
        Route::post('/update/{cartId}', [CartController::class, 'updateCart']);
    });
});

//Authentication Public Routes

Route::prefix('order')->group(function () {
    Route::post('/place', [OrderController::class, 'store'])->name('order.store');
});
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('user/forgetPassword', [AuthController::class, 'forgetPassword']);
Route::post('user/resetPassword', [AuthController::class, 'resetPassword']);

Route::post('/contact_add', [AuthController::class, 'contact_add']);

Route::post('/inquiry_add', [AuthController::class, 'inquiry_add']);
//Authentication Public Routes

Route::post('/subscribe', [SubscriberController::class, 'subscribe']);
Route::post('/unsubscribe', [SubscriberController::class, 'unsubscribe']);
Route::post('/contact-us', [ContactUsController::class, 'contactUs']);
//Products Public Routes
Route::get('home/trending/product/get', [ProductController::class, 'homeTrendingProduct']);
Route::get('home/category/{id}/product', [ProductController::class, 'categoryProduct']);
Route::get('home/category/s/{slug}/product', [ProductController::class, 'categoryProductBySlug']);
Route::get('home/subcategory/{id}/product', [ProductController::class, 'subCategoryProduct']);
Route::get('home/popular/product/get', [ProductController::class, 'homePopularProduct']);
Route::get('home/new/arrival/product', [ProductController::class, 'newArrivalProduct']);
Route::get('product/{id}/details', [ProductController::class, 'productDetails']);
Route::post('product/s/details', [ProductController::class, 'productDetailsBySlug']);
Route::get('product/customize/{id}/details', [ProductController::class, 'customizeproductDetails']);
Route::post('product/customize/s/details', [ProductController::class, 'customizeproductDetailsBySlug']);
Route::post('/review_add', [ProductController::class, 'review_add']);
Route::get('/all_reviews', [ProductController::class, 'all_reviews']);
Route::get('/filter_reviews', [ProductController::class, 'filter_reviews']);
Route::get('/product_reviews/{productId}', [ProductController::class, 'product_review']);
Route::post('/product-reviews-by-slug', [ProductController::class, 'productReviewBySlug']);
Route::get('/user_wise_reviews', [ProductController::class, 'user_wise_reviews']);
Route::get('/reviews/{id}/likes', [ProductController::class, 'getLikesAndDislikes']);

Route::get('/areasList', [AreaController::class, 'index']);
//Products Public Routes

//Category Public Routes
Route::get('product/category', [ProductCategoryController::class, 'categoryList']);
Route::get('category/{id}/subcategory', [ProductSubcategoryController::class, 'categoryWiseSubcategory']);
Route::get('category-by-slug/{slug}', [ProductCategoryController::class, 'categoryBySlug']);

Route::get('category/slug/{slug}/subcategory', [ProductSubcategoryController::class, 'categoryWiseSubcategoryBySlug']);
//Category Public Routes

Route::prefix('blogs')->group(function () {
    Route::get('/index', [BlogController::class, 'index'])->name('blogs.list');
    Route::get('/details/{id}', [BlogController::class, 'show'])->name('blogs.show');
    Route::post('/s/details', [BlogController::class, 'showBySlug'])->name('blogs.showBySlug');
    Route::get('/category_wise/{categoryId}', [BlogController::class, 'categoryWise'])->name('blogs.category-wise');
});

// Pages Routes
Route::prefix('page')->group(function () {
    Route::get('/index', [PageController::class, 'index'])->name('page.list');
    Route::get('/detail/{id}', [PageController::class, 'show'])->name('page.show');
    Route::get('/detail/slug/{slug}', [PageController::class, 'showBySlug'])->name('page.show.slug');
});

Route::get('home/best/sell/product', [ProductController::class, 'bestSellProduct']);
Route::get('home/subcategory/product/related', [ProductController::class, 'relatedProductGet']);
Route::get('search/product', [ProductController::class, 'srcProductList']);
Route::get('product/all/search', [ProductController::class, 'globalProductSearch']);

Route::get('search/Customizeproduct', [ProductController::class, 'srcCustomizeProductList']);
Route::get('offer/banner', [OfferController::class, 'offerBanner']);
Route::get('offer/product/list', [OfferController::class, 'offerProduct']);

Route::get('product/popular/category', [ProductCategoryController::class, 'popularCategory']);
Route::get('section/product', [ProductController::class, 'sectionProductList']);
Route::get('all/subcategory', [ProductSubcategoryController::class, 'allSubcategory']);
Route::get('all/category/subcategory', [ProductCategoryController::class, 'allCategorySubcategory']);
Route::post('product/price/range/src', [ProductController::class, 'priceRangeSrc']);
Route::get('country/list', [StorageController::class, 'countryList']);
Route::get('shipping/cost/get', [SettingController::class, 'shippingCost']);
Route::get('currency/get', [SettingController::class, 'currency']);
Route::get('division/list', [StorageController::class, 'divisionList']);
Route::get('district/list', [StorageController::class, 'districtList']);
Route::get('product/size/list', [ProductController::class, 'productSizList']);
Route::get('product/color/list', [ProductController::class, 'productColorList']);
Route::get('product/company/info', [CompanyInfoController::class, 'getCompanyInfo']);
Route::get('product/price/min/max', [ProductController::class, 'minMaxPrice']);

Route::get('product/all/color', [ProductController::class, 'allColor']);
Route::get('product/all/size', [ProductController::class, 'allSize']);
Route::get('product/all/option', [ProductController::class, 'allOptions']);
Route::get('product/all/variants', [ProductController::class, 'allvariantss']);
Route::get('product/all/brand', [BrandController::class, 'allBrand']);
Route::get('product/top/brand', [BrandController::class, 'topBrand']);

Route::get('product/all/category', [ProductCategoryController::class, 'allCategory']);
Route::get('featured/link/list', [SettingController::class, 'featuredList']);

Route::get('faq/list', [SettingController::class, 'getFaq']);
Route::get('ads/list', [SettingController::class, 'getAds']);

Route::get('bundles', [BundleController::class, 'index']);
Route::get('bundles/getByID/{id}', [BundleController::class, 'getByID']);
Route::post('bundles/getBySlug', [BundleController::class, 'getBySlug']);