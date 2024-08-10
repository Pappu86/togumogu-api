<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Blog\ArticleController;
use App\Http\Controllers\Corporate\CompanyController;
use App\Http\Controllers\Corporate\CompanyCategoryController;
use App\Http\Controllers\Corporate\EmployeeGroupController;
use App\Http\Controllers\Corporate\EmployeeController;
use App\Http\Controllers\Corporate\PartnershipController;
use App\Http\Controllers\Blog\CategoryController as BlogCategoryController;
use App\Http\Controllers\Video\VideoController;
use App\Http\Controllers\Video\CategoryController as VideoCategoryController;
use App\Http\Controllers\Brand\BrandController;
use App\Http\Controllers\Brand\BrandOutletController;
use App\Http\Controllers\Brand\CategoryController as BrandCategoryController;
use App\Http\Controllers\Daycare\DaycareController;
use App\Http\Controllers\Common\ActivityController;
use App\Http\Controllers\Common\AssetController;
use App\Http\Controllers\Common\AssetCategoryController;
use App\Http\Controllers\Common\CacheManagementController;
use App\Http\Controllers\Common\FileController;
use App\Http\Controllers\Common\FilterController;
use App\Http\Controllers\Common\MenuController;
use App\Http\Controllers\Common\SettingController;
use App\Http\Controllers\Common\TagController;
use App\Http\Controllers\Community\TopicController;
use App\Http\Controllers\Daycare\DaycareFeatureController;
use App\Http\Controllers\Daycare\DaycareCategoryController;
use App\Http\Controllers\User\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Home\MainSliderController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Order\OrderStatusController;
use App\Http\Controllers\Marketing\CouponController;
use App\Http\Controllers\Payment\PaymentMethodController;
use App\Http\Controllers\Payment\PaymentStatusController;
use App\Http\Controllers\Product\CategoryController as ProductCategoryController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\ProductSliderController;
use App\Http\Controllers\Shipping\AreaController;
use App\Http\Controllers\Shipping\DistrictController;
use App\Http\Controllers\Shipping\DivisionController;
use App\Http\Controllers\Reports\SalesReportController;
use App\Http\Controllers\Shipping\ShippingCostController;
use App\Http\Controllers\Shipping\ShippingPathaoController;
use App\Http\Controllers\Shipping\ShippingProviderController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\CustomerAuthController;
use App\Http\Controllers\User\RoleController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\CustomerAddressController;
use App\Http\Controllers\Message\CustomNotificationController;
use App\Http\Controllers\Message\TemplateController;
use App\Http\Controllers\Quiz\QuizController;
use App\Http\Controllers\Quiz\QuestionController;

//Community controller
use App\Http\Controllers\Common\AgeGroupController;
use App\Http\Controllers\Common\HashtagController;
use App\Http\Controllers\Community\PostController;
use App\Http\Controllers\Community\CommentController;
use App\Http\Controllers\Community\ReportController;
use App\Http\Controllers\Community\ReportReasonController;

use App\Http\Controllers\Marketing\OfferController;
use App\Http\Controllers\Marketing\OfferRedeemController;
use App\Http\Controllers\Marketing\RewardPointController;
use App\Http\Controllers\Marketing\ServiceController;
use App\Http\Controllers\Marketing\ServiceRegistrationController;

/*
|--------------------------------------------------------------------------
| API admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "admin" middleware group. Enjoy building your API!
|
*/

// Controllers Within The "App\Http\Controllers\User" Namespace

Route::prefix('auth')->group(function () {
    // confirm email
    Route::get('confirm-email/{token}', [AuthController::class, 'confirmEmail']);

    // confirm email resent
    Route::get('send-token/{email}', [AuthController::class, 'sendConfirmationToken']);

    // login a user
    Route::post('login', [AuthController::class, 'login'])->name('login');

    // check if email or mobile exist
    Route::get('check', [AuthController::class, 'checkIsEmailMobileExist']);

    // get token
    Route::post('token', [AuthController::class, 'token']);

    // auth protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // logout auth user
        Route::post('logout', [AuthController::class, 'logout']);
        // get auth user
        Route::get('me', [AuthController::class, 'me']);
    });
});


