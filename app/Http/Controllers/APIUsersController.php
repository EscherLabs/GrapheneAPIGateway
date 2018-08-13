<?php

namespace App\Http\Controllers;

use \App\APIUser;

class APIUsersController extends Controller
{
    public function __construct() {
    }
    
    public function browse() {
        return APIUser::all();
    }   
}
