<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function activity_logs()
    {
        $activity_logs = Activity::all();

        return response()->json(['activity_logs' => $activity_logs], 200);
    }
}
