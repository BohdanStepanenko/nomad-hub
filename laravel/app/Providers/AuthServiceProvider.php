<?php

namespace App\Providers;

use App\Models\ForumComment;
use App\Models\ForumPost;
use App\Models\ForumTopic;
use App\Models\TaxInfo;
use App\Policies\ForumCommentPolicy;
use App\Policies\ForumPostPolicy;
use App\Policies\ForumTopicPolicy;
use App\Policies\TaxInfoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        ForumTopic::class => ForumTopicPolicy::class,
        ForumPost::class => ForumPostPolicy::class,
        ForumComment::class => ForumCommentPolicy::class,
        TaxInfo::class => TaxInfoPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Passport::ignoreRoutes();

        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }
}
