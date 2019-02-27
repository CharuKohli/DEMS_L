<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserProfile;
class LoginController extends Controller
{
  public function signin(Request $request){
    $users=UserProfile::all();
    $name=$request->input('username');
    $pass=$request->input('password');
    $flag=0;
    $uname=null;
    $msg=null;
    if(count($users)>0){
      foreach($users as $user){
        if($user->name==$name && $user->password==$pass )
        {
          session(['loggedinuser'=>$user->name]);
          session(['userid'=>$user->user_id]);
          $flag=1;
          break;
        }
       }
    }
  if($flag==1){
    $validuser=UserProfile::where('name',$name)->where('password',$pass)->get();
    foreach($validuser as $user){
      if($user->useroradmin==1){
        $msg='admin';
      }else if($user->authorized==1 && $user->useroradmin==0){
        $msg='auth';

      }else if($user->authorized==0 && $user->useroradmin==0){
        $msg='user';

      }
    }
  }
  if($flag==0){
    $msg="0";
  }
    return response()->json(['msg'=>$msg]);

  }

  public function logout(Request $request){
    session()->forget('loggedinuser');
    session()->forget('numofalerts');
    return view('layouts.signin');
  }
}
