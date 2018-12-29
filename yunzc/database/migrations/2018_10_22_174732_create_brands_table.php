<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->comment('品牌名称');
            $table->string('images', 500)->comment('图片');
            $table->string('trade_mark',100)->comment('商标');
            $table->text('description')->comment('企业简介');
            $table->char('link')->comment('企业官网');
            $table->string('honors',500)->comment('企业荣誉图片');
            $table->char('service_tel',20)->comment('客服电话');
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
        Schema::dropIfExists('brands');
    }
}
