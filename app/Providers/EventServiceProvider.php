<?php

namespace App\Providers;

use App\Models\Blog\Article;
use App\Models\Community\Report as CommunityReport;
use App\Models\Daycare\Daycare;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Models\Order\Order;
use App\Models\Product\Product;
use App\Observers\OrderObserver;
use App\Models\Reward\RewardTransaction;
use App\Models\Marketing\ServiceRegistration;
use App\Models\Quiz\QuizResult;
use App\Observers\ArticleObserver;
use App\Observers\DaycareObserver;
use App\Observers\ProductObserver;
use App\Observers\ServiceRegistrationObserver;
use App\Observers\QuizResultObserver;
use App\Observers\RewardTransactionObserver;
use App\Observers\CommunityReportObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //Order observer
        Order::observe(OrderObserver::class);
        
        //Reward Transaction observer
        RewardTransaction::observe(RewardTransactionObserver::class);

        //Products observer
        Product::observe(ProductObserver::class);

        //Daycare observer
        Daycare::observe(DaycareObserver::class);

        //Article observer
        Article::observe(ArticleObserver::class);

        //ServiceRegistration observer
        ServiceRegistration::observe(ServiceRegistrationObserver::class);

        //QuizResultObserver observer
        QuizResult::observe(QuizResultObserver::class);

        //CommunityReport observer
        CommunityReport::observe(CommunityReportObserver::class);
    }
}
