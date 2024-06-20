<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\adminLoginController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\DiscountController;
use App\Http\Controllers\admin\PageController;
use Illuminate\Support\Str;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\ShippingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\TempImagesController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::view('/socket', 'others/socket');

Route::get('/test', function () {
    //orderEmail(12);
});

Route::controller(StripePaymentController::class)->group(function(){
    Route::get('stripe', 'stripe')->name('stripe.index');
    Route::post('stripe', 'stripePost')->name('stripe.post');
});


Route::get('/', [FrontController::class, 'index'])->name('front.home');
Route::get('/shop/{categorySlug?}/{subCategorySlug?}', [ShopController::class, 'index'])->name('front.shop');
Route::get('/product/{slug}', [ShopController::class, 'product'])->name('front.product');
Route::get('/cart', [CartController::class, 'cart'])->name('front.cart');
Route::post('/add-to-cart', [CartController::class, 'addToCart'])->name('front.addToCart');
Route::post('/update-cart', [CartController::class, 'updateCart'])->name('front.updateCart');
Route::post('/delete-cart-item', [CartController::class, 'deleteCartItem'])->name('front.deleteCartItem');
Route::get('/checkout', [CartController::class, 'checkout'])->name('front.checkout');
Route::post('/process-checkout', [CartController::class, 'processCheckout'])->name('front.processCheckout');
Route::get('/thanks/{orderId}', [CartController::class, 'thanks'])->name('front.thanks');
Route::post('/get-order-summary', [CartController::class, 'getOrderSummary'])->name('front.getOrderSummary');
Route::post('/apply-discount', [CartController::class, 'discountCoupon'])->name('cart.discountCoupon');
Route::post('/remove-discount', [CartController::class, 'removeDiscount'])->name('cart.removeDiscount');
Route::post('/add-to-wishlist', [FrontController::class, 'addToWishlist'])->name('front.addToWishlist');
Route::get('/page/{slug}', [FrontController::class, 'pages'])->name('front.pages');
Route::post('/send-contact-email', [FrontController::class, 'sendContactEmail'])->name('front.sendContactEmail');
Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('front.forgotPassword');
Route::post('/process-forgot-password', [AuthController::class, 'processforgotPassword'])->name('front.processforgotPassword');
Route::get('/reset-password/{token}', [AuthController::class, 'resetPassword'])->name('front.resetPassword');
Route::post('/process-reset-password', [AuthController::class, 'processresetPassword'])->name('front.processresetPassword');
Route::post('/save-rating/{productId}', [ShopController::class, 'saveRating'])->name('front.saveRating');
Route::get('/post', [PostController::class, 'show']);
Route::post('/post-save', [PostController::class, 'save'])->name('post.save');

// Route::get('event-test', function () {

//     $pusher = new Pusher\Pusher(
//         env('PUSHER_APP_KEY'),
//         env('PUSHER_APP_SECRET'),
//         env('PUSHER_APP_ID'),
//         array('cluster' => env('PUSHER_APP_CLUSTER'))
//     );
    
//     $pusher->trigger(
//         'private-notify-channel',
//         'form-submit',
//         'Welcome'
//     );
    
//     return "Event has been sent";
//     });

// User and Auth Route
Route::group(['prefix' => 'account'], function () {

    Route::group(['middleware' => 'guest'], function () {
        Route::get('/login', [AuthController::class, 'login'])->name('account.login');
        Route::post('/login', [AuthController::class, 'authenticate'])->name('account.authenticate');
        Route::get('/register', [AuthController::class, 'register'])->name('account.register');
        Route::post('/process-register', [AuthController::class, 'registerProcess'])->name('account.registerProcess');
    });

    Route::group(['middleware' => 'auth'], function () {
        Route::get('/profile', [AuthController::class, 'profile'])->name('account.profile');
        Route::post('/update-profile', [AuthController::class, 'updateProfile'])->name('account.updateProfile');
        Route::post('/update-address', [AuthController::class, 'updateAddress'])->name('account.updateAddress');
        Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('account.showChangePassword');
        Route::post('/process-change-password', [AuthController::class, 'processChangePassword'])->name('account.processChangePassword');
        Route::get('/logout', [AuthController::class, 'logout'])->name('account.logout');
        Route::get('/orders', [AuthController::class, 'orders'])->name('account.orders');
        Route::get('/order-details/{orderId}', [AuthController::class, 'orderDetails'])->name('account.orderDetails');
        Route::get('/wishlist', [AuthController::class, 'wishlist'])->name('account.wishlist');
        Route::post('/remove-product-from-wishlist', [AuthController::class, 'removeProductFromWishlist'])->name('account.removeProductFromWishlist');
        Route::get('/user/dashboard', [AuthController::class, 'loadDashboard'])->name('account.loadDashboard');
        Route::post('/user/save-chat', [AuthController::class, 'saveChat'])->name('account.saveChat');

    });

});