// Controllers Within The "App\Http\Controllers\User" Namespace
Route::middleware('auth:sanctum')->group(function () {
    // user routes
    Route::post('update-user-avatar/{user}', [UserController::class, 'updateUserAvatar']);
    Route::patch('update-user-info/{user}', [UserController::class, 'updateUserInfo']);
    Route::patch('update-user-password/{user}', [UserController::class, 'updateUserPassword']);
    Route::get('user-filter', [UserController::class, 'getBySearch']);
    Route::get('user-order', [UserController::class, 'getByOrder']);
    Route::resource('user', UserController::class);

    Route::get('customer/check', [CustomerAuthController::class, 'checkIsEmailMobileExist']);

    // Update customer profile
    Route::patch('customer/update-profile/{customer}', [CustomerController::class, 'updateProfile']);
    Route::patch('customer/reset-password/{customer}', [CustomerController::class, 'resetPassword']);

    // Customer address
    Route::post('customer/address', [CustomerAddressController::class, 'store']);

    // customer routes
    Route::get('customer/statistic', [CustomerController::class, 'getStatistic']);
    Route::get('get-customers', [CustomerController::class, 'getCustomer']);
    Route::get('customer/address', [CustomerController::class, 'getAddress']);
    Route::apiResource('customer', CustomerController::class);

    // role routes
    Route::patch('add-permission/{role}', [RoleController::class, 'addPermissions']);
    Route::get('get-permission/{role}', [RoleController::class, 'getPermissionsByRole']);
    Route::get('permission', [RoleController::class, 'getPermissions']);
    Route::get('role-filter', [RoleController::class, 'getBySearch']);
    Route::get('role-order', [RoleController::class, 'getByOrder']);
    Route::get('role-all', [RoleController::class, 'getAllRoles']);
    Route::get('roles-all', [RoleController::class, 'getRoles']);
    Route::resource('role', RoleController::class);
});

// Controllers Within The "App\Http\Controllers\Auth" Namespace
//Route::namespace('Auth')->group(function () {
//    // reset password
//    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
//    Route::post('password/reset', [ResetPasswordController::class, 'reset']);
//    // verify email
//    Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
//    Route::get('email/resend/{email}', [VerificationController::class, 'resend'])->name('verification.resend');
//});

// Controllers Within The "App\Http\Controllers\Common" Namespace
Route::middleware('auth:sanctum')->group(function () {
    // setting routes
    Route::patch('settings-image/{setting}', [SettingController::class, 'updateImage']);
    Route::patch('settings/{setting}', [SettingController::class, 'update']);
    Route::get('common-settings', [SettingController::class, 'getCommonSettings']);
    Route::get('settings', [SettingController::class, 'getSettings']);
    // activity logs
    Route::get('activity-log', [ActivityController::class, 'getActivityLogs']);
    Route::delete('activity-log/{activity}', [ActivityController::class, 'destroy']);
    Route::delete('activity-log', [ActivityController::class, 'destroyAll']);
    // menu routes
    Route::post('menu-rebuild', [MenuController::class, 'rebuildTree']);
    Route::get('menus', [MenuController::class, 'getMenus']);
    Route::resource('menu', MenuController::class);
    
    // media routes
    Route::get('file-download/{file}', [FileController::class, 'downloadFile']);
    Route::apiResource('file', FileController::class);

    // Controllers Within The "App\Http\Controllers\Common" Namespace
    Route::prefix('asset')->name('asset.')->group(function () {
        // categories routes
        Route::post('category-rebuild', [AssetCategoryController::class, 'rebuildTree']);
        // delete trashed all category
        Route::delete('category-force', [AssetCategoryController::class, 'forceDelete']);
        // delete trashed single category
        Route::delete('category-force/{id}', [AssetCategoryController::class, 'forceSingleDelete']);
        // get all
        Route::get('category-all', [AssetCategoryController::class, 'getAll']);
        // get all as tree
        Route::get('category-tree', [AssetCategoryController::class, 'getAllAsTree']);
        // get all child
        Route::get('category-child', [AssetCategoryController::class, 'getAllChild']);
        Route::apiResource('category', AssetCategoryController::class);
    });

    // media assets
    Route::get('asset-download/{asset}', [AssetController::class, 'downloadAsset']);
    Route::apiResource('asset', AssetController::class);
    
    // cache management routes
    Route::get('cache-supported-commands', [CacheManagementController::class, 'getArtisanCommands']);
    Route::get('cache-run-command/{command}', [CacheManagementController::class, 'runArtisanCommand']);
        
});

