<?php

namespace App\Http\Middleware;

use App\Models\Permission;
use App\Models\Sidebar\Submenu;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class IsPermitted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::id();
        $link = $request->segment(1);

        $permissiontable=(new Permission)->getTable();
        $submenu=(new Submenu)->getTable();

        $data = DB::table($permissiontable)
            ->join($submenu,"$submenu.id","=","$permissiontable.submenu_id")
            ->where("$permissiontable.user_id",$user)
            ->where("$submenu.link",$link);

        $data = $data->select("$submenu.id")->get();

        if(count($data)>=1) {
            return $next($request);
        }
        else {
            return redirect('/');
        }
    }
}
