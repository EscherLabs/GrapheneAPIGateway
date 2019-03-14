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
            'resource_type'=>'mysql',
            'type'=>'dev',
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
            'resource_type'=>'constant',
            'type'=>'dev',
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
            'functions'=>[[
                'name'=>'Constructor',
                'content'=>"",
            ],[
                'name'=>'hello_world',
                'content'=>"return ['message'=>'hello world!'];\n",
            ],[
                'name'=>'whoami',
                'content'=>"return ['youare'=>\$args['name']];\n",
            ],[
                'name'=>'echo',
                'content'=>"\$args['pi'] = PI;
return ['args'=>\$args];\n",
            ],[
                'name'=>'mysql_test',
                'content'=>"MySQLDB::connect('PharmacyEMR');
return MySQLDB::query('select * from users');\n",
            ],[
                'name'=>'mysql_test2',
                'content'=>"\$connection = DB::connection('PharmacyEMR');
return \$connection->table('users')->get();\n",
            ]],
            'files'=>[[
                'name'=>'main',
                'content'=>"<?php
class AnotherClass {
    public function whatever(\$args) {
        return 'whatever';
    }
}"
            ]], 
            'resources'=>[
                ['name'=>'PharmacyEMR','type'=>'mysql'],
                ['name'=>'PI','type'=>'constant'],
            ], 
            'routes'=>[
                [
                    'path'=>'/hello_world',
                    'function_name' => 'hello_world',
                    'description'=>'Prints Hello World',
                    'params'=>[['name'=>'other','required'=>false]],
                    'verb' => 'GET',
                ],
                [
                    'path'=>'/whoami',
                    'function_name' => 'whoami',
                    'description'=>'Prints out who you are',
                    'params'=>[['name'=>'name','required'=>true],['name'=>'other','required'=>false]],
                    'verb' => 'GET',
                ],
                [
                    'path'=>'/echo',
                    'function_name' => 'echo',
                    'description'=>'Prints out all args which are sent',
                    'params'=>[],
                    'verb' => 'GET',
                ],
                [
                    'path'=>'/mysql_test',
                    'function_name' => 'mysql_test',
                    'description'=>'Tests Database Connection for PharmacyEMR Database',
                    'params'=>[],
                    'verb' => 'GET',
                ],
                [
                    'path'=>'/mysql_test2',
                    'function_name' => 'mysql_test2',
                    'description'=>'Tests Lumen PDO Database Connection for PharmacyEMR Database',
                    'params'=>[],
                    'verb' => 'GET',
                ]
            ],
        ]);
        $service_version->save();

        $nosql_service = new \App\Service(['name'=>'NoSQLDB','description'=>'This is a default NoSQL Database Service']);
        $nosql_service->save();

        $files = <<<'EOD'
[  
    {  
        "name":"Constructor",
        "content":""
    },
    {  
        "name":"read",
        "content":"if (isset($args[\"id\"])) {\n    $document = \\App\\NoSQLDB::where(\"id\",$args[\"id\"])->first();\n    if (!is_null($document)) {\n        return self::flatten($document);\n    } else {\n        return response(\"document not found\", 404);\n    }\n} else {\n    $documents_obj = \\App\\NoSQLDB::where(\"type\",$args[\"type\"])->get();\n    $documents_arr = [];\n    foreach($documents_obj as $document) {\n        $documents_arr[] = self::flatten($document);\n    }\n    return $documents_arr;\n}"
    },
    {  
        "name":"edit",
        "content":"$document = \\App\\NoSQLDB::where(\"id\",$args[\"id\"])->first();\nif (!is_null($document)) {\n    $document->update([\"data\"=>$request->except([\"created_at\",\"updated_at\",\"id\",\"type\"])]);\n    return self::flatten($document);\n} else {\n    return response(\"document not found\", 404);\n}"
    },
    {  
        "name":"add",
        "content":"$document = new \\App\\NoSQLDB([\"type\"=>$args[\"type\"], \"data\"=>$request->except([\"created_at\",\"updated_at\",\"id\",\"type\"])]);\n$document->save();\nreturn self::flatten($document);"
    },
    {  
        "name":"delete",
        "content":"if ( \\App\\NoSQLDB::where(\"id\",$args[\"id\"])->delete() ) {\n    return [true];\n}"
    },
    {  
        "name":"flatten",
        "content":"$document = $args;\n$document_arr = [\n    \"id\"=>$document->id,\n    \"created_at\"=>$document->created_at->toDateTimeString(),\n    \"updated_at\"=>$document->updated_at->toDateTimeString(),\n];\n$document_arr = array_merge($document_arr,$document->data);\nreturn $document_arr;"
    }
]
EOD;

        $nosql_service_version = new \App\ServiceVersion([
            'service_id'=>$nosql_service->id, 
            'summary'=>'First Version',
            'description'=>'From DB Seed',
            'functions'=>json_decode($files),
            'files'=>[], 
            'resources'=>[], 
            'routes'=>json_decode('[{"path": "/", "verb": "GET", "params": [{"name": "type", "required": "true"}, {"name": "id", "required": "0"}], "description": "", "function_name": "read"}, {"path": "/", "verb": "PUT", "params": [{"name": "type", "required": "true"}, {"name": "id", "required": "true"}], "description": "", "function_name": "edit"}, {"path": "/", "verb": "POST", "params": [{"name": "type", "required": "true"}], "description": "", "function_name": "add"}, {"path": "/", "verb": "DELETE", "params": [{"name": "type", "required": "true"}, {"name": "id", "required": "true"}], "description": "", "function_name": "delete"}]'),
        ]);
        $nosql_service_version->save();

        $service_instance = new \App\ServiceInstance([
            'name'=>'New TestService Instance',
            'slug'=>'test',
            'public'=>false,
            'service_version_id'=>$service_version->id,
            'environment_id'=>$environment->id,
            'service_id'=>$service->id,
            'route_user_map'=>[
                [
                    'route'=>'/hello_world',
                    'api_user'=>$api_user->id
                ],
                [
                    'route'=>'/echo',
                    'api_user'=>$api_user2->id
                ],
                [
                    'route'=>'',
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

        $nosql_service_instance = new \App\ServiceInstance([
            'name'=>'NoSQL Demo',
            'slug'=>'nosqldb_demo',
            'public'=>false,
            'service_version_id'=>$nosql_service_version->id,
            'environment_id'=>$environment->id,
            'service_id'=>$nosql_service->id,
            'route_user_map'=>[
                [
                    'route'=>'',
                    'api_user'=>$api_user3->id
                ]
            ],
            'resources'=>[]
        ]);
        $nosql_service_instance->save();

        $scheduler = new \App\Scheduler([
            'name'=>'Echo Every Min',
            'cron' => '* * * * *',
            'service_instance_id' => $service_instance->id,
            'route' => '/echo',
            'args'=>[['name'=>'hello','value'=>'world']],
            'verb'=>'GET',
        ]);
        $scheduler->save();

        $nosqldoc = new \App\NoSQLDB([
            'type'=>'demo',
            'data' => ["hello"=>"world"],
        ]);
        $nosqldoc->save();
        $nosqldoc = new \App\NoSQLDB([
            'type'=>'demo',
            'data' => [1,3,4,5,6,7,8,9,0],
        ]);
        $nosqldoc->save();
    }
}
