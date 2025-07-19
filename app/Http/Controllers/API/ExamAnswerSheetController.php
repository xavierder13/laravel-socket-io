<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\ExamAnswerSheet;
use Validator;

class ExamAnswerSheetController extends Controller
{
    public function index()
    {
        $exam_answer_sheets = ExamAnswerSheet::all();

        return response()->json(['exam_answer_sheets' => $exam_answer_sheets], 200);
    }

    public function store(Request $request)
    {
        
        $validator = $this->validator($request, null);

        if($validator->fails())
        {   
            return response()->json($validator->errors(), 200);
        }

        $exam_answer_sheet = ExamAnswerSheet::create([
            'question_id' => $request->question_id,
            'answer_text' => $request->answer_text,
            'choice_id' => $request->choice_id,
        ]);

        return response()->json(['success' => 'Record has been added', 'exam_answer_sheet' => $exam_answer_sheet], 200);

    }

    public function update(Request $request)
    {
        $validator = $this->validator($request, null);

        if($validator->fails())
        {   
            return response()->json($validator->errors(), 200);
        }

        ExamAnswerSheet::where('id', $request->question_id)
                        ->update([
                            'question_id' => $request->question_id,
                            'answer_text' => $request->answer_text,
                            'choice_id' => $request->choice_id,
                        ]);

        $exam_answer_sheet = ExamAnswerSheet::find($request->question_id);

        return response()->json(['success' => 'Record has been updated', 'exam_answer_sheet' => $exam_answer_sheet], 200);
    }

    public function delete(Request $request)
    {
        ExamAnswerSheet::findOrFail('id', $request->question_id)->delete();

        return response()->json(['success' => 'Record has been deleted'], 200);
    }

    public function validator($request, $id)
    {   

        $rules = [
            'question_id.required' => 'Question ID is required',
            'question_id.integer' => 'Question ID must be an integer',
            'answer_text.required' => 'Answer Text is required',
            'choice_id.required' => 'Choice ID is required',
            'choice_id.integer' => 'Choice ID must be an integer',
        ];

        $valid_fields = [
            'question_id.required' => 'Question ID is required',
            'question_id.integer' => 'Question ID must be an integer',
            'answer_text.required' => 'Answer Text is required',
            'choice_id.required' => 'Choice ID is required',
            'choice_id.integer' => 'Choice ID must be an integer',
        ];

        return Validator::make($request->all(), $valid_fields, $rules);
    }
}
