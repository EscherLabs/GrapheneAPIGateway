<?php

namespace App\Http\Controllers;

use \App\Models\User;
use Illuminate\Http\Request;
use \Carbon\Carbon;

class UsersController extends Controller
{
    public function __construct() {
    }
    
    public function browse() {
        return User::orderby('name')->get();
    }   

    public function read($user_id)
    {
        $user = User::where('id',$user_id)->first();
        if (!is_null($user)) {
            return $user;
        } else {
            return response('api not found', 404);
        }
    }


    public function edit(Request $request, $user_id)
    {
        $user = User::where('id',$user_id)->first();
        if (!is_null($user)) {
            $user->update($request->all());
            return $user;
        } else {
            return response('user not found', 404);
        }
    }

    public function add(Request $request)
    {
        $user = new User($request->all());
        $user->save();
        return $user;
    }

    public function delete($user_id)
    {
        if ( User::where('id',$user_id)->delete() ) {
            return [true];
        }
    }

}
