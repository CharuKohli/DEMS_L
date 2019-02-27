<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Ticket;
use App\UserProfile;

class UserTicketServiceProvider extends ServiceProvider
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
        try{
        $useroradmin=UserProfile::where('user_id',$userid)->where('useroradmin',1)->get();
        $authuser=UserProfile::where('user_id',$userid)->where('authorized',1)->get();
      }catch(Exception $e){
        return back()->withError('Sorry, there are no records')->withInput();
      }
        if(count($authuser)>0){
          $tickets=Ticket::all();
          session(['authuser'=>'ok']);
         }
         else if(count($useroradmin)>0){
        $tickets=Ticket::all();
        session()->forget('authuser');
        }else{
        $tickets=Ticket::where('user_id',$userid)->get();
        session()->forget('authuser');
        }
        return $view->with('tickets',$tickets);
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
