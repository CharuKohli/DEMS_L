<?php

namespace App\Http\Middleware;

use Closure;
use App\UserProfile;

class DemsAdminReg
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      $users=UserProfile::where('useroradmin',1)->get();
      if(count($users)>0){
        return redirect('signin');
      }
       return redirect('register');

   }
}
