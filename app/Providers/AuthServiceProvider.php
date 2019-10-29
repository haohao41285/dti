<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Session;
use Gate;
use App\Models\MainPermissionDti;

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

        Gate::define('permission',function($user,$permission)
        {
            $permission_list = MainPermissionDti::where('permission_slug',$permission)->first();

            if($permission_list != ""){
                $permission_list_session = Session::get('permission_list_session');
                $permission_id = $permission_list->id;
                if(in_array(intval($permission_id),$permission_list_session)){
                    return true;
                }
                else return false;
            }else
                return false;
        });
    }
}
