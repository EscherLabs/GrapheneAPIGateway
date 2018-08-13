<?php

namespace App\Http\Controllers;

use \App\Database;

class DatabasesController extends Controller
{
    public function __construct() {
    }
    
    public function browse() {
        return Database::all();
    }   
}
