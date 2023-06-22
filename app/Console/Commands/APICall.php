<?php

namespace App\Console\Commands;

use App\Environment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Scheduler;
use Carbon\Carbon;
use \App\Libraries\ExecAPI;
use \App\APIInstance;
use \App\APIVersion;

class APICall extends Command
{
    protected $config = [];
    protected $signature = 'api:call {verb} {domain} {slug} {route} {args?*}';
    protected $description = 'Calls and executes specified API';

    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        if (!in_array($this->argument('verb'),['GET','POST','PUT','PATCH','DELETE'])) {
            $this->error('Invalid Verb! Must be: GET,POST,PUT,PATCH,DELETE'); exit();
        }
        $environment = Environment::where('domain',$this->argument('domain'))->first();
        if (is_null($environment)) {
            $this->error('Domain "'.$this->argument('domain').'" Not Found!'); exit();
        }
        $api_instance = APIInstance::where('slug',$this->argument('slug'))
            ->where('environment_id',$environment->id)
            ->with('api')->first();    
        if (is_null($api_instance)) {
            $this->error('API Slug "'.$this->argument('slug').'" Not Found!'); exit();
        }
        if (substr($this->argument('route'),0,1) != '/') {
            $this->error('Routes must start with a "/" --> Invalid: "'.$this->argument('route').'"'); exit();
        }
        $args = [];
        foreach($this->argument('args') as $arg) {
            $args_array = explode('=',$arg);
            if (isset($args_array[1])) {
                $args[$args_array[0]] = $args_array[1];
            } else {
                $this->info('Skipping Argument: "'.$arg.'" --> Missing "=" and value');
            }
        }

        $exec_api = new ExecAPI();
        $_SERVER['REQUEST_METHOD'] = $this->argument('verb');
        $_SERVER['REQUEST_URI'] = '/'.$api_instance->slug.$this->argument('route');
        $_GET = $args;
        $result = $exec_api->eval_code($api_instance);
        if (is_a($result,'Illuminate\Http\Response')) {
            var_dump(json_decode($result->content(),true));
        } else {
            var_dump($result);
        }
    }
}
