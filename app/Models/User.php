<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;


class User extends Model implements AuthenticatableContract

{
    use Authenticatable;

    protected $table = 'users';

    protected $fillable = ['unique_id','admin','developer','active'];

    protected $casts = ['admin' => 'boolean','active' => 'boolean','developer' => 'boolean'];


    public function apis() {
        return $this->hasMany(API::class);
    }

    public function is_api_developer() {
        return APIDeveloper::where('user_id',$this->id)->exists();
    }

}