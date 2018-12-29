<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forums', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title',200)->comment('论坛标题');
            $table->string('source', 100)->comment('来源');
            $table->integer('page_view')->comment('浏览量');
            $table->text('description')->comment('论坛内容');
            $table->string('images',600)->comment('图片');
            $table->integer('user_id')->comment('用户');
            $table->integer('contentable_id')->comment('类型id');
            $table->string('contentable_type',200)->comment('类型');
            $table->tinyInteger('status')->default('1')->comment('是否发表');
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
        Schema::dropIfExists('forums');
    }
}
