<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\ExamQuestion;
use Validator;

class ExamQuestionController extends Controller
{
    public function index()
    {
        $exam_questions = ExamQuestion::all();

        return response()->json(['exam_questions' => $exam_questions], 200);
    }

    public function store(Request $request)
    {
        
        $validator = $this->validator($request, null);

        if($validator->fails())
        {   
            return response()->json($validator->errors(), 200);
        }

        $exam_question = ExamQuestion::create([
            'exam_id' => $request->exam_id,
            'type' => $request->type,
            'question_text' => $request->question_text,
            'points' => $request->points,
        ]);

        return response()->json(['success' => 'Record has been added', 'exam_question' => $exam_question], 200);

    }

    public function update(Request $request)
    {
        $validator = $this->validator($request, null);

        if($validator->fails())
        {   
            return response()->json($validator->errors(), 200);
        }

        ExamQuestion::where('id', $request->question_id)
                    ->update([
                        'exam_id' => $request->exam_id,
                        'type' => $request->type,
                        'question_text' => $request->question_text,
                        'points' => $request->points,
                    ]);
            
        $exam_question = ExamQuestion::find($request->question_id);

        return response()->json(['success' => 'Record has been updated', 'exam_question' => $exam_question], 200);
    }

    public function delete(Request $request)
    {
        ExamQuestion::findOrFail('id', $request->question_id)->delete();

        return response()->json(['success' => 'Record has been deleted'], 200);
    }

    public function validator($request, $id)
    {   

        $rules = [
            'exam_type.required' => 'Exam Type is required',
            'question_text.required' => 'Question Text is required',
            'question_text.unique' => 'Question Text already exists',
            'points.required' => 'Points is required',
            'points.integer' => 'Points must be an integer',
        ];

        $valid_fields = [
            'exam_type' => 'required',
            'question_text' => [
                'required',
                Rule::unique('exam_questions')->where(function ($query) use ($request, $id) {
                    return $query->where('question_text', '!=', $request->question_text)
                                 ->ignore($id);
                })
            ],
            'points' => 'required|integer',
        ];

        return Validator::make($request->all(), $valid_fields, $rules);
    }
}
