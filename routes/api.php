<?php

use App\Http\Controllers\Blog\ArticleApiController;
use App\Http\Controllers\Blog\ArticleAppController;
use App\Http\Controllers\Blog\CategoryApiController as BlogCategoryApiController;
use App\Http\Controllers\Video\CategoryApiController as VideoCategoryApiController;
use App\Http\Controllers\Video\VideoApiController;
use App\Http\Controllers\Video\VideoAppController;
use App\Http\Controllers\Common\FilterApiController;
use App\Http\Controllers\Common\TagApiController;
use App\Http\Controllers\Daycare\DaycareApiController;
use App\Http\Controllers\Daycare\DaycareAppController;
use App\Http\Controllers\Daycare\CategoryApiController as DaycareCategoryApiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Marketing\CouponApiController;
use App\Http\Controllers\Marketing\CouponAppController;
use App\Http\Controllers\Child\HospitalAppController;
use App\Http\Controllers\Child\DoctorAppController;
use App\Http\Controllers\Child\SchoolAppController;
use App\Http\Controllers\Child\ChildClassAppController;
use App\Http\Controllers\Order\CartApiController;
use App\Http\Controllers\Order\CartAppController;
use App\Http\Controllers\Order\OrderApiController;
use App\Http\Controllers\Order\OrderAppController;
use App\Http\Controllers\Order\WishListApiController;
use App\Http\Controllers\Order\WishListAppController;
use App\Http\Controllers\Payment\BKashPaymentController;
use App\Http\Controllers\Payment\PaymentApiController;
use App\Http\Controllers\Product\CategoryApiController as ProductCategoryApiController;
use App\Http\Controllers\Product\ProductApiController;
use App\Http\Controllers\Product\ProductAppController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Shipping\ShippingApiController;
use App\Http\Controllers\User\CustomerAddressController;
use App\Http\Controllers\User\CustomerAddressAppController;
use App\Http\Controllers\User\CustomerApiController;
use App\Http\Controllers\User\CustomerAppController;
use App\Http\Controllers\User\CustomerAuthController;
use App\Http\Controllers\Child\ChildAppController;
use App\Http\Controllers\Common\AgeGroupController;
use App\Http\Controllers\Common\HashtagAppController;
use App\Http\Controllers\Community\PostAppController;
use App\Http\Controllers\Common\SettingController;
use App\Http\Controllers\Community\CommentAppController;
use App\Http\Controllers\Community\ReportController;
use App\Http\Controllers\Community\ReportReasonAppController;
use App\Http\Controllers\Community\TopicAppController;
use App\Http\Controllers\Community\VoteAppController;
use App\Http\Controllers\Corporate\CompanyAppController;
use App\Http\Controllers\Home\MainSliderController;
use App\Http\Controllers\Marketing\BookFairApiController;
use App\Http\Controllers\Marketing\OfferAppController;
use App\Http\Controllers\Marketing\ServiceAppController;
use App\Http\Controllers\Brand\BrandAppController;
use App\Http\Controllers\Quiz\QuizAppController;
use App\Http\Controllers\Quiz\QuizApiController;
use App\Http\Controllers\Marketing\ServiceRegistrationAppController;
use App\Http\Controllers\Marketing\OfferRedeemAppController;
use App\Http\Controllers\Marketing\RewardPointController;
use App\Http\Controllers\User\CustomerNotificationController;
use App\Http\Controllers\Payment\BkashCheckoutController;
use App\Http\Controllers\Menstrual\MenstrualApiController;
use App\Http\Controllers\Menstrual\MenstrualAppController;
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