Route::group(['prefix' => 'admin'], function () {

    Route::group(['middleware' => 'admin.guest'], function () {
        Route::get('/login', [adminLoginController::class, 'index'])->name('admin.login');
        Route::post('/authenticate', [adminLoginController::class, 'authenticate'])->name('admin.authenticate');
    });

    Route::group(['middleware' => 'admin.auth'], function () {
        Route::get('/dashboard', [HomeController::class, 'index'])->name('admin.dashboard');
        Route::get('/logout', [HomeController::class, 'logout'])->name('admin.logout');

        // Category
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categpries/{categories}', [CategoryController::class, 'destroy'])->name('categories.delete');

        //  Sub Category Routes
        Route::get('/sub-categories', [SubCategoryController::class, 'index'])->name('sub-categories.index');
        Route::get('/sub-categories/create', [SubCategoryController::class, 'create'])->name('sub-categories.create');
        Route::post('/sub-categories', [SubCategoryController::class, 'store'])->name('sub-categories.store');
        Route::get('/sub-categories/{subCategory}/edit', [SubCategoryController::class, 'edit'])->name('sub-categories.edit');
        Route::put('/sub-categories/{subCategory}', [SubCategoryController::class, 'update'])->name('sub-categories.update');
        Route::delete('/sub-categories/{subCategory}', [SubCategoryController::class, 'delete'])->name('sub-categories.delete');

        // Brands
        Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
        Route::get('/brands/create', [BrandController::class, 'create'])->name('brands.create');
        Route::post('/brands', [BrandController::class, 'store'])->name('brands.store');
        Route::get('/brands/{brand}/edit', [BrandController::class, 'edit'])->name('brands.edit');
        Route::put('/brands/{brand}', [BrandController::class, 'update'])->name('brands.update');
        Route::delete('/brands/{brand}', [BrandController::class, 'destroy'])->name('brands.delete');

        // Products
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit/', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}/', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'delete'])->name('products.delete');
        Route::get('/products/ratings', [ProductController::class, 'productRatings'])->name('products.productRatings');
        Route::get('/products/change-ratings-status', [ProductController::class, 'changeRatingStatus'])->name('products.changeRatingStatus');

        Route::get('/get-products', [ProductController::class, 'getProducts'])->name('products.getProducts');
        Route::get('/products/sub-categories', [ProductController::class, 'getSubCategories'])->name('products.subCategories');
        Route::post('/product-images/upload', [ProductController::class, 'updateProductImage'])->name('product-images.update');
        Route::delete('/product-images', [ProductController::class, 'deleteProductImage'])->name('product-images.delete');

        // Shipping
        Route::get('/shipping/create', [ShippingController::class, 'create'])->name('shipping.create');
        Route::post('/shipping', [ShippingController::class, 'store'])->name('shipping.store');
        Route::get('/shipping/{shipping}/edit/', [ShippingController::class, 'edit'])->name('shipping.edit');
        Route::put('/shipping/{shipping}/', [ShippingController::class, 'update'])->name('shipping.update');
        Route::delete('/shipping/{shipping}', [ShippingController::class, 'delete'])->name('shipping.delete');

        // Coupon Code Routes
        Route::get('/coupons', [DiscountController::class, 'index'])->name('coupons.index');
        Route::get('/coupons/create', [DiscountController::class, 'create'])->name('coupons.create');
        Route::post('/coupons', [DiscountController::class, 'store'])->name('coupons.store');
        Route::get('/coupons/{coupon}/edit', [DiscountController::class, 'edit'])->name('coupons.edit');
        Route::put('/coupons/{coupon}', [DiscountController::class, 'update'])->name('coupons.update');
        Route::delete('/coupons/{coupon}', [DiscountController::class, 'delete'])->name('coupons.delete');

        // Orders Routes
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/order-detail/{order}', [OrderController::class, 'detail'])->name('orders.detail');
        Route::post('/order/change-status/{order}', [OrderController::class, 'changeOrderStatus'])->name('orders.changeOrderStatus');
        Route::post('/order/send-email/{order}', [OrderController::class, 'sendInvoiceEmail'])->name('orders.sendInvoiceEmail');

        // Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.delete');

        // Pages
        Route::get('/pages', [PageController::class, 'index'])->name('pages.index');
        Route::get('/pages/create', [PageController::class, 'create'])->name('pages.create');
        Route::post('/pages', [PageController::class, 'store'])->name('pages.store');
        Route::get('/pages/{user}/edit', [PageController::class, 'edit'])->name('pages.edit');
        Route::put('/pages/{user}', [PageController::class, 'update'])->name('pages.update');
        Route::delete('/pages/{user}', [PageController::class, 'destroy'])->name('pages.delete');

        // Temp Images 
        Route::post('/upload-temp-image', [TempImagesController::class, 'create'])->name('temp-images.create');

        // Slug
        Route::get('/getSlug', function (Request $request) {
            $slug = '';
            if (!empty($request->title)) {
                $slug = Str::slug($request->title);
            }
            return response()->json([
                'success' => true,
                'slug' => $slug,
            ]);
        })->name('getSlug');
    });
});