Route::prefix('{locale}')->middleware('auth:sanctum')->group(function () {
    // Controllers Within The "App\Http\Controllers\Common" Namespace
    Route::prefix('common')->group(function () {
        // filter routes
        Route::get('filter-all', [FilterController::class, 'getAll']);
        Route::post('filter-rebuild', [FilterController::class, 'rebuildTree']);
        // get trashed filters
        Route::get('filter-trashed', [FilterController::class, 'getTrashed']);
        // restore trashed all filters
        Route::get('filter-trashed-restore', [FilterController::class, 'restoreTrashed']);
        // restore trashed single filter
        Route::get('filter-trashed/{id}', [FilterController::class, 'restoreSingleTrashed']);
        // delete trashed all filter
        Route::delete('filter-force', [FilterController::class, 'forceDelete']);
        // delete trashed single filter
        Route::delete('filter-force/{id}', [FilterController::class, 'forceSingleDelete']);
        Route::delete('filter-child/{filter}', [FilterController::class, 'deleteFilter']);
        Route::resource('filter', FilterController::class);

        // tag routes
        Route::get('tag-filter', [TagController::class, 'getBySearch']);
        Route::get('tag-order', [TagController::class, 'getByOrder']);
        Route::get('tag-all', [TagController::class, 'getAll']);
        // get trashed tags
        Route::get('tag-trashed', [TagController::class, 'getTrashed']);
        // restore trashed all tags
        Route::get('tag-trashed-restore', [TagController::class, 'restoreTrashed']);
        // restore trashed single tag
        Route::get('tag-trashed/{id}', [TagController::class, 'restoreSingleTrashed']);
        // delete trashed all tag
        Route::delete('tag-force', [TagController::class, 'forceDelete']);
        // delete trashed single tag
        Route::delete('tag-force/{id}', [TagController::class, 'forceSingleDelete']);
        Route::apiResource('tag', TagController::class);

        // Age groups
        Route::resource('age-group', AgeGroupController::class);

    });

    // Controllers Within The "App\Http\Controllers\Daycare" Namespace
    Route::prefix('daycare')->name('daycare.')->group(function () {
        Route::get('category-all', [DaycareCategoryController::class, 'getAll']);
        Route::post('category-rebuild', [DaycareCategoryController::class, 'rebuildTree']);
        // get trashed categories
        Route::get('category-trashed', [DaycareCategoryController::class, 'getTrashed']);
        // restore trashed all categories
        Route::get('category-trashed-restore', [DaycareCategoryController::class, 'restoreTrashed']);
        // restore trashed single category
        Route::get('category-trashed/{id}', [DaycareCategoryController::class, 'restoreSingleTrashed']);
        // delete trashed all category
        Route::delete('category-force', [DaycareCategoryController::class, 'forceDelete']);
        Route::get('category/slug/{title}', [DaycareCategoryController::class, 'checkSlug']);
        // delete trashed single category
        Route::delete('category-force/{id}', [DaycareCategoryController::class, 'forceSingleDelete']);
        Route::apiResource('category', DaycareCategoryController::class);

        // Features routes
        Route::get('feature-filter', [DaycareFeatureController::class, 'getBySearch']);
        Route::get('feature-order', [DaycareFeatureController::class, 'getByOrder']);
        Route::get('feature-all', [DaycareFeatureController::class, 'getAll']);
        // get trashed tags
        Route::get('feature-trashed', [DaycareFeatureController::class, 'getTrashed']);
        // restore trashed all feature
        Route::get('feature-trashed-restore', [DaycareFeatureController::class, 'restoreTrashed']);
        // restore trashed single feature
        Route::get('feature-trashed/{id}', [DaycareFeatureController::class, 'restoreSingleTrashed']);
        // delete trashed all feature
        Route::delete('feature-force', [DaycareFeatureController::class, 'forceDelete']);
        // delete trashed single feature
        Route::delete('feature-force/{id}', [DaycareFeatureController::class, 'forceSingleDelete']);
        Route::apiResource('feature', DaycareFeatureController::class);

        // check slug
        Route::get('daycares/slug/{title}', [DaycareController::class, 'checkSlug']);
        Route::post('daycares/bulk-create', [DaycareController::class, 'createBulk']);
        Route::resource('daycares', DaycareController::class);
    });

    // Controllers Within The "App\Http\Controllers\Blog" Namespace
    Route::prefix('blog')->group(function () {
        // category routes
        Route::get('category-filter', [BlogCategoryController::class, 'getBySearch']);
        Route::get('category-order', [BlogCategoryController::class, 'getByOrder']);
        Route::get('category-all', [BlogCategoryController::class, 'getAll']);
        // get all as tree
        Route::get('category-tree', [BlogCategoryController::class, 'getAllAsTree']);
        Route::post('category-rebuild', [BlogCategoryController::class, 'rebuildTree']);
        // get trashed categories
        Route::get('category-trashed', [BlogCategoryController::class, 'getTrashed']);
        // restore trashed all categories
        Route::get('category-trashed-restore', [BlogCategoryController::class, 'restoreTrashed']);
        // restore trashed single category
        Route::get('category-trashed/{id}', [BlogCategoryController::class, 'restoreSingleTrashed']);
        // delete trashed all category
        Route::delete('category-force', [BlogCategoryController::class, 'forceDelete']);
        // delete trashed single category
        Route::delete('category-force/{id}', [BlogCategoryController::class, 'forceSingleDelete']);
        // check slug
        Route::get('category/slug/{name}', [BlogCategoryController::class, 'checkSlug']);
        Route::apiResource('category', BlogCategoryController::class);

        // articles routes
        Route::get('article-filter', [ArticleController::class, 'getBySearch']);
        Route::get('article-order', [ArticleController::class, 'getByOrder']);
        // get trashed articles
        Route::get('article-trashed', [ArticleController::class, 'getTrashed']);
        // restore trashed all articles
        Route::get('article-trashed-restore', [ArticleController::class, 'restoreTrashed']);
        // restore trashed single article
        Route::get('article-trashed/{id}', [ArticleController::class, 'restoreSingleTrashed']);
        // delete trashed all article
        Route::delete('article-force', [ArticleController::class, 'forceDelete']);
        // delete trashed single article
        Route::delete('article-force/{id}', [ArticleController::class, 'forceSingleDelete']);
        // featured image for article
        Route::post('article/image-upload/{article}', [ArticleController::class, 'uploadImage']);
        // image for article
        Route::get('article/image-delete/{article}', [ArticleController::class, 'deleteImage']);
        // check slug
        Route::get('article/slug/{title}', [ArticleController::class, 'checkSlug']);
        Route::resource('article', ArticleController::class);
    });

    // Controllers Within The "App\Http\Controllers\Video" Namespace
    Route::prefix('video')->name('video.')->group(function () {
        // category routes
        Route::get('category-filter', [VideoCategoryController::class, 'getBySearch']);
        Route::get('category-order', [VideoCategoryController::class, 'getByOrder']);
        Route::get('category-all', [VideoCategoryController::class, 'getAll']);
        // get all as tree
        Route::get('category-tree', [VideoCategoryController::class, 'getAllAsTree']);
        Route::post('category-rebuild', [VideoCategoryController::class, 'rebuildTree']);
        // get trashed categories
        Route::get('category-trashed', [VideoCategoryController::class, 'getTrashed']);
        // restore trashed all categories
        Route::get('category-trashed-restore', [VideoCategoryController::class, 'restoreTrashed']);
        // restore trashed single category
        Route::get('category-trashed/{id}', [VideoCategoryController::class, 'restoreSingleTrashed']);
        // delete trashed all category
        Route::delete('category-force', [VideoCategoryController::class, 'forceDelete']);
        // delete trashed single category
        Route::delete('category-force/{id}', [VideoCategoryController::class, 'forceSingleDelete']);
        // check slug
        Route::get('category/slug/{name}', [VideoCategoryController::class, 'checkSlug']);
        Route::apiResource('category', VideoCategoryController::class);

        // videos routes
        Route::get('video-filter', [VideoController::class, 'getBySearch']);
        Route::get('video-order', [VideoController::class, 'getByOrder']);
        // get trashed video
        Route::get('video-trashed', [VideoController::class, 'getTrashed']);
        // restore trashed all video
        Route::get('video-trashed-restore', [VideoController::class, 'restoreTrashed']);
        // restore trashed single video
        Route::get('video-trashed/{id}', [VideoController::class, 'restoreSingleTrashed']);
        // delete trashed all video
        Route::delete('video-force', [VideoController::class, 'forceDelete']);
        // delete trashed single video
        Route::delete('video-force/{id}', [VideoController::class, 'forceSingleDelete']);
        // featured image for video
        Route::post('video/image-upload/{video}', [VideoController::class, 'uploadImage']);
        // image for video
        Route::get('video/image-delete/{video}', [VideoController::class, 'deleteImage']);
        // check slug
        Route::get('video/slug/{title}', [VideoController::class, 'checkSlug']);
        Route::resource('video', VideoController::class);
    });

    // Controllers Within The "App\Http\Controllers\Brand" Namespace
    Route::prefix('brand')->name('brand.')->group(function () {
        // category routes
        Route::get('category-filter', [BrandCategoryController::class, 'getBySearch']);
        Route::get('category-order', [BrandCategoryController::class, 'getByOrder']);
        Route::get('category-all', [BrandCategoryController::class, 'getAll']);
        // get all as tree
        Route::get('category-tree', [BrandCategoryController::class, 'getAllAsTree']);
        Route::post('category-rebuild', [BrandCategoryController::class, 'rebuildTree']);
        // get trashed categories
        Route::get('category-trashed', [BrandCategoryController::class, 'getTrashed']);
        // restore trashed all categories
        Route::get('category-trashed-restore', [BrandCategoryController::class, 'restoreTrashed']);
        // restore trashed single category
        Route::get('category-trashed/{id}', [BrandCategoryController::class, 'restoreSingleTrashed']);
        // delete trashed all category
        Route::delete('category-force', [BrandCategoryController::class, 'forceDelete']);
        // delete trashed single category
        Route::delete('category-force/{id}', [BrandCategoryController::class, 'forceSingleDelete']);
        // check slug
        Route::get('category/slug/{name}', [BrandCategoryController::class, 'checkSlug']);
        Route::apiResource('category', BrandCategoryController::class);

        // brands routes
        Route::get('brand-filter', [BrandController::class, 'getBySearch']);
        Route::get('brand-order', [BrandController::class, 'getByOrder']);
        // get trashed brand
        Route::get('brand-trashed', [BrandController::class, 'getTrashed']);
        // restore trashed all brand
        Route::get('brand-trashed-restore', [BrandController::class, 'restoreTrashed']);
        // restore trashed single brand
        Route::get('brand-trashed/{id}', [BrandController::class, 'restoreSingleTrashed']);
        // delete trashed all brand
        Route::delete('brand-force', [BrandController::class, 'forceDelete']);
        // delete trashed single brand
        Route::delete('brand-force/{id}', [BrandController::class, 'forceSingleDelete']);
        // featured image for brand
        Route::post('brand/image-upload/{brand}', [BrandController::class, 'uploadImage']);
        // image for brand
        Route::get('brand/image-delete/{brand}', [BrandController::class, 'deleteImage']);
        Route::get('all', [BrandController::class, 'getAll']);
        // check slug
        Route::get('brand/slug/{title}', [BrandController::class, 'checkSlug']);
        Route::resource('brand', BrandController::class);

        // check slug
        Route::get('brand-outlet/slug/{title}', [BrandOutletController::class, 'checkSlug']);
        Route::resource('brand-outlet', BrandOutletController::class);
    });

    // Controllers Within The "App\Http\Controllers\Marketing\Offer" Namespace
    Route::prefix('offer')->name('offer.')->group(function () {
        // check slug
        Route::get('offer/slug/{title}', [OfferController::class, 'checkSlug']);
        Route::resource('offer', OfferController::class);
    });

    // Controllers Within The "App\Http\Controllers\Marketing\Service" Namespace
    Route::prefix('service')->name('service.')->group(function () {
        // check slug
        Route::get('service/slug/{title}', [ServiceController::class, 'checkSlug']);
        Route::resource('service', ServiceController::class);
    });

    // Controllers Within The "App\Http\Controllers\Product" Namespace
    Route::prefix('product')->name('product.')->group(function () {
        // categories routes
        Route::post('category-rebuild', [ProductCategoryController::class, 'rebuildTree']);
        // get trashed categories
        Route::get('category-trashed', [ProductCategoryController::class, 'getTrashed']);
        // restore trashed all categories
        Route::get('category-trashed-restore', [ProductCategoryController::class, 'restoreTrashed']);
        // restore trashed single category
        Route::get('category-trashed/{id}', [ProductCategoryController::class, 'restoreSingleTrashed']);
        // delete trashed all category
        Route::delete('category-force', [ProductCategoryController::class, 'forceDelete']);
        // delete trashed single category
        Route::delete('category-force/{id}', [ProductCategoryController::class, 'forceSingleDelete']);
        // get all
        Route::get('category-all', [ProductCategoryController::class, 'getAll']);
        // get all as tree
        Route::get('category-tree', [ProductCategoryController::class, 'getAllAsTree']);
        // get all child
        Route::get('category-child', [ProductCategoryController::class, 'getAllChild']);
        // check slug
        Route::get('category/slug/{name}', [ProductCategoryController::class, 'checkSlug']);
        Route::apiResource('category', ProductCategoryController::class);

        // products routes
        // get trashed products
        Route::get('product-trashed', [ProductController::class, 'getTrashed']);
        // restore trashed all products
        Route::get('product-trashed-restore', [ProductController::class, 'restoreTrashed']);
        // restore trashed single product
        Route::get('product-trashed/{id}', [ProductController::class, 'restoreSingleTrashed']);
        // delete trashed all product
        Route::delete('product-force', [ProductController::class, 'forceDelete']);
        // delete trashed single product
        Route::delete('product-force/{id}', [ProductController::class, 'forceSingleDelete']);
        Route::get('get-products', [ProductController::class, 'getProducts']);
        Route::get('product-all', [ProductController::class, 'getAll']);
        // check slug
        Route::get('product/slug/{name}', [ProductController::class, 'checkSlug']);
        Route::apiResource('product', ProductController::class);

        // sliders routes
        Route::apiResource('slider', ProductSliderController::class);
    });

    // Controllers Within The "App\Http\Controllers\Order" Namespace
    Route::prefix('order')->name('order.')->group(function () {
        // status routes
        Route::get('status-all', [OrderStatusController::class, 'getAll']);
        Route::post('status-rebuild', [OrderStatusController::class, 'rebuildTree']);
        // get trashed statuses
        Route::get('status-trashed', [OrderStatusController::class, 'getTrashed']);
        // restore trashed all statuses
        Route::get('status-trashed-restore', [OrderStatusController::class, 'restoreTrashed']);
        // restore trashed single status
        Route::get('status-trashed/{id}', [OrderStatusController::class, 'restoreSingleTrashed']);
        // delete trashed all status
        Route::delete('status-force', [OrderStatusController::class, 'forceDelete']);
        // delete trashed single status
        Route::delete('status-force/{id}', [OrderStatusController::class, 'forceSingleDelete']);
        Route::apiResource('status', OrderStatusController::class);
    });

    // Controllers Within The "App\Http\Controllers\Payment" Namespace
    Route::prefix('payment')->name('payment.')->group(function () {
        // payment-method routes
        Route::get('payment-method-all', [PaymentMethodController::class, 'getAll']);
        Route::post('payment-method-rebuild', [PaymentMethodController::class, 'rebuildTree']);
        // get trashed payment-methods
        Route::get('payment-method-trashed', [PaymentMethodController::class, 'getTrashed']);
        // restore trashed all payment-methods
        Route::get('payment-method-trashed-restore', [PaymentMethodController::class, 'restoreTrashed']);
        // restore trashed single payment-method
        Route::get('payment-method-trashed/{id}', [PaymentMethodController::class, 'restoreSingleTrashed']);
        // delete trashed all payment-method
        Route::delete('payment-method-force', [PaymentMethodController::class, 'forceDelete']);
        // delete trashed single payment-method
        Route::delete('payment-method-force/{id}', [PaymentMethodController::class, 'forceSingleDelete']);
        Route::apiResource('payment-method', PaymentMethodController::class);

        // status routes
        Route::get('status-all', [PaymentStatusController::class, 'getAll']);
        Route::post('status-rebuild', [PaymentStatusController::class, 'rebuildTree']);
        // get trashed statuses
        Route::get('status-trashed', [PaymentStatusController::class, 'getTrashed']);
        // restore trashed all statuses
        Route::get('status-trashed-restore', [PaymentStatusController::class, 'restoreTrashed']);
        // restore trashed single status
        Route::get('status-trashed/{id}', [PaymentStatusController::class, 'restoreSingleTrashed']);
        // delete trashed all status
        Route::delete('status-force', [PaymentStatusController::class, 'forceDelete']);
        // delete trashed single status
        Route::delete('status-force/{id}', [PaymentStatusController::class, 'forceSingleDelete']);
        Route::apiResource('status', PaymentStatusController::class);
    });

    // Controllers Within The "App\Http\Controllers\Shipping" Namespace
    Route::prefix('shipping')->group(function () {
        // provider routes
        Route::get('provider-all', [ShippingProviderController::class, 'getAll']);
        Route::post('provider-rebuild', [ShippingProviderController::class, 'rebuildTree']);
        // get trashed providers
        Route::get('provider-trashed', [ShippingProviderController::class, 'getTrashed']);
        // restore trashed all providers
        Route::get('provider-trashed-restore', [ShippingProviderController::class, 'restoreTrashed']);
        // restore trashed single provider
        Route::get('provider-trashed/{id}', [ShippingProviderController::class, 'restoreSingleTrashed']);
        // delete trashed all provider
        Route::delete('provider-force', [ShippingProviderController::class, 'forceDelete']);
        // delete trashed single provider
        Route::delete('provider-force/{id}', [ShippingProviderController::class, 'forceSingleDelete']);
        Route::apiResource('provider', ShippingProviderController::class);
    });

    // Controllers Within The "App\Http\Controllers\Home" Namespace
    Route::prefix('home')->name('home.')->group(function () {
        // sliders routes
        Route::apiResource('slider', MainSliderController::class);
    });

    // Controllers Within The "App\Http\Controllers\Corporate" Namespace
    Route::prefix('corporate')->name('corporate.')->group(function () {
           
        Route::get('company-category-all', [CompanyCategoryController::class, 'getAll']);
        Route::post('company-category-rebuild', [CompanyCategoryController::class, 'rebuildTree']);
        // get trashed categories
        Route::get('company-category-trashed', [CompanyCategoryController::class, 'getTrashed']);
        // restore trashed all categories
        Route::get('company-category-trashed-restore', [CompanyCategoryController::class, 'restoreTrashed']);
        // restore trashed single category
        Route::get('company-category-trashed/{id}', [CompanyCategoryController::class, 'restoreSingleTrashed']);
        // delete trashed all category
        Route::delete('company-category-force', [CompanyCategoryController::class, 'forceDelete']);
        Route::get('company-category/slug/{title}', [CompanyCategoryController::class, 'checkSlug']);
        // delete trashed single category
        Route::delete('company-category-force/{id}', [CompanyCategoryController::class, 'forceSingleDelete']);
        
        // Company category api resource
        Route::apiResource('company-category', CompanyCategoryController::class);
        
        // Employee group resource
        Route::get('employee-group/slug/{title}', [EmployeeGroupController::class, 'checkSlug']);        
        Route::get('employee-group/all', [EmployeeGroupController::class, 'getEmployeeGroups']);
        Route::resource('employee-group', EmployeeGroupController::class);
    
        // Employee resource
        Route::get('single-employee/{employee}', [EmployeeController::class, 'getSingleEmployee']);
        Route::post('employee/bulk-create', [EmployeeController::class, 'createBulk']);
        Route::resource('employee', EmployeeController::class);

        // Partnership resource
        Route::get('partnership/referral/{partnership}', [PartnershipController::class, 'generateDynamicLinks']);
        Route::resource('partnership', PartnershipController::class);

        // Company resource
        Route::get('company/all', [CompanyController::class, 'getCompanies']);
        Route::resource('companies', CompanyController::class);
    });

    // Controllers Within The "App\Http\Controllers\Message" Namespace
    Route::prefix('message')->name('message.')->group(function () {

        // Notification resource
        Route::resource('notification', CustomNotificationController::class);

        // Template resource
        Route::get('template/all', [TemplateController::class, 'getTemplates']);
        Route::resource('template', TemplateController::class);
    });

    // Controllers Within The "App\Http\Controllers\Community" Namespace
    Route::prefix('community')->group(function () {

        // topic routes
        Route::get('topic-filter', [TopicController::class, 'getBySearch']);
        Route::get('topic-order', [TopicController::class, 'getByOrder']);
        Route::get('topic-all', [TopicController::class, 'getAll']);
        // get all as tree
        Route::get('topic-tree', [TopicController::class, 'getAllAsTree']);
        Route::post('topic-rebuild', [TopicController::class, 'rebuildTree']);
        // get trashed categories
        Route::get('topic-trashed', [TopicController::class, 'getTrashed']);
        // restore trashed all categories
        Route::get('topic-trashed-restore', [TopicController::class, 'restoreTrashed']);
        // restore trashed single category
        Route::get('topic-trashed/{id}', [TopicController::class, 'restoreSingleTrashed']);
        // delete trashed all category
        Route::delete('topic-force', [TopicController::class, 'forceDelete']);
        // delete trashed single category
        Route::delete('topic-force/{id}', [TopicController::class, 'forceSingleDelete']);
        // check slug
        Route::get('topic/slug/{name}', [TopicController::class, 'checkSlug']);
        Route::apiResource('topic', TopicController::class);

        // Report reasons 
        // check slug
        Route::get('report-reason/slug/{name}', [ReportReasonController::class, 'checkSlug']);
        Route::apiResource('report-reason', ReportReasonController::class);

    });

    // Controllers Within The "App\Http\Controllers\Quiz" Namespace
    Route::prefix('quiz')->group(function () {
        Route::get('all', [QuizController::class, 'getActiveQuizs']);
        // check slug
        Route::get('quiz/slug/{title}', [QuizController::class, 'checkSlug']);
        Route::resource('quiz', QuizController::class);

        // check slug
        Route::get('question/slug/{title}', [QuestionController::class, 'checkSlug']);
        Route::resource('question', QuestionController::class);
    });

});

