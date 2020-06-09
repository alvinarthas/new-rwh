<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;

class CheckUser
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
        Carbon::setLocale('id');
        if ($request->session()->has('isLoggedIn')) {
            $role = session('role');

            if($role == "Superadmin" || $role == "Direktur Utama" || $role == "General Manager" || $role == "Assistant General Manager"){
                return $next($request);
            }else{

                $time = Carbon::now(); // Current time
                $start = Carbon::create($time->year, $time->month, $time->day, 22, 0, 0); //set time to 10:00
                $end = Carbon::create($time->year, $time->month, $time->day, 6, 0, 0); //set time to 18:00
                if($time < $end || $time > $start){
                    $request->session()->flush();
                    return redirect()->route('getHome');
                }else{
                    return $next($request);
                }
            }
        }else{
            return redirect()->route('getHome');
        }
    }
}
