<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class ExamMaintenance
{

    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        //Exam Record
        if($request->is('api/exam/index')){
            if($user->can('exam-list')){
                return $next($request); 
            }
        }

        //Exam Create
        if($request->is('api/exam/store')){
            if($user->can('exam-create')){
                return $next($request); 
            }
        }

        //Exam Edit
        if($request->is('api/exam/update')){
            if($user->can('exam-edit')){
                return $next($request); 
            }
        }

        //Exam Delete
         if($request->is('api/exam/delete')){
            if($user->can('exam-delete')){
                return $next($request); 
            }
        }

        return abort(401, 'Unauthorized');
    }
}
