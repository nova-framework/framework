<?php

namespace Modules\Users\Providers;

use Nova\Auth\Access\GateInterface as Gate;
use Nova\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = array(
        'Modules\Users\Models\FieldItem' => 'Modules\Users\Policies\FieldItemPolicy',
        'Modules\Users\Models\User'      => 'Modules\Users\Policies\UserPolicy',
    );


    /**
     * Register any application authentication / authorization services.
     *
     * @param  Nova\Auth\Access\GateInterface  $gate
     * @return void
     */
    public function boot(Gate $gate)
    {
        $this->registerPolicies($gate);

        //
    }
}
