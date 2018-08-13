<?php

namespace App\Http\Controllers;

use \App\Environment;

class EnvironmentsController extends Controller
{
    public function __construct() {
    }
    
    public function browse() {
        return Environment::all();
    }   
}
