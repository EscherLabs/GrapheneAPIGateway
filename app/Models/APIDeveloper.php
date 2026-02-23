<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class APIDeveloper extends Model
{
    protected $table = 'api_developers';
    public $timestamps = false;
    protected $fillable = ['api_id','user_id'];

    public function apis()
    {
        return $this->belongsTo(API::class);
    }

    public function users(){
        return $this->belongsTo(User::class);
    }

}