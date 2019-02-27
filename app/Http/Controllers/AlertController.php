<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ExpenseName;
class AlertController extends Controller
{

    function getDueDateAlerts(){
      $duedate=array();
      $expname=array();
      $count=0;
      $currentdate=strtotime(date('Y-m-d'));
      if(session('adminsection')){
        $alerts=ExpenseName::all();
        $pre_due_date=null;
        foreach($alerts as $alert){
          // $from_date=date_create($alert->from_date);
          // $to_date=date_create($alert->to_date);
          // $diff=date_diff($to_date,$from_date)->format("%a");

          for($i=5;$i>=0;$i--){
            $due_date=strtotime(date('Y-m-d',strtotime("$alert->due_date -$i days")));
            if($currentdate==$due_date){
              if($pre_due_date!=$due_date){
            array_push($duedate,$alert->due_date);
            array_push($expname,$alert->exp_name);
            break;
          }
          }else{
            $pre_due_date=$due_date;
          }
        }
      }
    }else{
      $alerts=ExpenseName::where('for_whom',session('userid'))->orwhere('for_whom','general')->get();
      $pre_due_date=null;
      foreach($alerts as $alert){
        // $from_date=date_create($alert->from_date);
        // $to_date=date_create($alert->to_date);
        // $diff=date_diff($to_date,$from_date)->format("%a");

        for($i=5;$i>=0;$i--){
          $due_date=strtotime(date('Y-m-d',strtotime("$alert->due_date -$i days")));
          if($currentdate==$due_date){
            if($pre_due_date!=$due_date){
          array_push($duedate,$alert->due_date);
          array_push($expname,$alert->exp_name);
          break;
        }
        }else{
          $pre_due_date=$due_date;
        }
      }
    }
     }
      $html = view('layouts.alerts',compact('duedate','expname'))->render();
        return response()->json(['html'=>$html]);

}

function getNotificationCount(){
  $count=0;
  if(session('adminsection')){
    $alerts=ExpenseName::all();
    $count=$this->getCount($alerts);

  }else{
  $alerts=ExpenseName::where('for_whom',session('userid'))->orwhere('for_whom','general')->get();
  $count=$this->getCount($alerts);
  }
  return response()->json(["count"=>$count]);
}


function getCount($alerts){
  $count=0;
  $pre_due_date=null;
  $currentdate=strtotime(date('Y-m-d'));
  foreach($alerts as $alert){
    // $from_date=date_create($alert->from_date);
    // $to_date=date_create($alert->to_date);
    // $diff=date_diff($to_date,$from_date)->format("%a");

    for($i=5;$i>=0;$i--){
      $due_date=strtotime(date('Y-m-d',strtotime("$alert->due_date -$i days")));
      if($currentdate==$due_date){
        if($pre_due_date!=$due_date){
      $count++;
      break;
    }
    }else{
      $pre_due_date=$due_date;
    }
  }
}
  return $count;
}

}
