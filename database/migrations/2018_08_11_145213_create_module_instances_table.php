<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuleInstancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_instances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('environment_id')->unsigned()->index();
            $table->integer('module_id')->unsigned()->index();
            $table->integer('module_version_id')->unsigned()->nullable()->default(null);
            $table->string('name');
            $table->string('slug');
            $table->boolean('public')->default(false);
            $table->json('route_user_map')->nullable();
            $table->json('database_instance_map')->nullable();
            $table->timestamps();
            $table->foreign('module_id')->references('id')->on('modules');
            $table->foreign('environment_id')->references('id')->on('environments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('module_instances');
    }
}
