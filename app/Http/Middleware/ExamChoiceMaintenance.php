<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class ExamChoiceMaintenance
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        //Exam Choice Record
        if($request->is('api/exam_choice/index')){
            if($user->can('exam-choice-list')){
                return $next($request); 
            }
        }

        //Exam Choice Create
        if($request->is('api/exam_choice/store')){
            if($user->can('exam-choice-create')){
                return $next($request); 
            }
        }

        //Exam Choice Edit
        if($request->is('api/exam_choice/update')){
            if($user->can('exam-choice-edit')){
                return $next($request); 
            }
        }

        //Exam Choice Delete
         if($request->is('api/exam_choice/delete')){
            if($user->can('exam-choice-delete')){
                return $next($request); 
            }
        }

        return abort(401, 'Unauthorized');
    }
}
