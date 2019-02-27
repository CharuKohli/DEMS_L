<?php

namespace App\Http\Middleware;

use Closure;
use App\UserProfile;
class RoleBasedLogin
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


      $userid=session('userid');
      $adminuser=UserProfile::where('user_id',$userid)->where('useroradmin',1)->get();
      if(count($adminuser)>0){
        session(['adminsection'=>'ok']);
        return $next($request);
      }else if(session('loggedinuser')){
        session()->forget('adminsection');
        return $next($request);
      }
      session()->forget('loggedinuser');
      return redirect('signin');

}
}
