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
        $environment = new \App\Environment(['domain'=>'localhost:8000','name'=>'localdev']);
        $environment->save();

        $api_user = new \App\APIUser(['app_name'=>'test']);
        $api_user->app_secret = 'test';
        $api_user->save();
        $api_user2 = new \App\APIUser(['app_name'=>'test2']);
        $api_user2->app_secret = 'test2';
        $api_user2->save();
        $api_user3 = new \App\APIUser(['app_name'=>'test3']);
        $api_user3->app_secret = 'test3';
        $api_user3->save();

        $database = new \App\Database(['name'=>'PharmacyEMR','type'=>'mysql']);
        $database->save();

        $database_instance = new \App\DatabaseInstance([
            'name'=>'local_dev',
            'database_id'=>$database->id,
            'config'=>[
                'server' => '127.0.0.1',
                'user' => 'pharmacyemr',
                'pass' => 'pharmacyemr',
                'name' => 'PharmacyEMR',
            ],
        ]);
        $database_instance->save();

        $module = new \App\Module(['name'=>'TestModule','description'=>'This is a test']);
        $module->save();

        $module_version = new \App\ModuleVersion([
            'module_id'=>$module->id, 
            'summary'=>'First Version',
            'code'=>[[
                'name'=>'main',
                'content'=>"
class TestModule {
    public function hello_world(\$args) {
        return ['message'=>'hello world!'];
    }

    public function whoami(\$args) {
        return ['youare'=>\$args['name']];
    }

    public function echo(\$args) {
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
            'databases'=>[$database->id], 
            'routes'=>[
                [
                    'path'=>'/hello_world/*',
                    'function_name' => 'hello_world',
                    'description'=>'Prints Hello World',
                    'required'=>'',
                    'optional'=>'',
                    'verb' => 'GET',
                ],
                [
                    'path'=>'/whoami/*',
                    'function_name' => 'whoami',
                    'description'=>'Prints out who you are',
                    'required'=>'name',
                    'optional'=>'',
                    'verb' => 'GET',
                ],
                [
                    'path'=>'/echo/*',
                    'function_name' => 'echo',
                    'description'=>'Prints out all args which are sent',
                    'required'=>'',
                    'optional'=>'',
                    'verb' => 'GET',
                ],
                [
                    'path'=>'/mysql_test/*',
                    'function_name' => 'mysql_test',
                    'description'=>'Tests Database Connection for PharmacyEMR Database',
                    'required'=>'',
                    'optional'=>'',
                    'verb' => 'GET',
                ],
                [
                    'path'=>'/mysql_test2/*',
                    'function_name' => 'mysql_test2',
                    'description'=>'Tests Lumen PDO Database Connection for PharmacyEMR Database',
                    'required'=>'',
                    'optional'=>'',
                    'verb' => 'GET',
                ]
            ],
        ]);
        $module_version->save();

        $module_instance = new \App\ModuleInstance([
            'name'=>'New TestModule Instance',
            'slug'=>'test',
            'public'=>false,
            'module_version_id'=>$module_version->id,
            'environment_id'=>$environment->id,
            'module_id'=>$module->id,
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
            'database_instance_map'=>[
                [
                    'database'=>$database->id,
                    'database_instance'=>$database_instance->id,
                ]   
            ]
        ]);
        $module_instance->save();
    }
}
