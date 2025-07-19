<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exam;
use App\ExamAnswerSheet;
use App\ExamChoice;
use App\ExamQuestion;
use App\ExamResult;
use Validator;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::all();

        return response()->json(['exams' => $exams], 200);
    }

    public function store(Request $request)
    {
        
        $validator = $this->validator($request, null);

        if($validator->fails())
        {   
            return response()->json($validator->errors(), 200);
        }

        // save Exam
        $exam = Exam::create([
            'title' => $request->title,
            'description' => $request->description,
            'active' => $request->active,
            'passing_score' => $request->passing_score,
        ]);

        return response()->json(['success' => 'Record has been added', 'exam' => $exam], 200);
    }

    public function update(Request $request)
    {

        $validator = $this->validator($request, $request->id);

        if($validator->fails())
        {   
            return response()->json($validator->errors(), 200);
        }

        // save Exam
        Exam::where('id', $request->id)
            ->update([
                'title' => $request->title,
                'description' => $request->description,
                'active' => $request->active,
                'passing_score' => $request->passing_score,
            ]);

        $exam = Exam::find($request->id);
        
        return response()->json(['success' => 'Record has been updated', 'exam' => $exam], 200);
    }


    public function save_exam_result(Request $request, $exam_code)
    {
        $rules = [
            '*.question_id.required' => 'Question ID is required',
            '*.question_id.integer' => 'Question ID must be an integer',
            '*.answer_text.required' => 'Answer Text is required',
            '*.choice_id.required' => 'Choice ID is required',
            '*.choice_id.integer' => 'Choice ID must be an integer',
            '*.points.integer' => 'Points must be an integer',
        ];

        $valid_fields = [
            '*.question_id' => 'required|integer',
            '*.answer_text' => 'required',
            '*.choice_id' => 'required|integer',
            '*.points' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $valid_fields, $rules);

        if($validator->fails())
        {   
            return response()->json($validator->errors(), 200);
        }

        foreach ($request->exam_result as $result) {
            $data = [
                'exam_code' => $exam_code,
                'question_id' => $result['answer_text'],
                'answer_text' => $result['answer_text'],
                'choice_id' => $result['choice_id'],
                'points' => $result['points'],
            ];

            if($request->exam_result_id)
            {
                ExamResult::where('id', $request->choice_id)
                                ->update($data);
            }
            else
            {
                ExamResult::create($data);
            }
        }

        return response()->json(['success' => 'Record has been added'], 200);
    }

    public function delete(Request $request)
    {
        $exam_id = $request->exam_id;
        Exam::findOrFail($exam_id)->delete();
        $exam_question_ids = ExamQuestion::where('exam_id', $exam_id)->pluck('id');

        ExamQuestion::where('exam_id', $exam_id)->delete();
        ExamChoice::whereIn('question_id', $exam_question_ids)->delete();
        ExamAnswerSheet::whereIn('question_id', $exam_question_ids)->delete();   
        
        return response()->json(['success' => 'Record has been deleted'], 200);

    }

    public function validator($request, $id)
    {   
        $rules = [
            'title.required' => 'Title is required',
            'title.unique' => 'Title already exists',
            'description.required' => 'Description is required',
            'active.required' => 'Status is required',
            'active.boolean' => 'Invalid value',
            'passing_score.required' => 'Passing Score is required',
            'passing_score.integer' => 'Passing Score must be an integer',
        ];

        $valid_fields = [
            'title' => 'required|unique:exams,title,'.$id,
            'description' => 'required',
            'active' => 'required|boolean',
            'passing_score' => 'required|integer',
        ];
        
        return Validator::make($request->all(), $valid_fields, $rules);
    }

    


}
