<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesStoragesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files_storages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('originalName',100)->nullable();
            $table->char('ext',50)->nullable();
            $table->char('type',20)->nullable();
            $table->char('realPath',100)->nullable();
            $table->string('fileName')->nullable();
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
        Schema::dropIfExists('files_storages');
    }
}
