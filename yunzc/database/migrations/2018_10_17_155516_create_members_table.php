<?php

//use Illuminate\Support\Facades\Schema;
use Jialeo\LaravelSchemaExtend\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->comment('用户昵称');
            $table->string('tel',12)->comment('手机号');
            $table->string('password', 255)->comment('密码');
            $table->string('head',200)->comment('头像');
            $table->dateTime('first_login_at')->comment('首次登陆');
            $table->dateTime('last_login_at')->comment('最后一次登录');
            $table->string('account', 100)->comment('用户账号');
            $table->string('remark',200)->comment('备注');
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
        Schema::dropIfExists('members');
    }
}