// Controllers Within The "App\Http\Controllers\Community" Namespace
Route::prefix('community')->middleware('auth:sanctum')->group(function () {
    Route::resource('comment', CommentController::class);
    Route::resource('post', PostController::class);
    Route::resource('report', ReportController::class);
});

// Controllers Within The "App\Http\Controllers\Common" Namespace
Route::prefix('common')->middleware('auth:sanctum')->group(function () {
    Route::resource('hashtag', HashtagController::class);
});

// Controllers Within The "App\Http\Controllers\Order" Namespace
Route::prefix('order')->middleware('auth:sanctum')->name('order.')->group(function () {
    // order routes
    Route::get('order-all', [OrderController::class, 'getAll']);
    Route::post('order-rebuild', [OrderController::class, 'rebuildTree']);
    // get trashed orders
    Route::get('order-trashed', [OrderController::class, 'getTrashed']);
    // restore trashed all orders
    Route::get('order-trashed-restore', [OrderController::class, 'restoreTrashed']);
    // restore trashed single order
    Route::get('order-trashed/{id}', [OrderController::class, 'restoreSingleTrashed']);
    // delete trashed all order
    Route::delete('order-force', [OrderController::class, 'forceDelete']);
    // delete trashed single order
    Route::delete('order-force/{id}', [OrderController::class, 'forceSingleDelete']);
    // update status
    Route::patch('order-status/{order}', [OrderController::class, 'updateStatus']);
    // update process
    Route::patch('order-process/{order}', [OrderController::class, 'updateProcess']);

    // place order
    Route::post('store', [OrderController::class, 'store']);

    Route::apiResource('order', OrderController::class);
});

