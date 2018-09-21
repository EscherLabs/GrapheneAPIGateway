<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchedulerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheduler', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('');
            $table->string('cron')->default('0 0 5 31 2 ?');
            $table->integer('service_instance_id')->unsigned()->index();
            $table->string('route')->nullable();
            $table->json('args')->nullable();
            $table->string('verb')->default('GET');
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
        Schema::dropIfExists('scheduler');
    }
}
