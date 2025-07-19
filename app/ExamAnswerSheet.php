<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExamAnswerSheet extends Model
{
    protected $fillable = ['question_id', 'answer_text', 'choice_id'];
}
