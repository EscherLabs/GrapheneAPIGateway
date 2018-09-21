<?php

namespace App\Http\Controllers;

use \App\SchedulerController;
use Illuminate\Http\Request;

class SchedulerController extends Controller
{
    public function __construct() {
    }
    
    public function browse() {
        return SchedulerController::all();
    }   

    public function read($scheduler_id)
    {
        $scheduler = SchedulerController::where('id',$scheduler_id)->first();
        if (!is_null($scheduler)) {
            return $scheduler;
        } else {
            return response('scheduler not found', 404);
        }
    }

    public function edit(Request $request, $scheduler_id)
    {
        $scheduler = SchedulerController::where('id',$scheduler_id)->first();
        if (!is_null($scheduler)) {
            $scheduler->update($request->all());
            return $scheduler;
        } else {
            return response('scheduler not found', 404);
        }
    }

    public function add(Request $request)
    {
        $scheduler = new SchedulerController($request->all());
        $scheduler->save();
        return $scheduler;
    }

    public function delete($scheduler_id)
    {
        if ( SchedulerController::where('id',$scheduler_id)->delete() ) {
            return [true];
        }
    }

}
