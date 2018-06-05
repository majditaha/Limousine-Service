<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\City' => 'App\Policies\CityPolicy',
        'App\Plan' => 'App\Policies\PlanPolicy',
        'App\Discipline' => 'App\Policies\DisciplinePolicy',
        'App\MenuItem' => 'App\Policies\MenuItemPolicy',
        'App\Message' => 'App\Policies\MessagePolicy',
        'App\Page' => 'App\Policies\PagePolicy',
        'App\Practice' => 'App\Policies\PracticePolicy',
        'App\School' => 'App\Policies\SchoolPolicy',
        'App\Section' => 'App\Policies\SectionPolicy',
        'App\Setting' => 'App\Policies\SettingPolicy',
        'App\Subtype' => 'App\Policies\SubtypePolicy',
        'App\Theory' => 'App\Policies\TheoryPolicy',
        'App\Transaction' => 'App\Policies\TransactionPolicy',
        'App\Variant' => 'App\Policies\VariantPolicy',
        'App\User' => 'App\Policies\UserPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
