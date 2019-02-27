<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FinYear;
use App\UserProfile;
use App\ExpenseName;
use App\Utility;
class AdminController extends Controller
{
    public function createFinYear(Request $request){
       $finyear=new FinYear;
       $yy=$request->fin_year;
       $years=Finyear::all();
       $flag=0;
       foreach($years as $year){
         if($year->fin_year==$yy){
           $flag=1;
           break;
         }
       }
       if($flag==0){
       $finyear->fin_year=$yy;
       $finyear->save();
       $msg="1";
     }else if($flag==1){
       $msg="0";
     }
       return response()->json(['msg'=>$msg]);
    }

    public function createExpenseName(Request $request){
       $expname=new ExpenseName;
       $expname->exp_name=$request->exp_name;
       $due_date=null;
       if($request->due_date!=null){
       $due_date=date('Y-m-d',strtotime($request->due_date));
        }
        $expname->due_date=$due_date;
       $expname->for_whom=$request->for_whom;
       $expname->save();
       $msg="Expense name created";
       return response()->json(['msg'=>$msg]);
    }

    public function getUserIds(Request $request){
       $userids=UserProfile::select('user_id')->where('useroradmin',0)->get();
       return response()->json(['userids'=>$userids]);
    }
    public function getFinYears(Request $request){
       $years=FinYear::orderBy('fin_year','asc')->get();
       return response()->json(['finyears'=>$years]);
    }

   public function getAllUsers(){
     $html = view('partials.dispUsers',compact('users'))->render();
     return response()->json(['html'=>$html]);
   }

   public function deleteUser($id){
     UserProfile::find($id)->delete();
     $html = view('partials.dispUsers',compact('users'))->render();
     return response()->json(['html'=>$html]);
   }
   public function updateUser(Request $request){
       $id=$request->input('id');
     UserProfile::where('id',$id)->update($request->except('_token'));
     session()->forget('id');
     $message="User Data Updated successfully";
     return response()->json(['msg'=>$message]);
   }


   public function editUser($id){
     session(['id'=>$id]);
     $user=null;
     $users=UserProfile::where('id',$id)->get();
     foreach($users as $user){
       $user=$user;
     }
     return response()->json(['user'=>$user]);
   }

public function dispExpenseNames(){
  $expnames=ExpenseName::all();
  $html = view('partials.dispExpNames',compact('expnames'))->render();
  return response()->json(['html'=>$html]);
}

public function deleteExpName($id){
  ExpenseName::find($id)->delete();
$expnames=ExpenseName::all();
  $html = view('partials.dispExpNames',compact('expnames'))->render();
  return response()->json(['html'=>$html]);
}

public function editExpName($id){
  $expname=null;
  session(['id'=>$id]);
  $expnames=ExpenseName::where('id',$id)->get();
  foreach($expnames as $expname){
    $expname=$expname;
  }
  return response()->json(['expname'=>$expname]);
}

public function updateExpName(Request $request){
  $id=session('id');
  $data=$request->except('_token');
  $data['due_date']=date('Y-m-d',strtotime($request->due_date));
  ExpenseName::where('id',$id)->update($data);
  session()->forget('id');
  $message="Expense Name Updated successfully";
  return response()->json(['msg'=>$message]);
}


function setDateFormat(Request $request){
  $date_format=null;
  $msg=null;
  $ids=Utility::all();
  foreach($ids as $id){
  Utility::where('id',$id->id)->update(['set_date_format'=>0]);
}
  Utility::where('date_format',$request->dateformat)->update(['set_date_format'=>'1']);
  $dateformats=Utility::where('set_date_format','1')->get();
  foreach($dateformats as $dateformat){
    $date_format=$dateformat->date_format;
  }

  session(['dateformat'=>$date_format]);
  return response()->json(['msg'=>$date_format]);

}

function getDateFormat(){
    $date_format=null;
  $dateformats=Utility::where('set_date_format','1')->get();
  foreach($dateformats as $dateformat){
    $date_format=$dateformat->date_format;
  }
  session(['dateformat'=>$date_format]);
  return response()->json(['msg'=>$date_format]);
}
}
