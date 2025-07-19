<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    protected $fillable = ['exam_code', 'question_id', 'answer_text', 'choice_id', 'points'];
}
