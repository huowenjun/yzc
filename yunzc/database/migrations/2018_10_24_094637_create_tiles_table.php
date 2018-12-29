<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tiles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('brand_id')->comment('品牌id');
            $table->char('img',100)->comment('封面图片');
            $table->string('images',600)->comment('图片集1');
            $table->string('name', 100)->comment('品牌名称');
            $table->text('description')->comment('简介');
            $table->string('cate', 255)->nullable()->comment('备用');
            $table->string('photos', 600)->comment('图片集2');
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
        Schema::dropIfExists('tiles');
    }
}