Route::prefix('auth')->group(function () {
    // Route::post('register', [CustomerAuthController::class, 'register']);

    // Customer registration 
    // send verification code
    Route::get('email-mobile-exist', [CustomerAuthController::class, 'checkIsEmailMobileExist']);
    Route::post('verification-code', [CustomerAuthController::class, 'sendMessageCode']);
    Route::post('valid-code', [CustomerAuthController::class, 'checkMessageCode']);
    Route::post('social', [CustomerAuthController::class, 'socialLogin']);

    // Customer login
    Route::post('login', [CustomerAuthController::class, 'login']);

    //Reset password of customer auth
    Route::post('reset-password', [CustomerAuthController::class, 'resetPassword']);

    // forgot password
    Route::post('forgot-password', [CustomerAuthController::class, 'forgotPassword']);
    Route::post('reset-password-by-email', [CustomerAuthController::class, 'resetPasswordByEmail']);

    // verify email
    Route::middleware('auth:customer')->group(function () {
        Route::get('me', [CustomerAuthController::class, 'me']);
        Route::patch('update-password', [CustomerAuthController::class, 'updatePassword']);
        Route::post('logout', [CustomerAuthController::class, 'logout']);
    });
});

Route::prefix('customer')->middleware('auth:customer')->group(function () {
    // update profile
    Route::patch('update-profile/{customer}', [CustomerApiController::class, 'updateProfile']);
    //Route::patch('update-password', [CustomerApiController::class, 'updatePassword']);
    Route::post('update-customer-avatar/{customer}', [CustomerApiController::class, 'updateAvatar']);
    
    // customer address
    Route::get('get-default-address', [CustomerAddressController::class, 'getDefaultAddress']);
    Route::get('address', [CustomerAddressController::class, 'index']);
    Route::post('address', [CustomerAddressController::class, 'store']);
    Route::get('address/{address}', [CustomerAddressController::class, 'edit']);
    Route::patch('address/{address}', [CustomerAddressController::class, 'update']);
    Route::delete('address/{address}', [CustomerAddressController::class, 'delete']);
    // customer order
    Route::prefix('order')->group(function () {
        Route::get('/', [CustomerApiController::class, 'getOrderList']);
        Route::get('/{order}', [CustomerApiController::class, 'getOrder']);
    });
    // wish list
    Route::get('wishlist', [WishListApiController::class, 'index']);
    Route::post('wishlist', [WishListApiController::class, 'store']);
    Route::post('wishlist-delete', [WishListApiController::class, 'destroy']);

    // get parent questions
    Route::patch('parent-question/{customer}', [CustomerApiController::class, 'storeParentQuestion']);

    //Menstrual calendar
    Route::prefix('menstrual')->group(function () {
        Route::post('/create', [MenstrualApiController::class, 'store']);
        Route::get('/info', [MenstrualApiController::class, 'getSingleMenstrualInfo']);
    });

    Route::prefix('{locale}')->group(function () {
        Route::prefix('quiz')->group(function () {
            Route::get('tracker', [QuizApiController::class, 'getTrackerQuiz']);   
            Route::get('/check-participate', [QuizApiController::class, 'checkParticipateQuiz']);
            Route::get('/participated', [QuizApiController::class, 'getParticipatedAllQuiz']);          
        });
    });    
});

