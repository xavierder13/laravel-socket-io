<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class RoleMaintenance
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
        //Role Record
        if($request->is('api/role/index')){
            if(Auth::user()->can('role-list')){
                return $next($request); 
            }
        }

        //Role Create
        if($request->is('api/role/store')){
            if(Auth::user()->can('role-create')){
                return $next($request); 
            }
        }

        //Role Edit
        if($request->is('api/role/edit/*') || $request->is('api/role/update/*')){
            if(Auth::user()->can('role-edit')){
                return $next($request); 
            }
        }

        //Role Delete
        if($request->is('api/role/delete')){
            if(Auth::user()->can('role-delete')){
                return $next($request); 
            }
        }

        return abort(401, 'Unauthorized');
    }
}
