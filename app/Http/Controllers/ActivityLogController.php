<?php

namespace App\Http\Controllers;

use \App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct() {
    }
    
    public function browse() {
        return ActivityLog::all();
    }   

}
