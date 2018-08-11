<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuleVersionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_versions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('module_id')->unsigned()->index();
            $table->string('summary')->default('');
            $table->string('description')->default('');
            $table->boolean('stable')->default(false);
            $table->json('code')->nullable();
            $table->json('databases')->nullable();
            $table->json('routes')->nullable();
            $table->integer('user_id')->unsigned()->index()->nullable()->default(null);
            $table->timestamps();
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('module_versions');
    }
}
