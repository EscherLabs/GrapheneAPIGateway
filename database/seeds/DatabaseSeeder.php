<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $environment = new \App\Environment([
            'domain'=>'localhost:8000',
            'name'=>'localdev',
            'type'=>'dev',
        ]);
        $environment->save();

        $api_user = new \App\APIUser(['app_name'=>'test']);
        $api_user->app_secret = 'test';
        $api_user->environment_id = $environment->id;
        $api_user->save();
        $api_user2 = new \App\APIUser(['app_name'=>'test2']);
        $api_user2->app_secret = 'test2';
        $api_user2->environment_id = $environment->id;
        $api_user2->save();
        $api_user3 = new \App\APIUser(['app_name'=>'test3']);
        $api_user3->app_secret = 'test3';
        $api_user3->environment_id = $environment->id;
        $api_user3->save();

        $resource = new \App\Resource([
            'name'=>'PharmacyEMR_local',
            'type'=>'mysql',
            'config'=>[
                'server' => '127.0.0.1',
                'user' => 'pharmacyemr',
                'pass' => 'pharmacyemr',
                'name' => 'PharmacyEMR',
            ],
        ]);
        $resource->save();

        $resource2 = new \App\Resource([
            'name'=>'PI',
            'type'=>'constant',
            'config'=>[
                'value' => '3.14159',
            ],
        ]);
        $resource2->save();

        $service = new \App\Service(['name'=>'TestService','description'=>'This is a test']);
        $service->save();

        $service_version = new \App\ServiceVersion([
            'service_id'=>$service->id, 
            'summary'=>'First Version',
            'description'=>'From DB Seed',
            'code'=>[[
                'name'=>'main',
                'content'=>"
<?php
class TestService {
    public function hello_world(\$args) {
        return ['message'=>'hello world!'];
    }

    public function whoami(\$args) {
        return ['youare'=>\$args['name']];
    }

    public function echo(\$args) {
        \$args['pi'] = PI;
        return ['args'=>\$args];
    }

    public function mysql_test(\$args) {
        MySQLDB::connect('PharmacyEMR');
        return MySQLDB::query('select * from users');
    }

    public function mysql_test2(\$args) {
        \$connection = DB::connection('PharmacyEMR');
        return \$connection->table('users')->get();
    }
}"
            ]], 
            'resources'=>[
                ['name'=>'PharmacyEMR','type'=>'mysql'],
                ['name'=>'PI','type'=>'constant'],
            ], 
            'routes'=>[
                [
                    'path'=>'/hello_world/*',
                    'function_name' => 'hello_world',
                    'description'=>'Prints Hello World',
                    'params'=>[['name'=>'other','required'=>false]],
                    'verb' => 'GET',
                ],
                [
                    'path'=>'/whoami/*',
                    'function_name' => 'whoami',
                    'description'=>'Prints out who you are',
                    'params'=>[['name'=>'name','required'=>true],['name'=>'other','required'=>false]],
                    'verb' => 'GET',
                ],
                [
                    'path'=>'/echo/*',
                    'function_name' => 'echo',
                    'description'=>'Prints out all args which are sent',
                    'params'=>[],
                    'verb' => 'GET',
                ],
                [
                    'path'=>'/mysql_test/*',
                    'function_name' => 'mysql_test',
                    'description'=>'Tests Database Connection for PharmacyEMR Database',
                    'params'=>[],
                    'verb' => 'GET',
                ],
                [
                    'path'=>'/mysql_test2/*',
                    'function_name' => 'mysql_test2',
                    'description'=>'Tests Lumen PDO Database Connection for PharmacyEMR Database',
                    'params'=>[],
                    'verb' => 'GET',
                ]
            ],
        ]);
        $service_version->save();

        $service_instance = new \App\ServiceInstance([
            'name'=>'New TestService Instance',
            'slug'=>'test',
            'public'=>false,
            'service_version_id'=>$service_version->id,
            'environment_id'=>$environment->id,
            'service_id'=>$service->id,
            'route_user_map'=>[
                [
                    'route'=>'/hello_world*',
                    'api_user'=>$api_user->id
                ],
                [
                    'route'=>'/echo*',
                    'api_user'=>$api_user2->id
                ],
                [
                    'route'=>'*',
                    'api_user'=>$api_user3->id
                ]
            ],
            'resources'=>[
                [
                    'name'=>'PharmacyEMR',
                    'resource'=>$resource->id,
                ],
                [
                    'name'=>'PI',
                    'resource'=>$resource2->id,
                ]  
            ]
        ]);
        $service_instance->save();

        $scheduler = new \App\Scheduler([
            'name'=>'Echo Every Min',
            'cron' => '* * * * *',
            'service_instance_id' => $service_instance->id,
            'route' => '/echo/',
            'args'=>[['name'=>'hello','value'=>'world']],
            'verb'=>'GET',
        ]);
        $scheduler->save();
    }
}
