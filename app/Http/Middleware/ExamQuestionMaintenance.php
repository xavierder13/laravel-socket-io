<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class ExamQuestionMaintenance
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        //Exam Question Record
        if($request->is('api/exam_question/index')){
            if($user->can('exam-question-list')){
                return $next($request); 
            }
        }

        //Exam Question Create
        if($request->is('api/exam_question/store')){
            if($user->can('exam-question-create')){
                return $next($request); 
            }
        }

        //Exam Question Edit
        if($request->is('api/exam_question/update')){
            if($user->can('exam-question-edit')){
                return $next($request); 
            }
        }

        //Exam Question Delete
         if($request->is('api/exam_question/delete')){
            if($user->can('exam-question-delete')){
                return $next($request); 
            }
        }

        return abort(401, 'Unauthorized');
    }
}
