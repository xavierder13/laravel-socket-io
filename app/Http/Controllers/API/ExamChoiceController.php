<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\ExamChoice;
use Validator;

class ExamChoiceController extends Controller
{
    public function index()
    {
        $exam_choices = ExamChoice::all();

        return response()->json(['exam_choices' => $exam_choices], 200);
    }

    public function store(Request $request)
    {
        
        $validator = $this->validator($request, null);

        if($validator->fails())
        {   
            return response()->json($validator->errors(), 200);
        }

        $exam_choice = ExamChoice::create([
            'question_id' => $request->question_id,
            'choice_text' => $request->choice_text,
        ]);

        return response()->json(['success' => 'Record has been added', 'exam_choice' => $exam_choice], 200);

    }

    public function update(Request $request)
    {
        $validator = $this->validator($request, null);

        if($validator->fails())
        {   
            return response()->json($validator->errors(), 200);
        }

        ExamChoice::where('id', $request->choice_id)
                ->update([
                    'question_id' => $request->question_id,
                    'choice_text' => $request->choice_text,
                ]);

        $exam_choice = ExamChoice::find($request->choice_id);

        return response()->json(['success' => 'Record has been updated', 'exam_choice' => $exam_choice], 200);
    }

    public function delete(Request $request)
    {
        ExamChoice::findOrFail('id', $request->choice_id)->delete();

        return response()->json(['success' => 'Record has been deleted'], 200);
    }

    public function validator($request, $id)
    {   

        $rules = [
            'question_id.required' => 'Question ID is required',
            'question_id.integer' => 'Question ID must be an integer',
            'choice_text.required' => 'Choice Text is required',
            'choice_text.unique' => 'Choice Text already exists',
        ];

        $valid_fields = [
            'question_id' => 'required|integer',
            'choice_text' => [
                'required',
                Rule::unique('exam_choices')->where(function ($query) use ($request, $id) {
                    return $query->where('choice_text', '!=', $request->choice_text)
                                 ->ignore($id);
                })
            ],
        ];

        return Validator::make($request->all(), $valid_fields, $rules);
    }
}