Route::prefix('{locale}')->group(function () {
    // search
    Route::get('search', [SearchController::class, 'search']);
    // Controllers Within The "App\Http\Controllers\Common" Namespace
    Route::prefix('common')->group(function () {
        Route::get('tag-articles', [TagApiController::class, 'getAllWithArticles']);
        Route::get('tag-articles/{article_id}', [TagApiController::class, 'getTagsByArticle']);
        // filters
        Route::get('filters', [FilterApiController::class, 'getAllFilters']);
    });
    // Controllers Within The "App\Http\Controllers\Blog" Namespace
    Route::prefix('blog')->group(function () {
        Route::get('categories', [BlogCategoryApiController::class, 'getAll']);
        Route::get('categories/{category}', [BlogCategoryApiController::class, 'getSingleCategory']);
        Route::get('articles', [ArticleApiController::class, 'getArticles']);
        Route::get('related/articles/{article_id}', [ArticleApiController::class, 'getRelatedArticles']);
        Route::get('related/products/{article_id}', [ArticleApiController::class, 'getRelatedProducts']);
        Route::get('articles/{slug}', [ArticleApiController::class, 'getSingleArticle']);
        Route::get('article-update-view-count/{article_id}', [ArticleApiController::class, 'updateViewCount']);
    });
    // Controllers Within The "App\Http\Controllers\Daycare" Namespace
    Route::prefix('daycare')->group(function () {
        Route::get('/', [DaycareApiController::class, 'getDayCares']);
        Route::get('category', [DaycareApiController::class, 'getDayCareCategories']);
        Route::get('{daycare}', [DaycareApiController::class, 'getDayCare']);
    });
    // Controllers Within The "App\Http\Controllers\Product" Namespace
    Route::prefix('product')->group(function () {
        Route::get('categories', [ProductCategoryApiController::class, 'getAll']);
        Route::get('categories/{category}', [ProductCategoryApiController::class, 'getSingleCategory']);
        Route::get('category-ancestor-descendant/{category}', [ProductCategoryApiController::class, 'getAncestorsDescendants']);
        Route::get('products', [ProductApiController::class, 'getProducts']);
        Route::get('products/{product}', [ProductApiController::class, 'getProductBySlug']);
        Route::get('recommend', [ProductApiController::class, 'recommendProductsForCustomer']);

        Route::get('related/articles/{product_id}', [ProductApiController::class, 'getRelatedArticles']);
        Route::get('related/products/{product_id}', [ProductApiController::class, 'getRelatedProducts']);

    });

    // Controllers Within The "App\Http\Controllers\Payment" Namespace
    Route::prefix('payment')->group(function () {
        // payment method
        Route::get('payment-method', [PaymentApiController::class, 'getPaymentMethods']);
    });

    // Controllers Within The "App\Http\Controllers\Shipping" Namespace
    Route::prefix('shipping')->group(function () {
        // division routes
        Route::get('division-all', [ShippingApiController::class, 'getDivisions']);
        Route::get('district-by-division/{division}', [ShippingApiController::class, 'getDistricts']);
        Route::get('area-by-district/{district}', [ShippingApiController::class, 'getAreas']);
    });

    Route::prefix('home')->group(function () {
        // sliders routes
        Route::get('sliders', [MainSliderController::class, 'getAllSliders']);
    });

    //Quiz 
    Route::prefix('quiz')->group(function () {
        Route::get('/review', [QuizApiController::class, 'getReview']);
        Route::get('/all', [QuizApiController::class, 'getAll']);
        Route::get('/{slug}', [QuizApiController::class, 'getSingle']);
        Route::post('/auth', [QuizApiController::class, 'createAnswererAccount']);
        Route::post('/answers', [QuizApiController::class, 'submitQuestionAnswer']);
        Route::get('/auth/check', [QuizApiController::class, 'checkAnswerer']);     
    });
});

// Controllers Within The "App\Http\Controllers\Order" Namespace
Route::prefix('order')->name('order.')->group(function () {
    Route::middleware('auth:customer')->group(function () {
        // cart
        Route::get('cart', [CartApiController::class, 'getCart']);
        Route::post('cart', [CartApiController::class, 'updateOrInsert']);
        Route::delete('cart', [CartApiController::class, 'removeFromCart']);
        // place order
        Route::post('store', [OrderApiController::class, 'store']);
    });
});

// bkash
Route::prefix('bkash')->group(function () {    
    Route::post('create', [BkashCheckoutController::class, 'create'])->name('url-create');
    Route::get('callback', [BkashCheckoutController::class, 'callback'])->name('url-callback');
});

// TODO: It will be removed after bkash integation successfully done

// Controllers Within The "App\Http\Controllers\Payment" Namespace
// Route::prefix('payment')->group(function () {
//     // bkash
//     Route::prefix('bkash')->group(function () {
//         Route::get('token', [BKashPaymentController::class, 'getToken']);
//         Route::get('refresh-token', [BKashPaymentController::class, 'getRefreshToken']);
//         Route::post('create', [BKashPaymentController::class, 'createPayment']);
//         Route::post('execute', [BKashPaymentController::class, 'executePayment']);
//     });
// });

