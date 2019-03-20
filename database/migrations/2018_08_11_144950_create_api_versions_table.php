<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAPIVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_versions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('api_id')->unsigned()->index();
            $table->string('summary')->default('');
            $table->string('description')->default('');
            $table->boolean('stable')->default(false);
            $table->json('files')->nullable();
            $table->json('functions')->nullable();
            $table->json('resources')->nullable();
            $table->json('routes')->nullable();
            $table->string('user_id')->nullable()->default(null);
            $table->timestamps();
            $table->foreign('api_id')->references('id')->on('apis')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_versions');
    }
}
