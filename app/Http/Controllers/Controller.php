<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use DateTime;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function convertDate($date){
      $date1=null;
      if(session('dateformat')=="m-d-Y"){
        $date2= DateTime::createFromFormat("m-d-Y",$date);
       $date1=$date2->format('Y-m-d');
     }else{
      $date1=date("Y-m-d",strtotime($date));
    }
    return $date1;
    }
}
