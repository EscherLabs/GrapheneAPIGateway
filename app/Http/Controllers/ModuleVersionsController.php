<?php

namespace App\Http\Controllers;

use \App\ModuleVersion;

class ModuleVersionsController extends Controller
{
    public function __construct() {
    }
    
    public function browse() {
        return ModuleVersion::all();
    }   
}
