<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDesignInstitutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('design_institutes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title',200)->comment('标题');
            $table->string('images',500)->comment('图片');
            $table->text('description')->comment('简介');
            $table->integer('scale')->comment('人员规模');
            $table->string('city_id')->comment('地区');
            $table->string('address', 200)->comment('详细地址');
            $table->tinyInteger('status')->comment('显示');
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
        Schema::dropIfExists('design_institutes');
    }
}
