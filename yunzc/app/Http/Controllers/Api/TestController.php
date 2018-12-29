<?php
/**
 * Created by PhpStorm.
 * User: liule
 * Date: 2018/10/7
 * Time: 16:08
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\ValidateCodeController;

class TestController
{

    public function test()
    {
        $yzm = new ValidateCodeController();
        $yzm->doimg();
        //$yzm->getCode();
    }
}