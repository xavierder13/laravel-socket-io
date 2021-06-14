<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class PermissionMaintenance
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
        //Permission Record
        if($request->is('api/permission/index')){
            if(Auth::user()->can('permission-list')){
                return $next($request); 
            }
        }

        //Permission Create
        if($request->is('api/permission/store')){
            if(Auth::user()->can('permission-create')){
                return $next($request); 
            }
        }

        //Permission Edit
        if($request->is('api/permission/edit/*') || $request->is('api/permission/update/*')){
            if(Auth::user()->can('permission-edit')){
                return $next($request); 
            }
        }

        //Permission Delete
        if($request->is('api/permission/delete')){
            if(Auth::user()->can('permission-delete')){
                return $next($request); 
            }
        }

        return abort(401, 'Unauthorized');
    }
}
