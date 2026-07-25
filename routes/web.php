<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BankAccountController;
use App\Http\Controllers\Admin\CashAccountController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompanyInfoController;
use App\Http\Controllers\Admin\FeaturedLinkController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\offerController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductSubcategoryController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\SupplierController;

use App\Http\Controllers\Admin\ReceiptVoucherController;
use App\Http\Controllers\Admin\PaymentVoucherController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\VariantController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Api\AuthController;

use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\RiderController;
use App\Http\Controllers\Admin\RiderPaymentContoller;
use App\Http\Controllers\Admin\LidOptionController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\QuotationController;
use App\Http\Controllers\Admin\BundleController;
use App\Http\Controllers\Admin\WebsiteSettingController;
use App\Http\Controllers\Admin\PurchaseReceivedController;

use App\Http\Controllers\Admin\SaleAndOrderController;
use App\Http\Controllers\Admin\PartyController;
use App\Http\Controllers\Admin\VoucherController;

use App\Models\Supplier;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\DatabaseBackupController;


Route::get('run_shedule_command', function () {
    Artisan::call('sms:send-sms');
    Artisan::call('schedule:run');
});

Route::get('login/google', [AuthController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::get('auth/facebook/callback', [AuthController::class, 'handleFacebookCallback']);
Route::get('/google/logiin', function () {
    return view('login');
});

Route::get('/auth/facebook/redirect', [AuthController::class, 'redirectToFacebook'])->name('facebook.redirect');

Route::prefix('admin')->group(function () {
    Route::get('/export-database', [DatabaseBackupController::class, 'exportDatabase']);

    Route::get('migrate', function () {
        echo Artisan::call('migrate');
        echo 'All migration run successfully';
    });

    Route::get('whatsapp-test/{number}/{s}', function ($number, $s) {

        $whatsapp = new \App\Services\WhatsAppService;

        $templates = [
            1 => [
                'template' => \App\Services\WhatsAppService::ORDER_PLACED,
                'params' => ["DB User", "ORD-1222112", "-", 500]
            ],
            2 => [
                'template' => \App\Services\WhatsAppService::ORDER_CANCELLED,
                'params' => ["DB User", "1000", "15"]
            ],
            3 => [
                'template' => \App\Services\WhatsAppService::ORDER_DELIVERED,
                'params' => ["DB User", "1000"]
            ],
        ];

        if (!isset($templates[$s])) {
            return response()->json(['status' => 'error', 'message' => 'Invalid parameter s'], 400);
        }

        $res = $whatsapp
            ->to($number)
            ->template($templates[$s]['template'])
            ->params($templates[$s]['params'])
            ->send();

        return response()->json(['status' => 'success', 'response' => $res]);
    });



    Route::get('/', [AdminController::class, 'loginView'])->name('login');

    Route::post('admin/login', [AdminController::class, 'loginAdmin'])->name('admin.login');

    Route::group(['middleware' => 'authCheck'], function () {
        Route::get('/blogs/index', [BlogController::class, 'index'])->name('blogs.list');
        Route::get('/blogs/create', [BlogController::class, 'create'])->name('blogs.create');
        Route::post('/blog/store', [BlogController::class, 'storeBlog'])->name('admin.store.blog');
        Route::get('/blogs/edit/{id}', [BlogController::class, 'blogEditInfo'])->name('blogs.edit');
        Route::post('/blog/update', [BlogController::class, 'updateBlog'])->name('admin.update.blog');
        Route::get('/blog/delete', [BlogController::class, 'blogDelete'])->name('admin.delete.blog');
        Route::get('/blog/restore', [BlogController::class, 'blogRestore'])->name('admin.restore.blog');

        //Pages Routes Start

        Route::get('/pages/index', [PageController::class, 'index'])->name('pages.list');

        Route::get('/page/create', [PageController::class, 'create'])->name('page.create');

        Route::post('/page/slug/validate', [PageController::class, 'pageSlugValidate'])->name('page.slug.validate');

        Route::post('/page/store', [PageController::class, 'store'])->name('page.store');

        Route::get('/page/edit/{id}', [PageController::class, 'edit'])->name('page.edit');

        Route::post('/page/update/{id}', [PageController::class, 'update'])->name('page.update');

        Route::get('/page/delete/{id}', [PageController::class, 'destroy'])->name('page.delete');

        //Pages Routes End

        Route::get('/areas/index', [AreaController::class, 'index'])->name('areas.list');
        Route::get('/areas/create', [AreaController::class, 'create'])->name('areas.create');
        Route::post('/areas/store', [AreaController::class, 'storeareas'])->name('admin.store.areas');
        Route::get('/areas/edit/{id}', [AreaController::class, 'areasEditInfo'])->name('areas.edit');
        Route::post('/areas/update', [AreaController::class, 'updateareas'])->name('admin.update.areas');
        Route::get('/areas/delete', [AreaController::class, 'areasDelete'])->name('admin.delete.areas');

        Route::post('/areas/import/excel', [AreaController::class, 'importExcel'])->name('admin.areas.import.excel');

        Route::get('/home', [HomeController::class, 'index'])->name('home');
        Route::get('/create/product/view', [ProductController::class, 'createProduct'])->name('admin.create.product');
        Route::post('/product/slug/validate', [ProductController::class, 'productSlugValidate'])->name(
            'product.slug.validate',
        );
        Route::post('/product/store', [ProductController::class, 'storeProduct'])->name('admin.store.product');
        Route::get('/product/list', [ProductController::class, 'productList'])->name('admin.product.list');
        Route::get('product/details', [ProductController::class, 'ProductOrderDetails'])->name('admin.product.detail');
        Route::get('product/edit', [ProductController::class, 'ProductEdit'])->name('admin.product.edit');
        Route::post('product/export', [ProductController::class, 'ProductExport'])->name('admin.product.export');

        Route::get('/product/delete', [ProductController::class, 'productDelete'])->name('admin.delete.product');
        Route::get('/product/restore', [ProductController::class, 'productRestore'])->name('admin.restore.product');

        Route::get('/product/edit/info', [ProductController::class, 'productEditDetails'])->name('product.edit.info');
        Route::get('/product/image/delete', [ProductController::class, 'imageDelete'])->name('product.image.delete');
        Route::put('/product/update/{id}', [ProductController::class, 'updateProduct'])->name('admin.update.product');
        Route::post('/product/remove-product-image', [ProductController::class, 'removeProductImage'])->name(
            'admin.removeProductImage',
        );
        Route::get('/product/color', [ProductController::class, 'productColor'])->name('admin.product.color.show');
        Route::post('/product/color/store', [ProductController::class, 'productColorStore'])->name(
            'admin.product.color.store',
        );
        Route::post('/product/color/update', [ProductController::class, 'productColorUpdate'])->name(
            'admin.product.color.update',
        );

        Route::get('/search/product-by-name-or-brand', [ProductController::class, 'searchProductByNameBrand'])->name(
            'admin.search.product.by.name.or.brand',
        );

        Route::get('/product/get-by-id', [ProductController::class, 'getProductByID'])->name('admin.get.product.by.id');

        Route::get('/products-sample-file', function () {
            return response()->download(public_path('products-sample-file.xlsx'));
        })->name('admin.products-sample-file');

        Route::get('product/lids/all', [LidOptionController::class, 'index'])->name('product.lids.index');
        Route::get('product/lids/create', [LidOptionController::class, 'create'])->name('product.lids.create');
        Route::post('product/lids/store', [LidOptionController::class, 'store'])->name('product.lids.store');
        Route::get('product/lids/{id}/edit', [LidOptionController::class, 'edit'])->name('product.lids.edit');
        Route::put('product/lids/{id}/update', [LidOptionController::class, 'update'])->name('product.lids.update');
        Route::delete('product/lids/{id}/delete', [LidOptionController::class, 'destroy'])->name(
            'product.lids.destroy',
        );

        Route::post('/product/price-import', [ProductController::class, 'priceImport'])->name('product.prices.import');
        Route::post('/product/productsImport', [ProductController::class, 'productsImport'])->name(
            'product.productsImport',
        );
        Route::get('/product/productsExport', [ProductController::class, 'productsExport'])->name(
            'product.productsExport',
        );

        Route::get('/product/productVariantsExport', [ProductController::class, 'productVariantsExport'])->name(
            'product.productVariantsExport',
        );
        Route::post('/product/productVariantsImport', [ProductController::class, 'productVariantsImport'])->name(
            'product.productVariantsImport',
        );

        Route::post('/product/toggleStockStatus', [ProductController::class, 'toggleStockStatus'])->name(
            'product.toggleStockStatus',
        );

        //Expense

        Route::get('expenses', [ExpenseController::class, 'index'])->name('expenses.index');
        Route::get('expense/create', [ExpenseController::class, 'create'])->name('expenses.create');
        Route::post('expense/store', [ExpenseController::class, 'store'])->name('expenses.store');
        Route::get('expense/{id}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
        Route::put('expense/{id}/update', [ExpenseController::class, 'update'])->name('expenses.update');
        Route::delete('expense/{id}/delete', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

        Route::delete('expense/{id}/expenseActive', [ExpenseController::class, 'expenseActive'])->name(
            'expense.expenseActive',
        );
        Route::delete('expense/{id}/expenseInactive', [ExpenseController::class, 'expenseInactive'])->name(
            'expense.expenseInactive',
        );

        Route::get('expense/expenseAccountCreate', [ExpenseController::class, 'expenseAccountCreate'])->name(
            'expenses.expense-account-create',
        );
        Route::post('expense/expenseAccountstore', [ExpenseController::class, 'expenseAccountstore'])->name(
            'expenses.expenseAccountstore',
        );
        Route::get('expense/expenseAccountIndex', [ExpenseController::class, 'expenseAccountIndex'])->name(
            'expense.expenseAccountIndex',
        );
        Route::get('expense/{id}/expenseAccountEdit', [ExpenseController::class, 'expenseAccountEdit'])->name(
            'expense.expenseAccountEdit',
        );
        Route::put('expense/{id}/expenseAccountUpdate', [ExpenseController::class, 'expenseAccountUpdate'])->name(
            'expense.expenseAccountUpdate',
        );
        Route::delete('expense/{id}/expenseAccountActive', [ExpenseController::class, 'expenseAccountActive'])->name(
            'expense.expenseAccountActive',
        );
        Route::delete('expense/{id}/expenseAccountInactive', [
            ExpenseController::class,
            'expenseAccountInactive',
        ])->name('expense.expenseAccountInactive');
        //Expense X

        // Quotations

        Route::prefix('quotations')
            ->name('quotations.')
            ->controller(QuotationController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('create', 'create')->name('create');
                Route::get('addProductToQuotationList', 'addProductToQuotationList')->name('addProductToQuotationList');
                Route::post('store', 'store')->name('store');
                Route::get('{id}/edit', 'edit')->name('edit');
                Route::put('{id}/update', 'update')->name('update');
                Route::delete('{id}/delete', 'destroy')->name('destroy');
                Route::get('{id}/show', 'show')->name('show');
                Route::post('{id}/createSell', 'createSell')->name('create.sell');
                Route::get('{id}/quotationInvoice', 'quotationInvoice')->name('quotationInvoice');
            });

        //Quotations X

        // Bundles

        Route::prefix('bundles')
            ->name('bundles.')
            ->controller(BundleController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('create', 'create')->name('create');
                Route::post('store', 'store')->name('store');
                Route::get('{id}/edit', 'edit')->name('edit');
                Route::put('{id}/update', 'update')->name('update');
                Route::delete('{id}/delete', 'destroy')->name('destroy');
                Route::get('{id}/show', 'show')->name('show');

                Route::get('searchProductForBundle', 'searchProductForBundle')->name('searchProductForBundle');
                Route::get('addProductToBundle', 'addProductToBundle')->name('addProductToBundle');
            });

        //Bundles X

        // Website Settings

        Route::prefix('website-settings')
            ->name('website.settings.')
            ->controller(WebsiteSettingController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('create', 'create')->name('create');
                Route::post('store', 'store')->name('store');
                Route::get('{id}/edit', 'edit')->name('edit');
                Route::put('{id}/update', 'update')->name('update');
            });

        //Website Settings X

        Route::get('/product/size', [ProductController::class, 'productSize'])->name('admin.product.size.show');
        Route::post('/product/size/store', [ProductController::class, 'productSizeStore'])->name(
            'admin.product.size.store',
        );
        Route::post('/product/size/update', [ProductController::class, 'productSizeUpdate'])->name(
            'admin.product.size.update',
        );
        Route::post('/product/saveNewPackSize', [ProductController::class, 'saveNewPackSize'])->name(
            'admin.product.saveNewPackSize',
        );

        Route::get('/product/option', [ProductController::class, 'productOption'])->name('admin.product.option.show');
        Route::post('/product/option/store', [ProductController::class, 'productOptionStore'])->name(
            'admin.product.option.store',
        );
        Route::post('/product/option/update', [ProductController::class, 'productOptionUpdate'])->name(
            'admin.product.option.update',
        );

        Route::get('/product/variants', [ProductController::class, 'productVariant'])->name(
            'admin.product.variants.show',
        );
        Route::post('/product/variants/store', [ProductController::class, 'productVariantStore'])->name(
            'admin.product.variants.store',
        );
        Route::post('/product/variants/update', [ProductController::class, 'productVariantUpdate'])->name(
            'admin.product.variants.update',
        );

        Route::get('/product/category', [ProductCategoryController::class, 'productCategory'])->name(
            'admin.product.category',
        );
        Route::get('/product/category/sort-orders', [ProductCategoryController::class, 'showCategoriesSortOrders'])->name(
            'admin.product.category.sort.orders',
        );
        Route::post('/product/category/sort-orders/store', [ProductCategoryController::class, 'storeCategoriesSortOrders'])->name(
            'admin.product.category.sort.orders.store',
        );

        Route::post('/product/category/slug/validate', [
            ProductCategoryController::class,
            'productCategorySlugValidate',
        ])->name('category.slug.validate');
        Route::post('/product/category/serial-no/validate', [
            ProductCategoryController::class,
            'productCategorySerialNoValidate',
        ])->name('category.serial_no.validate');

        Route::post('/product/category/store', [ProductCategoryController::class, 'productCategoryStore'])->name(
            'admin.store.category',
        );
        Route::post('/product/category/update', [ProductCategoryController::class, 'productCategoryUpdate'])->name(
            'admin.update.category',
        );
        Route::get('/product/category/inactive', [ProductCategoryController::class, 'productCategoryInactive'])->name(
            'admin.inactive.category',
        );
        Route::get('/product/category/active', [ProductCategoryController::class, 'productCategoryActive'])->name(
            'admin.active.category',
        );

        Route::get('/product/brand', [BrandController::class, 'brandShow'])->name('admin.product.brand');
        Route::post('/product/brand/store', [BrandController::class, 'brandStore'])->name('admin.product.brand.store');

        Route::post('/product/brand/update', [BrandController::class, 'brandUpdate'])->name(
            'admin.product.brand.update',
        );

        Route::get('/product/subcategory', [ProductSubcategoryController::class, 'productSubcategory'])->name(
            'admin.product.subcategory',
        );
        Route::post('/product/subcategory/store', [
            ProductSubcategoryController::class,
            'productSubCategoryStore',
        ])->name('admin.store.subcategory');
        Route::post('/product/subcategory/update', [
            ProductSubcategoryController::class,
            'productSubCategoryUpdate',
        ])->name('admin.update.subcategory');
        Route::get('/product/subcategory/delete', [
            ProductSubcategoryController::class,
            'productSubCategoryDelete',
        ])->name('admin.delete.subcategory');
        Route::get('/product/subcategory/list/get', [ProductSubcategoryController::class, 'subcategoryListGet'])->name(
            'subcategory.list.get',
        );

        Route::get('/supplier/list', [SupplierController::class, 'supplierList'])->name('admin.supplier.list');
        Route::post('/supplier/store', [SupplierController::class, 'supplierStore'])->name('admin.store.supplier');
        Route::get('/supplier/edit/info', [SupplierController::class, 'supplierEditInfo'])->name('supplier.edit.info');
        Route::post('/supplier/update', [SupplierController::class, 'supplierUpdate'])->name('admin.update.supplier');
        Route::get('/supplier/toggleStatus/{id}', [SupplierController::class, 'toggleStatus'])->name(
            'admin.toggleStatus.supplier',
        );

        Route::get('/bank/list', [BankAccountController::class, 'bankList'])->name('admin.bank.list');
        Route::post('/bank/store', [BankAccountController::class, 'bankStore'])->name('admin.store.bank');
        Route::post('/bank/update', [BankAccountController::class, 'bankUpdate'])->name('admin.update.bank');

        Route::delete('/bank/{id}/bankActive', [BankAccountController::class, 'bankActive'])->name(
            'admin.bank.bankActive',
        );
        Route::delete('/bank/{id}/bankInactive', [BankAccountController::class, 'bankInactive'])->name(
            'admin.bank.bankInactive',
        );

        Route::get('/cash/list', [CashAccountController::class, 'cashList'])->name('admin.cash.list');
        Route::post('/cash/store', [CashAccountController::class, 'cashStore'])->name('admin.store.cash');
        Route::post('/cash/update', [CashAccountController::class, 'cashUpdate'])->name('admin.update.cash');

        Route::delete('/cash/{id}/cashActive', [CashAccountController::class, 'cashActive'])->name(
            'admin.cash.cashActive',
        );
        Route::delete('/cash/{id}/cashInactive', [CashAccountController::class, 'cashInactive'])->name(
            'admin.cash.cashInactive',
        );

        Route::get('/pos/customer', [PosController::class, 'posCustomerList'])->name('admin.pos.customer.list');
        Route::post('/pos/customer/store', [PosController::class, 'posCustomerStore'])->name(
            'admin.store.pos.customer',
        );
        Route::get('/pos/customer/store/in-pos', [PosController::class, 'posCustomerStoreInPos'])->name(
            'admin.pos.customer.add.in-pos',
        );
        Route::post('/pos/customer/update', [PosController::class, 'posCustomerUpdate'])->name(
            'admin.pos.customer.update',
        );

        Route::delete('/pos/customer/{id}/posCustomerActive', [PosController::class, 'posCustomerActive'])->name(
            'admin.pos.customer.customerActive',
        );
        Route::delete('/pos/customer/{id}/posCustomerInactive', [PosController::class, 'posCustomerInactive'])->name(
            'admin.pos.customer.customerInactive',
        );

        Route::get('/pos/view', [PosController::class, 'posView'])->name('admin.pos.view');
        Route::get('/pos/product/get', [PosController::class, 'getPostProductList'])->name('admin.pos.product.get');
        Route::get('/pos/salesOrders', [PosController::class, 'salesOrders'])->name('admin.pos.salesOrders');
        Route::get('/pos/createSalesOrder', [PosController::class, 'createSalesOrder'])->name(
            'admin.pos.createSalesOrder',
        );
        Route::post('/pos/storeSalesOrder', [PosController::class, 'storeSalesOrder'])->name(
            'admin.pos.storeSalesOrder',
        );
        Route::post('/pos/getSalesOrderInfo', [PosController::class, 'getSalesOrderInfo'])->name(
            'admin.pos.getSalesOrderInfo',
        );
        Route::post('/pos/getCustomerInfoHTML', [PosController::class, 'getCustomerInfoHTML'])->name(
            'admin.pos.getCustomerInfoHTML',
        );

        // Route::get('/pos/product/variants/get', [PosController::class, 'getProductVariants'])->name('admin.pos.sell.get.product.variants');

        Route::get('/pos/product/src/get', [PosController::class, 'postProductSearch'])->name('admin.pos.product.src');

        Route::get('/pos/sell/item/get', [PosController::class, 'sellItemGet'])->name('admin.pos.sell.item.get');

        // Route::get('/pos/sell/item/variant/get/to-order-list', [PosController::class, 'sellItemVariantGetToOrderList'])->name('admin.pos.sell.item.variant.get.to.order.list');

        Route::post('/pos/payment/store', [PosController::class, 'posPaymentStore'])->name('pos.payment.store');

        Route::post('/pos/sellAllocateRider', [PosController::class, 'sellAllocateRider'])->name(
            'pos.sellAllocateRider',
        );

        // Route::post('/pos/payment/variants/store', [PosController::class, 'posPaymentStoreVariants'])->name('pos.payment.variants.store');

        Route::get('/product/stock/view', [PurchaseController::class, 'purchaseProductView'])->name(
            'admin.product.purchase',
        );

        Route::get('/product/purchase/list', [PurchaseController::class, 'purchaseList'])->name(
            'admin.product.purchase.list',
        );

        Route::get('/purchase/supplier/store', [SupplierController::class, 'purchaseSupplierStore'])->name(
            'admin.supplier.store.form.purchase',
        );
        Route::get('/purchase/item/get', [SupplierController::class, 'purchaseItemGet'])->name(
            'admin.pos.purchase.item.get',
        );

        Route::get('/purchase/item/variant/get', [SupplierController::class, 'purchaseItemVariantGet'])->name(
            'admin.pos.purchase.item.variant.get',
        );

        Route::post('/purchase/payment/store', [PurchaseController::class, 'purchasePaymentStore'])->name(
            'purchase.payment.store',
        );

        Route::get('/purchase/invoice', [PurchaseController::class, 'purchaseInvoice'])->name('purchase.invoice');

        Route::get('/post/sell/list', [PosController::class, 'sellList'])->name('sell.list');

        Route::post('/pos/sell/sellsMultiAction', [PosController::class, 'sellsMultiAction'])->name(
            'sell.sellsMultiAction',
        );

        Route::get('/post/offer/list', [offerController::class, 'offerList'])->name('offer.list');

        Route::post('admin/store/offer', [offerController::class, 'storeOffer'])->name('admin.store.offer');
        Route::get('admin/set/offer/product', [offerController::class, 'setOfferProduct'])->name(
            'admin.set.offer.product',
        );

        Route::get('admin/offer/product/list', [offerController::class, 'offerProductList'])->name(
            'admin.offer.product.list',
        );

        Route::post('admin/offer/product/store', [offerController::class, 'storeOfferProduct'])->name(
            'admin.offer.product.store',
        );

        Route::get('admin/product/offerProduct/delete', [offerController::class, 'offerProductDelete'])->name(
            'admin.product.offerProduct.delete',
        );

        Route::get('admin/delete/offer/banner', [offerController::class, 'offerBannerDelete'])->name(
            'admin.delete.offer.banner',
        );
        Route::post('admin/update/offer', [offerController::class, 'offerBannerUpdate'])->name('admin.update.offer');

        Route::get('sell/invoice', [PosController::class, 'sellInvoice'])->name('sell.invoice');

        Route::get('/addReturnInvoiceForm', [PosController::class, 'addReturnInvoiceForm']);
        Route::post('admin/addReturnInvoiceStore', [PosController::class, 'addReturnInvoiceStore'])->name(
            'admin.addReturnInvoiceStore',
        );

        Route::get('product/barcode/generate', [ProductController::class, 'productBarcodeGenerate'])->name(
            'product.barcode.generate',
        );

        Route::get('admin/order/ecommerce/all', [OrderController::class, 'ecommerceOrderList'])->name(
            'admin.ecommerce.order.list',
        );
        Route::get('admin/order/ecommerce/customer/all', [OrderController::class, 'ecommerceCustomerList'])->name(
            'admin.ecommerce.customer.list',
        );

        Route::get('admin/order/status/update', [OrderController::class, 'OrderStatusUpdate'])->name(
            'admin.order.status.update',
        );

        Route::get('admin/order/pay-status/update', [OrderController::class, 'OrderPayStatusUpdate'])->name(
            'admin.order.pay.status.update',
        );

        Route::post('admin/order/allocate-rider', [OrderController::class, 'allocateRider'])->name(
            'admin.order.allocate.rider',
        );

        Route::post('order/ordersMultiAction', [OrderController::class, 'ordersMultiAction'])->name(
            'admin.order.ordersMultiAction',
        );

        Route::get('admin/sell/order/details', [OrderController::class, 'SellOrderDetails'])->name(
            'admin.order.detail',
        );
        Route::get('admin/setting/shipping/rate', [SettingController::class, 'shippingRate'])->name(
            'setting.shipping.rate',
        );
        Route::get('admin/report/product/sell', [ReportController::class, 'sellReport'])->name('admin.report.sell');
        Route::get('admin/report/product/sell/profit', [ReportController::class, 'sellProfitReport'])->name(
            'admin.report.sell.profit',
        );
        Route::get('admin/report/customerLedger', [ReportController::class, 'customerLedgerReport'])->name(
            'admin.report.customerLedger',
        );
        Route::get('admin/report/supplierLedger', [ReportController::class, 'supplierLedgerReport'])->name(
            'admin.report.supplierLedger',
        );
        Route::get('admin/report/bankLedger', [ReportController::class, 'bankLedgerReport'])->name(
            'admin.report.bankLedger',
        );
        Route::get('admin/report/cashLedger', [ReportController::class, 'cashLedgerReport'])->name(
            'admin.report.cashLedger',
        );

        Route::get('/report/product-report', [ReportController::class, 'productReport'])->name('admin.report.product');
        Route::get('/report/purchase-report', [ReportController::class, 'purchaseReport'])->name(
            'admin.report.purchase',
        );
        Route::get('/report/order-report', [ReportController::class, 'orderReport'])->name('admin.report.order');
        Route::get('/report/sells-order-report', [ReportController::class, 'sellOrderReport'])->name(
            'admin.report.sell.order',
        );
        Route::get('/report/stock-summary-report', [ReportController::class, 'stockSummaryReport'])->name(
            'admin.report.stock-summary-report',
        );
        Route::get('/report/stock-summary-report-pdf', [ReportController::class, 'stockSummaryReportPdf'])->name(
            'admin.report.stock-summary-report-pdf',
        );
        Route::get('/report/simple-stock-report', [ReportController::class, 'simpleStockReport'])->name(
            'admin.report.simple-stock-report',
        );
        Route::get('/report/detail-stock-report', [ReportController::class, 'detailStockReport'])->name(
            'admin.report.detail-stock-report',
        );
        Route::get('/report/brand-report', [ReportController::class, 'brandReport'])->name('admin.report.brand');
        Route::get('/report/variant-report', [ReportController::class, 'variantReport'])->name('admin.report.variant');
        Route::get('/report/sellAndOrderReport', [ReportController::class, 'sellAndOrderReport'])->name(
            'admin.report.sellAndOrderReport',
        );
        Route::get('/report/partyLedgerReport', [ReportController::class, 'partyLedgerReport'])->name(
            'admin.report.partyLedgerReport',
        );

        Route::get('admin/district/list/get', [SettingController::class, 'districtList']);
        Route::post('admin/shipping/cost/setting/store', [SettingController::class, 'shippingCostStore'])->name(
            'shipping.cost.setting.store',
        );
        Route::post('admin/currency/setting/store', [SettingController::class, 'currencyCostStore'])->name(
            'currency.setting.store',
        );
        Route::post('admin/company/info', [CompanyInfoController::class, 'CompanyInfo'])->name('company.info.store');

        //Route::get('admin/setting/company/details', [CompanyInfoController::class, 'companyDetails'])->name('setting.company.details');

        Route::get('admin/logout', [AdminController::class, 'logOutAdmin'])->name('admin.logout');
        Route::get('admin/role', [AdminController::class, 'adminRole'])->name('admin.role.create');
        Route::post('admin/role/store', [AdminController::class, 'adminRoleStore'])->name('admin.role.store');
        Route::get('admin/create', [AdminController::class, 'adminCreate'])->name('admin.admin.create');
        Route::post('admin/store', [AdminController::class, 'adminStore'])->name('admin.admin.store');
        Route::get('admin/delete', [AdminController::class, 'adminDelete'])->name('admin.admin.delete');
        Route::get('admin/setting/company/details', [CompanyInfoController::class, 'companyDetails'])->name(
            'setting.company.details',
        );
        Route::get('/admin/edit/info', [AdminController::class, 'adminEditInfo'])->name('admin.edit.info');
        Route::post('/admin/update', [AdminController::class, 'adminUpdate'])->name('admin.update.admin');

        Route::get('admin/featured/link/list', [FeaturedLinkController::class, 'featuredLinkList'])->name(
            'admin.featured.link.list',
        );
        Route::post('admin/featured/store', [FeaturedLinkController::class, 'featuredLinkStore'])->name(
            'admin.featured.store',
        );
        Route::post('admin/featured/update', [FeaturedLinkController::class, 'featuredLinkUpdate'])->name(
            'admin.featured.update',
        );

        Route::get('admin/faq', [SettingController::class, 'faqView'])->name('faq.view');
        Route::post('admin/faq/store', [SettingController::class, 'faqStore'])->name('faq.store');
        Route::post('admin/faq/edit', [SettingController::class, 'faqEdit'])->name('faq.edit');
        Route::get('admin/faq/deleted', [SettingController::class, 'faqDelete'])->name('faq.delete');

        Route::get('admin/ads', [SettingController::class, 'adsView'])->name('ads.view');
        Route::post('admin/ads/store', [SettingController::class, 'adsStore'])->name('ads.store');
        Route::post('admin/ads/edit', [SettingController::class, 'adsEdit'])->name('admin.update.ads');
        Route::get('admin/ads/deleted', [SettingController::class, 'adsDelete'])->name('ads.delete');

        Route::get('/receipt/list', [ReceiptVoucherController::class, 'admin_receipt_list'])->name(
            'admin.receipt.list',
        );
        Route::get('/addReceiptVoucherForm', [ReceiptVoucherController::class, 'addReceiptVoucherForm'])->name(
            'admin.addReceiptVoucherForm',
        );
        Route::get('/receipt/loadCustomerCurrentBalance', [
            ReceiptVoucherController::class,
            'loadCustomerCurrentBalance',
        ])->name('admin.receipt.loadCustomerCurrentBalance');
        Route::get('/viewReceiptVoucherDetail', [ReceiptVoucherController::class, 'viewReceiptVoucherDetail'])->name(
            'admin.viewReceiptVoucherDetail',
        );
        Route::post('/receipt/store', [ReceiptVoucherController::class, 'receiptStore'])->name('admin.receipt.store');

        Route::get('/payment/list', [PaymentVoucherController::class, 'admin_payment_list'])->name(
            'admin.payment.list',
        );
        Route::get('/addPaymentVoucherForm', [PaymentVoucherController::class, 'addPaymentVoucherForm'])->name(
            'admin.addPaymentVoucherForm',
        );
        Route::get('/payment/loadSupplierCurrentBalance', [
            PaymentVoucherController::class,
            'loadSupplierCurrentBalance',
        ])->name('admin.payment.loadSupplierCurrentBalance');
        Route::post('/payment/store', [PaymentVoucherController::class, 'paymentStore'])->name('admin.payment.store');
        Route::get('/viewPaymentVoucherDetail', [PaymentVoucherController::class, 'viewPaymentVoucherDetail'])->name(
            'admin.viewPaymentVoucherDetail',
        );

        Route::post('/notifications/mark-as-read', [NotificationController::class, 'markNotificationAsRead'])->name(
            'notification.mark.as.read',
        );

        Route::get('discounts/all', [DiscountController::class, 'index'])->name('discounts.index');
        Route::get('discount/create', [DiscountController::class, 'create'])->name('discounts.create');
        Route::post('discount/store', [DiscountController::class, 'store'])->name('discounts.store');
        Route::get('discount/{id}/show', [DiscountController::class, 'show'])->name('discounts.show');
        Route::get('discount/{id}/edit', [DiscountController::class, 'edit'])->name('discounts.edit');
        Route::put('discount/{id}/update', [DiscountController::class, 'update'])->name('discounts.update');
        Route::delete('discount/{id}/delete', [DiscountController::class, 'destroy'])->name('discounts.destroy');
        Route::post('/admin/fetch-products-by-categories', [DiscountController::class, 'fetchProducts'])->name(
            'discounts.fetch-products',
        );
        Route::post('/discounts/{id}/duplicate', [DiscountController::class, 'duplicate'])->name('discounts.duplicate');

        Route::get('riders/all', [RiderController::class, 'index'])->name('riders.index');
        Route::get('riders/create', [RiderController::class, 'create'])->name('riders.create');
        Route::post('riders/store', [RiderController::class, 'store'])->name('riders.store');
        Route::get('riders/{id}/show', [RiderController::class, 'show'])->name('riders.show');
        Route::get('riders/{id}/edit', [RiderController::class, 'edit'])->name('riders.edit');
        Route::put('riders/{id}/update', [RiderController::class, 'update'])->name('riders.update');
        Route::delete('riders/{id}/delete', [RiderController::class, 'destroy'])->name('riders.destroy');

        //Rider Payments
        Route::get('rider-payments/all', [RiderPaymentContoller::class, 'index'])->name('rider.payments.index');
        Route::get('rider-payments/create', [RiderPaymentContoller::class, 'create'])->name('rider.payments.create');
        Route::post('rider-payments/store', [RiderPaymentContoller::class, 'store'])->name('rider.payments.store');
        Route::get('rider-payments/{id}/edit', [RiderPaymentContoller::class, 'edit'])->name('rider.payments.edit');
        Route::post('rider-payments/{id}/update', [RiderPaymentContoller::class, 'update'])->name(
            'rider.payments.update',
        );
        Route::post('rider-payments/{id}/toggleStatus', [RiderPaymentContoller::class, 'toggleStatus'])->name(
            'rider.payments.toggleStatus',
        );
        Route::get('rider-payments/getRiderBalance', [RiderPaymentContoller::class, 'getRiderBalance'])->name(
            'rider.payments.getRiderBalance',
        );

        Route::prefix('purchase-received')
            ->name('purchase.received.')
            ->controller(PurchaseReceivedController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('create', 'create')->name('create');
                Route::post('store', 'store')->name('store');

                Route::get('{id}/pdf/download', 'pdfDownload')->name('pdf.download');

                // Route::get('{id}/edit', 'edit')->name('edit');
                // Route::put('{id}/update', 'update')->name('update');
                // Route::delete('{id}/delete', 'destroy')->name('destroy');
                // Route::get('{id}/show', 'show')->name('show');

                Route::get('loadPurchaseOrderDetails', 'loadPurchaseOrderDetails')->name('loadPurchaseOrderDetails');
            });

        Route::prefix('web/orders')->group(function () {
            Route::get('/', [SaleAndOrderController::class, 'index'])->name('web.orders.index');
            Route::post('/allocateRider', [SaleAndOrderController::class, 'allocateRider'])
                ->name('web.orders.allocateRider');

            Route::post('/multiActionForm', [SaleAndOrderController::class, 'multiActionForm'])
                ->name('web.orders.multiActionForm');
        });
    });

    Route::get('/parties', [PartyController::class, 'index'])->name('admin.parties.index');
    Route::get('/parties/create', [PartyController::class, 'create'])->name('admin.parties.create');
    Route::post('/parties/store', [PartyController::class, 'store'])->name('admin.parties.store');
    Route::get('/parties/{id}/edit', [PartyController::class, 'edit'])->name('admin.parties.edit');
    Route::post('/parties/{id}/update', [PartyController::class, 'update'])->name('admin.parties.update');
    Route::post('/parties/{id}/destroy', [PartyController::class, 'destroy'])->name('admin.parties.destroy');

    Route::get('/vouchers', [VoucherController::class, 'index'])->name('admin.vouchers.index');
    Route::post('/vouchers/voucherStore', [VoucherController::class, 'voucherStore'])->name(
        'admin.vouchers.voucherStore',
    );
    Route::get('/vouchers/loadPartyBalance', [VoucherController::class, 'loadPartyBalance'])->name(
        'admin.vouchers.loadPartyBalance',
    );

    Route::get('/vouchers/inOutToAccount', [VoucherController::class, 'inOutToAccount'])->name(
        'admin.vouchers.inOutToAccount',
    );
    Route::post('/vouchers/inOutToAccountStore', [VoucherController::class, 'inOutToAccountStore'])->name(
        'admin.vouchers.inOutToAccountStore',
    );
    Route::get('/vouchers/loadAccountCurrentBalance', [VoucherController::class, 'loadAccountCurrentBalance'])->name(
        'admin.vouchers.loadAccountCurrentBalance',
    );

    Route::get('/vouchers/extraTransaction', [VoucherController::class, 'extraTransaction'])->name(
        'admin.vouchers.extraTransaction',
    );
    Route::post('/vouchers/extraTransactionStore', [VoucherController::class, 'extraTransactionStore'])->name(
        'admin.vouchers.extraTransactionStore',
    );
    Route::get('/vouchers/loadPartyExtraBalance', [VoucherController::class, 'loadPartyExtraBalance'])->name(
        'admin.vouchers.loadPartyExtraBalance',
    );
});

Route::get('/{any}', function () {
    return view('frontend.index ');
})->where('any', '.*');
