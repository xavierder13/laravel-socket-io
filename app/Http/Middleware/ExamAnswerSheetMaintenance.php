<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class ExamAnswerSheetMaintenance
{

    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        //Exam Answer Sheet Record
        if($request->is('api/exam_answer_sheet/index')){
            if($user->can('exam-answer-sheet-list')){
                return $next($request); 
            }
        }

        //Exam Answer Sheet Create
        if($request->is('api/exam_answer_sheet/store')){
            if($user->can('exam-answer-sheet-create')){
                return $next($request); 
            }
        }

        //Exam Answer Sheet Edit
        if($request->is('api/exam_answer_sheet/update')){
            if($user->can('exam-answer-sheet-edit')){
                return $next($request); 
            }
        }

        //Exam Answer Sheet Delete
         if($request->is('api/exam_answer_sheet/delete')){
            if($user->can('exam-answer-sheet-delete')){
                return $next($request); 
            }
        }

        return abort(401, 'Unauthorized');
    }
}
