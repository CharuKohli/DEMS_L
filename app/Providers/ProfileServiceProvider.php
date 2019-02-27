<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\UserProfile;
class ProfileServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
      view()->composer('*',function($view){
        $userid=session('userid');
        $profile=null;
        $profiles=UserProfile::where('user_id',$userid)->get();
        if(count($profiles)>0){
        foreach($profiles as $profile){
          $profile=$profile;
        }
      }else{
        $profile=null;
      }
        return $view->with('profile',$profile);

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
