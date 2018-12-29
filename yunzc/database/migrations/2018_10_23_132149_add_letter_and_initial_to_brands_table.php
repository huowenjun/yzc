<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLetterAndInitialToBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brands', function (Blueprint $table) {
            //
            $table->addColumn('char','letter',['length'=>50,'comment'=>'拼音']);
            $table->addColumn('char','initial',['length'=>5,'comment'=>'首字母']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('brands', function (Blueprint $table) {
            //
           $table->dropColumn('letter');
           $table->dropColumn('initial');
        });
    }
}
