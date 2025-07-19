<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class ExamResultMaintenance
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        //Exam Result Record
        if($request->is('api/exam_result/index')){
            if($user->can('exam-result-list')){
                return $next($request); 
            }
        }

        //Exam Result Create
        if($request->is('api/exam_result/store')){
            if($user->can('exam-result-create')){
                return $next($request); 
            }
        }

        //Exam Result Edit
        if($request->is('api/exam_result/update')){
            if($user->can('exam-result-edit')){
                return $next($request); 
            }
        }

        //Exam Result Delete
         if($request->is('api/exam_result/delete')){
            if($user->can('exam-result-delete')){
                return $next($request); 
            }
        }

        return abort(401, 'Unauthorized');
    }
}
