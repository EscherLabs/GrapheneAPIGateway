<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDatabaseInstancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('database_instances', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('database_id')->unsigned()->index();
            $table->json('config')->nullable();
            $table->timestamps();
            $table->foreign('database_id')->references('id')->on('databases');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('database_instances');
    }
}
