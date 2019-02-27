<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Expenses;
use App\UserProfile;
class UserExpensesServiceProvider extends ServiceProvider
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
        $voucherpath=array();
        $useroradmin=UserProfile::where('user_id',$userid)->where('useroradmin',1)->get();
        if(count($useroradmin)>0){
        $expenses=Expenses::paginate(5);
        foreach($expenses as $data){
          $billpath=$data->vouchers;
          foreach($billpath as $path){
            array_push($voucherpath,$path->voucher_path);
          }
        }
        }else{
        $expenses=Expenses::where('user_id',$userid)->get();
        foreach($expenses as $data){
          $billpath=$data->vouchers;
          foreach($billpath as $path){
            array_push($voucherpath,$path->voucher_path);
          }
        }
        }
        return $view->with(['userexpenses'=>$expenses,'voucherpath'=>$voucherpath]);
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
