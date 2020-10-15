<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAPIUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('environment_id')->unsigned()->index();
            $table->string('app_name',255)->nullable()->default(null);
            $table->string('app_secret',255)->nullable()->default(null);
            $table->string('encrypted_app_secret',1024)->nullable()->default(null);
            $table->unique(['environment_id', 'app_name']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_users');
    }
}
