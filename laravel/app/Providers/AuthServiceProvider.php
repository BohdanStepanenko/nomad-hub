<?php

namespace App\Providers;

use App\Models\Recommendation;
use App\Models\TestResult;
use App\Models\UserSkill;
use App\Policies\User\RecommendationPolicy;
use App\Policies\User\TestResultPolicy;
use App\Policies\User\UserSkillPolicy;
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
        UserSkill::class => UserSkillPolicy::class,
        TestResult::class => TestResultPolicy::class,
        Recommendation::class => RecommendationPolicy::class,
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
