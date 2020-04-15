<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('menu_name_id')->unsigned();
            $table->foreign('menu_name_id')->references('id')->on('menu_name');
            $table->string('title', 64)->nullable();
            $table->index('title');
            $table->integer('parent_id')->unsigned()->default('0');
            $table->string('slug', 255)->nullable();
            $table->string('target', 64)->nullable();
            $table->string('item', 64)->nullable();
            $table->string('class', 64)->nullable();
            $table->string('attr', 64)->nullable();
            $table->smallInteger('sort')->unsigned()->default('500');
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
        Schema::dropIfExists('menu');
    }
}
