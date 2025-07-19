<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExamChoice extends Model
{
    protected $fillable = ['question_id', 'choice_text'];
}
