<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\LocumShift;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Wallet::class => \App\Policies\WalletPolicy::class,
        \App\Models\FacilityWallet::class => \App\Policies\FacilityWalletPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('manage-shift', function (User $user, LocumShift $locumShift) {
            return $user->id === $locumShift->facility->user_id;
        });
        //
    }
}
