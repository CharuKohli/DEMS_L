<?php

namespace App\Http\Controllers\Auth;

use App\UserProfile;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class RegisterController extends Controller
{

    public function store(Request $request)
    {
      request()->validate([
        'name' => ['required', 'string', 'max:30'],
          'mobile'=>['required','min:10','max:10'],
          'email' => ['required', 'string', 'email', 'max:50', 'unique:users'],
          'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
       //$user=new UserProfile;
         UserProfile::create($request()->all());
        echo'User registered successfully';
    }
}
