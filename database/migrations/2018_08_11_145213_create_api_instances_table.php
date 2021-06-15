<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAPIInstancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_instances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('environment_id')->unsigned()->index();
            $table->integer('api_id')->unsigned()->index();
            $table->integer('api_version_id')->unsigned()->nullable()->default(null);
            $table->string('name');
            $table->string('slug');
            $table->boolean('public')->default(false);
            $table->enum('errors',['none','all'])->default('none');
            $table->json('route_user_map')->nullable();
            $table->json('resources')->nullable();
            $table->json('options')->nullable();
            $table->timestamps();
            $table->foreign('api_id')->references('id')->on('apis');
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
        Schema::dropIfExists('api_instances');
    }
}
