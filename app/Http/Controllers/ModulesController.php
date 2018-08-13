<?php

namespace App\Http\Controllers;

use \App\Module;

class ModulesController extends Controller
{
    public function __construct() {
    }
    
    public function browse() {
        return Module::all();
    }   
}
