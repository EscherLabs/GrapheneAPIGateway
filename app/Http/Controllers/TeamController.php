<?php

namespace App\Http\Controllers;

use App\User;
use App\Team;
use App\TeamMember;
use App\TeamMessage;
use App\TeamNote;
use App\TeamScenario;

use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function __construct()
    {
        //
    }

    public function browse()
    {
        $teams = Team::all();
        return $teams;
    }

    public function read($team_id)
    {
        $team = Team::where('id',$team_id)->with('team_scenario')->with('team_members.user')->first();
        if (!is_null($team)) {
            return $team;
        } else {
            abort(404);
        }
    }

    public function edit(Request $request, $team_id)
    {
        $team = Team::where('id',$team_id)->first();
        $team->update($request->all());
        return $team;
    }

    public function add(Request $request)
    {
        $team = new Team($request->all());
        $team->save();
        return $team;
    }

    public function delete($team_id)
    {
        if ( Team::where('id',$team_id)->delete() ) {
            return [true];
        }
    }

    public function list_members($team_id)
    {
        $team = TeamMember::where('team_id',$team_id)->with('user')->get();
        if (!is_null($team)) {
            return $team;
        } else {
            abort(404);
        }
    }

    public function add_member(Request $request, $team_id, $user_id)
    {
        $team = Team::where('id',$team_id)->first();
        $user = User::where('id',$user_id)->first();

        if (!is_null($team) && !is_null($user)) {
            $team_member = new TeamMember(['user_id'=>$user_id,'team_id'=>$team_id]);
            if ($request->has('role_id')) {
                $team_member->role_id = $request->role_id;
            }
            if ($request->has('admin')) {
                $team_member->admin = $request->admin;
            }
            $team_member->save();
            return $team_member;
        } else {
            abort(404);
        }
    }

    public function remove_member($team_id, $user_id)
    {
        if ( TeamMember::where('team_id',$team_id)->where('user_id',$user_id)->delete() ) {
            return [true];
        }
    }

    public function list_messages($team_id)
    {
        $team = TeamMessage::where('team_id',$team_id)->with('user')->get();
        if (!is_null($team)) {
            return $team;
        } else {
            abort(404);
        }
    }

    public function add_message(Request $request, $team_id, $user_id)
    {
        $this->validate($request,['message'=>['required']]);
        $team = Team::where('id',$team_id)->first();
        $user = User::where('id',$user_id)->first();

        if (!is_null($team) && !is_null($user)) {
            $team_message = new TeamMessage(['user_id'=>$user_id,'team_id'=>$team_id,'message'=>$request->message]);
            $team_message->save();
            return $team_message;
        } else {
            abort(404);
        }
    }

    public function list_notes($team_id)
    {
        $team = TeamNote::where('team_id',$team_id)->with('user')->get();
        if (!is_null($team)) {
            return $team;
        } else {
            abort(404);
        }
    }

    public function add_note(Request $request, $team_id, $user_id)
    {
        $this->validate($request,['note'=>['required']]);
        $team = Team::where('id',$team_id)->first();
        $user = User::where('id',$user_id)->first();

        if (!is_null($team) && !is_null($user)) {
            $team_note = new TeamNote(['user_id'=>$user_id,'team_id'=>$team_id,'note'=>$request->note]);
            $team_note->save();
            return $team_note;
        } else {
            abort(404);
        }
    }

    public function list_scenario_logs($team_id)
    {
        $team_scenario = TeamScenario::where('team_id',$team_id)->with('user')->get();
        if (!is_null($team_scenario)) {
            return $team_scenario;
        } else {
            abort(404);
        }
    }

    public function add_scenario_log(Request $request, $team_id, $user_id)
    {
        $this->validate($request,['scenario'=>['required']]);
        $team = Team::where('id',$team_id)->first();
        $user = User::where('id',$user_id)->first();

        if (!is_null($team) && !is_null($user)) {
            $team_scenario = new TeamScenario(['user_id'=>$user_id,'team_id'=>$team_id,'scenario'=>$request->scenario]);
            $team_scenario->save();
            $team->team_scenario_id = $team_scenario->id;
            $team->save();
            return $team_scenario;
        } else {
            abort(404);
        }
    }




}
