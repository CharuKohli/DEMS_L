<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\UserProfile;

class UserDetailsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
      view()->composer('*',function($view){
        $users=null;
        $users=UserProfile::where('useroradmin',0)->get();
        if(count($users)>0){
          $users=$users;
     }else{
       $users=null;
     }
     return $view->with('users',$users);



      });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