// Controllers Within The "App\Http\Controllers\Marketing" Namespace
Route::prefix('marketing')->group(function () {
    // coupon
    Route::get('coupon', [CouponApiController::class, 'checkCoupon']);
    //Book fair
    Route::post('book-fair', [BookFairApiController::class, 'store']);
});

Route::prefix('shipping')->group(function () {
    // get shipping cost
    Route::get('cost', [ShippingApiController::class, 'getShippingCost']);
});

// contact
Route::post('contact', [HomeController::class, 'contact']);
Route::get('contact-us', [SettingController::class, 'getContactUsSettings']);

// ===== Start App apis =====
Route::prefix('v1')->group(function () {
    // Auth
    Route::prefix('auth')->group(function () {
        // Route::post('register', [CustomerAppController::class, 'register']);
        
        // Customer registration
        // send verification code
        Route::post('verification-code', [CustomerAuthController::class, 'sendMessageCode']);
        Route::post('valid-code', [CustomerAuthController::class, 'checkMessageCode']);
        Route::post('social', [CustomerAppController::class, 'socialLogin']);
        Route::post('social/check-exist-provider', [CustomerAppController::class, 'checkExistSocialProvider']);

        // Customer login
        Route::post('login', [CustomerAppController::class, 'login']);
   
        //Reset password of customer auth
        Route::post('reset-password', [CustomerAuthController::class, 'resetPassword']);

        // forgot password
        Route::post('forgot-password', [CustomerAppController::class, 'forgotPassword']);
        Route::post('reset-password-by-email', [CustomerAppController::class, 'resetPasswordByEmail']);

        Route::middleware('auth:sanctum')->group(function () {
        
            Route::get('me', [CustomerAuthController::class, 'me']);
            Route::post('change-password', [CustomerAppController::class, 'changePassword']);
            Route::post('logout', [CustomerAppController::class, 'logout']);
            Route::get('generate-referral', [CustomerAppController::class, 'generateDynamicLinks']);

            //Broadcasting
            Route::post('broadcasting', [CustomerAuthController::class, 'broadcastLogin']);
        });
    });

    // Customers
    Route::prefix('customer')->middleware('auth:sanctum')->group(function () {
        // update profile
        Route::patch('update-profile', [CustomerAppController::class, 'updateProfile']);
        Route::post('update-customer-avatar', [CustomerAppController::class, 'updateAvatar']);
        Route::patch('update-password', [CustomerAppController::class, 'updatePassword']);

        //Notifications
        Route::get('notifications', [CustomerNotificationController::class, 'getNotifications']);
        Route::get('notifications/unread', [CustomerNotificationController::class, 'getUnreadNotifications']);
        Route::patch('notification/read-all', [CustomerNotificationController::class, 'markAsReadAllNotification']);
        Route::patch('notification/{notification}', [CustomerNotificationController::class, 'markAsReadNotification']);
        Route::patch('notification/unread/{notification}', [CustomerNotificationController::class, 'markAsUnreadNotification']);
        Route::delete('notification/{notification}', [CustomerNotificationController::class, 'deleteNotification']);
        Route::delete('notifications', [CustomerNotificationController::class, 'deleteNotifications']);

        // customer address
        Route::get('get-default-address', [CustomerAddressAppController::class, 'getDefaultAddress']);
        Route::get('address', [CustomerAddressAppController::class, 'index']);
        Route::post('address', [CustomerAddressAppController::class, 'store']);
        Route::get('address/{address}', [CustomerAddressAppController::class, 'edit']);
        Route::patch('address/{address}', [CustomerAddressAppController::class, 'update']);
        Route::delete('address/{address}', [CustomerAddressAppController::class, 'delete']);
        Route::get('companies', [CompanyAppController::class, 'getCompanies']);
        Route::get('devices', [CustomerAppController::class, 'getDevices']);
        Route::post('device', [CustomerAppController::class, 'createDevice']);

         //Customer settings
         Route::patch('setting', [CustomerAppController::class, 'updateSetting']);

        // get parent questions
        Route::patch('parent-question', [CustomerAppController::class, 'storeParentQuestion']);

        // customer children
        Route::prefix('children')->group(function () {
            Route::get('/{child}', [ChildAppController::class, 'getSingleChild']);
            Route::post('store', [ChildAppController::class, 'store']);
            Route::patch('update-profile/{child}', [ChildAppController::class, 'updateProfile']);
            Route::post('update-child-avatar/{child}', [ChildAppController::class, 'updateAvatar']);
            Route::patch('remove-doctor/{child}', [ChildAppController::class, 'removeDoctor']);
            // Delete single child
            Route::delete('/{child}', [ChildAppController::class, 'singleDelete']);
        });

        // customer order
        Route::prefix('order')->group(function () {
            // cart
            Route::get('cart', [CartAppController::class, 'getCart']);
            Route::post('cart', [CartAppController::class, 'updateOrInsert']);
            Route::delete('cart', [CartAppController::class, 'removeFromCart']);
            // place order
            Route::post('store', [OrderAppController::class, 'store']);
            Route::get('/', [CustomerAppController::class, 'getOrderList']);
            Route::get('/{order}', [CustomerAppController::class, 'getOrder']);
        });

        // wish list
        Route::get('wishlist', [WishListAppController::class, 'index']);
        Route::post('wishlist', [WishListAppController::class, 'store']);
        Route::post('wishlist-delete', [WishListAppController::class, 'destroy']);

        // customer subscription
        Route::prefix('subscription')->group(function () {
            Route::post('/store', [CustomerAppController::class, 'storeSubscription']);
        });

        //Community route
        Route::prefix('post')->group(function () {
            Route::post('/{post}/report', [PostAppController::class, 'reportStore']);
            Route::get('/favorite', [PostAppController::class, 'getAllFevorite']);
            Route::post('/{post}/favorite', [PostAppController::class, 'fevorite']);
            Route::post('/create', [PostAppController::class, 'store']);
            Route::post('/{post}/update-images', [PostAppController::class, 'updateImages']);
            Route::patch('/{post}', [PostAppController::class, 'update']);
            Route::delete('/{post}', [PostAppController::class, 'destroy']);
            Route::get('/{post}', [PostAppController::class, 'show']);
            Route::post('/{post}/like', [PostAppController::class, 'like']);
            Route::post('/{post}/dislike', [PostAppController::class, 'dislike']);
            Route::get('/', [PostAppController::class, 'getPosts']);
      
            Route::prefix('/{post}/comment')->group(function () {
                Route::post('/create', [CommentAppController::class, 'store']);
                Route::post('{comment}/reply', [CommentAppController::class, 'reply']);
                Route::get('/', [CommentAppController::class, 'getComments']);
            });

            Route::prefix('comment')->group(function () {
                Route::patch('/{comment}', [CommentAppController::class, 'update']);
                Route::delete('/{comment}', [CommentAppController::class, 'destroy']);
                Route::post('/{comment}/like', [CommentAppController::class, 'like']);
                Route::post('/{comment}/dislike', [CommentAppController::class, 'dislike']);
                Route::get('/{comment}/reply', [CommentAppController::class, 'getReplies']);
            });
        });

        //Report/claim of post, comment or others
        Route::post('/report', [ReportController::class, 'store']);

        Route::prefix('hashtag')->group(function () {
            Route::get('/', [HashtagAppController::class, 'index']);
        });

        Route::prefix('blog')->group(function () {
            Route::post('read/{article}', [ArticleAppController::class, 'readArticle']);
        });

        Route::prefix('video')->group(function () {
            Route::post('watch/{video}', [VideoAppController::class, 'watchVideo']);
        });

        // Confirm offer 
        Route::prefix('offer')->group(function () {
            Route::post('confirm', [OfferRedeemAppController::class, 'store']);
            Route::get('{locale}/', [OfferAppController::class, 'getAll']);
            Route::get('{locale}/{offer}', [OfferAppController::class, 'getSingle']);
        });

        // Controllers Within The "App\Http\Controllers\Marketing\Service" Namespace
        Route::prefix('service')->group(function () {
            Route::get('{locale}/tracker', [ServiceAppController::class, 'getTrackerService']);
            Route::post('confirm', [ServiceRegistrationAppController::class, 'store']);
            Route::post('update-registration/{serviceRegistration}', [ServiceRegistrationAppController::class, 'update']);
            Route::get('{locale}/', [ServiceAppController::class, 'getAll']);
            Route::get('{locale}/{service}', [ServiceAppController::class, 'getSingle']);
        });

        //Brand 
        Route::prefix('brand')->group(function () {
            Route::get('{locale}', [BrandAppController::class, 'getAll']);
            Route::get('{locale}/{brand}', [BrandAppController::class, 'getSingle']);
        });

        //Menstrual calendar
        Route::prefix('menstrual')->group(function () {
            Route::post('/create', [MenstrualAppController::class, 'store']);
            Route::get('/info', [MenstrualAppController::class, 'getSingleMenstrualInfo']);
        });

        //Quiz 
        Route::prefix('{locale}')->group(function () {
            Route::prefix('quiz')->group(function () { 
                Route::get('participated', [QuizAppController::class, 'getParticipatedAllQuiz']);
                Route::get('check-participate', [QuizAppController::class, 'checkParticipateQuiz']); 
                Route::get('review', [QuizAppController::class, 'getReview']);
                Route::get('tracker', [QuizAppController::class, 'getTrackerQuiz']);   
                Route::get('auth/check', [QuizAppController::class, 'checkAnswerer']);
                Route::get('all', [QuizAppController::class, 'getAll']);
                Route::get('/{slug}', [QuizAppController::class, 'getSingle']);
                Route::post('auth', [QuizAppController::class, 'createAnswererAccount']);
                Route::post('answers', [QuizAppController::class, 'submitQuestionAnswer']);     
            });

            Route::prefix('service')->group(function () {
                Route::get('tracker', [ServiceAppController::class, 'getTrackerService']);
            });
        });
    });

    // Controllers Within The "App\Http\Controllers\Marketing" Namespace
    Route::prefix('marketing')->group(function () {
        // coupon
        Route::get('coupon', [CouponAppController::class, 'checkCoupon']);
        Route::get('reward-setting', [RewardPointController::class, 'getSetting']);
        Route::get('reward-settings', [RewardPointController::class, 'getAllSettings']);
    
    });

    Route::prefix('shipping')->group(function () {
        // get shipping cost
        Route::get('cost', [ShippingApiController::class, 'getShippingCost']);
    });

    Route::prefix('{locale}')->group(function () {

        // Controllers Within The "App\Http\Controllers\Blog" Namespace
        Route::prefix('blog')->group(function () {
            Route::get('categories', [BlogCategoryApiController::class, 'getAll']);
            Route::get('categories/{category}', [BlogCategoryApiController::class, 'getSingleCategory']);
            Route::get('articles', [ArticleAppController::class, 'getArticles']);
            Route::get('related/articles/{article_id}', [ArticleApiController::class, 'getRelatedArticles']);
            Route::get('related/products/{article_id}', [ArticleApiController::class, 'getRelatedProducts']);
            Route::get('articles/{slug}', [ArticleAppController::class, 'getSingleArticle']);
            Route::get('article-update-view-count/{article_id}', [ArticleAppController::class, 'updateViewCount']);
            Route::get('article/tracker', [ArticleAppController::class, 'getTrackerArticle']);
        });

         // Controllers Within The "App\Http\Controllers\Video" Namespace
         Route::prefix('video')->group(function () {
            Route::get('categories', [VideoCategoryApiController::class, 'getAll']);
            Route::get('categories/{category}', [VideoCategoryApiController::class, 'getSingleCategory']);
            Route::get('videos', [VideoAppController::class, 'getVideos']);
            Route::get('recommend', [VideoAppController::class, 'recommendVideosForCustomer']);
            Route::get('related/videos/{video_id}', [VideoAppController::class, 'getRelatedVideos']);
            Route::get('related/articles/{video_id}', [VideoAppController::class, 'getRelatedArticles']);
            Route::get('related/products/{video_id}', [VideoAppController::class, 'getRelatedProducts']);
            Route::get('videos/{slug}', [VideoAppController::class, 'getSingleVideo']);
            Route::get('tracker', [VideoAppController::class, 'getTrackerVideo']);
        });

        // Controllers Within The "App\Http\Controllers\Daycare" Namespace
        Route::prefix('daycare')->group(function () {
            Route::get('/', [DaycareAppController::class, 'getDayCares']);
            Route::get('categories', [DaycareCategoryApiController::class, 'getAll']);
            Route::get('categories/{category}', [DaycareCategoryApiController::class, 'getSingleCategory']);
            Route::get('category', [DaycareAppController::class, 'getDayCareCategories']);
            Route::get('{daycare}', [DaycareAppController::class, 'getDayCare']);
        });

        // Controllers Within The "App\Http\Controllers\Product" Namespace
        Route::prefix('product')->group(function () {
            Route::get('recommend', [ProductApiController::class, 'recommendProductsForCustomer']);
            Route::get('categories', [ProductCategoryApiController::class, 'getAll']);
            Route::get('categories/{category}', [ProductCategoryApiController::class, 'getSingleCategory']);
            Route::get('category-ancestor-descendant/{category}', [ProductCategoryApiController::class, 'getAncestorsDescendants']);
            Route::get('products', [ProductAppController::class, 'getProducts']);
            Route::get('products/{product}', [ProductAppController::class, 'getProductBySlug']);
            Route::get('tracker', [ProductAppController::class, 'getTrackerProduct']);
       
            Route::get('related/articles/{product_id}', [ProductAppController::class, 'getRelatedArticles']);
            Route::get('related/products/{product_id}', [ProductAppController::class, 'getRelatedProducts']);
        });

        // search
        Route::get('search', [SearchController::class, 'search']);

        // Controllers Within The "App\Http\Controllers\Payment" Namespace
        Route::prefix('payment')->group(function () {
            // payment method
            Route::get('payment-method', [PaymentApiController::class, 'getPaymentMethods']);
        });

        // Controllers Within The "App\Http\Controllers\Shipping" Namespace
        Route::prefix('shipping')->group(function () {
            // division routes
            Route::get('division-all', [ShippingApiController::class, 'getDivisions']);
            Route::get('district-by-division/{division}', [ShippingApiController::class, 'getDistricts']);
            Route::get('area-by-district/{district}', [ShippingApiController::class, 'getAreas']);
        });

        Route::prefix('home')->group(function () {
            // sliders routes
            Route::get('sliders', [MainSliderController::class, 'getAllSliders']);
        });

        // Hospital
        Route::prefix('hospital')->group(function () {
            Route::get('/', [HospitalAppController::class, 'getHospitals']);
        });

        // Hospital
        Route::prefix('doctor')->group(function () {
            Route::post('/store', [DoctorAppController::class, 'store']);
            Route::get('/', [DoctorAppController::class, 'getDoctors']);
        });

        // School
        Route::prefix('school')->group(function () {
            Route::get('/', [SchoolAppController::class, 'getSchools']);
        });

        // Class
        Route::prefix('child-class')->group(function () {
            Route::get('/', [ChildClassAppController::class, 'getClasses']);
        });

        // communnity topics
        Route::prefix('topic')->group(function () {
            Route::get('/', [TopicAppController::class, 'getAll']);
        });

        Route::prefix('report-reason')->group(function () {
            Route::get('/', [ReportReasonAppController::class, 'getAll']);
        });
        
        Route::prefix('age-group')->group(function () {
            Route::get('/', [AgeGroupController::class, 'getAllAppAgeGroups']);
        });
    });

    // Controllers Within The "App\Http\Controllers\Settings" Namespace
    Route::get('contact-us', [SettingController::class, 'getContactUsSettings']);
    Route::get('offer-settings', [SettingController::class, 'getOfferSettings']);

});
