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
            $table->integer('belong_id')->unsigned();
            $table->foreign('belong_id')->references('id')->on('menu_name')->onDelete('cascade');
            $table->integer('parent_id')->default('0')->unsigned();
            $table->foreign('parent_id')->references('id')->on('menu_name');
            $table->string('title', 64)->nullable();
            $table->index('title');
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
