<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class UserMaintenance
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

        //User Record
        if($request->is('api/user/index')){
            if(Auth::user()->can('user-list')){
                return $next($request); 
            }
        }

        //User Create
        if($request->is('api/user/store')){
            if(Auth::user()->can('user-create')){
                return $next($request); 
            }
        }

        //User Edit
        if($request->is('api/user/edit/*') || $request->is('api/user/update/*')){
            if(Auth::user()->can('user-edit')){
                return $next($request); 
            }
        }

        //User Delete
        if($request->is('api/user/delete')){
            if(Auth::user()->can('user-delete')){
                return $next($request); 
            }
        }

        //User Roles Permissions
        if($request->is('api/user/roles_permissions')){
                return $next($request); 
        }

        return abort(401, 'Unauthorized');

        // return $next($request);
    }
}
