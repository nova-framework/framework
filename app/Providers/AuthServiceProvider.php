<?php

namespace App\Providers;

use Nova\Auth\Access\GateInterface as Gate;
use Nova\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Nova\Support\Facades\Cache;

use App\Models\Permission;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = array(
        'App\Models\Option'     => 'App\Policies\OptionPolicy',
        'App\Models\Permission' => 'App\Policies\PermissionPolicy',
        'App\Models\Role'       => 'App\Policies\RolePolicy',
        'App\Models\User'       => 'App\Policies\UserPolicy',
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

        $this->registerPermissions($gate);
    }

    protected function registerPermissions(Gate $gate)
    {
        // Retrieve the Permission items, caching them for 24 hours.
        $permissions = Cache::remember('system_permissions', 1440, function ()
        {
            return Permission::getResults();
        });

        foreach ($permissions as $permission) {
            // For convenience, we will define the Permission by its slug as Gate ability,
            // and resolving it as a callable using the native User's permissions checking.

            $gate->define($permission->slug, function ($user) use ($permission)
            {
                return $user->hasPermission($permission->slug);
            });
        }
    }
}