// Controllers Within The "App\Http\Controllers\Shipping" Namespace
Route::prefix('shipping')->group(function () {
    // division routes
    Route::get('division-all', [DivisionController::class, 'getDivisions']);
    Route::get('division-tree', [DivisionController::class, 'getTreeView']);
    Route::get('district-by-division/{division}', [DistrictController::class, 'getDistricts']);
    Route::get('division-id-by-division-name/{division}', [DivisionController::class, 'getDivisionByName']);
    Route::get('district-all', [DistrictController::class, 'getAllDistricts']);
    Route::get('area-by-district/{district}', [AreaController::class, 'getAreas']);
    Route::get('district-id-by-district-name/{district}', [DistrictController::class, 'getDistrictByName']);
    Route::get('area-all', [AreaController::class, 'getAllAreas']);

    // division routes
    Route::apiResource('division', DivisionController::class);

    // district routes
    Route::apiResource('district', DistrictController::class);

    // area routes
    Route::apiResource('area', AreaController::class);

    // shipping cost routes
    Route::get('cost/{area_id}', [ShippingCostController::class, 'getShippingCost']);
    Route::post('cost-bulk', [ShippingCostController::class, 'insertOrUpdateBulk']);
    Route::apiResource('cost', ShippingCostController::class);

    // Pathao service
    Route::prefix('pathao')->group(function () {
        // generate access token
        Route::get('token', [ShippingPathaoController::class, 'getAccessToken']);
        // generate refresh token
        Route::get('refresh-token', [ShippingPathaoController::class, 'refreshAccessToken']);
        // get cities
        Route::get('cities', [ShippingPathaoController::class, 'getAvailableCities']);
        // get zones
        Route::get('zones/{city_id}', [ShippingPathaoController::class, 'getAvailableZones']);
        // get areas
        Route::get('areas/{zone_id}', [ShippingPathaoController::class, 'getAvailableAreas']);
        // create store
        Route::post('store', [ShippingPathaoController::class, 'createStore']);
        // get stores
        Route::get('store', [ShippingPathaoController::class, 'getStores']);
        // place order
        Route::post('order', [ShippingPathaoController::class, 'createOrder']);
    });
});

