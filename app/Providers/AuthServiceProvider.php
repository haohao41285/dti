<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Session;
use Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        
        \Illuminate\Support\Facades\Auth::provider('customuserprovider', function($app, array $config) {
            return new CustomUserProvider($app['hash'], $config['model']);
        });

        Gate::define('permission',function($name,$permission)
            {
                $permission_list_session = Session::get('permission_list_session');

                if(isset($permission_list_session[$name][$permission]))

                    return $permission_list_session[$name][$permission] == 1 ;
                else
                    return 1 == 0;
            });

    }
}