// Controllers Within The "App\Http\Controllers\Marketing" Namespace
Route::prefix('marketing')->group(function () {
    // coupon routes
    Route::get('coupon/all', [CouponController::class, 'getCoupons']);
    Route::get('coupon', [CouponController::class, 'checkCoupon']);
    Route::apiResource('coupons', CouponController::class);
    
    //Reward points
    Route::patch('reward-points/{rewardSetting}', [RewardPointController::class, 'update']);
    Route::apiResource('reward-points', RewardPointController::class);
    
    //offer redeems
    Route::apiResource('offer-redeems', OfferRedeemController::class);

    //Service Registration
    Route::patch('service-registration-process/{serviceRegistration}', [ServiceRegistrationController::class, 'updateProcess']);
    Route::apiResource('service-registration', ServiceRegistrationController::class);
});

// Controllers Within The "App\Http\Controllers\Sales" Namespace
Route::prefix('reports')->group(function () {
    // Sales report routes
    Route::get('best-selling', [SalesReportController::class, 'bestSelling']);
    Route::apiResource('sales', SalesReportController::class);
});

Route::get('common-settings', [HomeController::class, 'getCommonSettings']);

Route::prefix('update-script')->group(function () {
    Route::post('product/bulk-update', [ProductController::class, 'updateScript']);
});

Route::prefix('dynamic')->group(function () {
    Route::get('product', [ProductController::class, 'generateDynamicLinks']);
    Route::get('article', [ArticleController::class, 'generateDynamicLinks']);
    Route::get('daycare', [DaycareController::class, 'generateDynamicLinks']);
});
