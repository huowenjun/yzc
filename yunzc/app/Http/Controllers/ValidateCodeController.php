<?php
namespace App\Http\Controllers;

//验证码类
class ValidateCodeController
{

    public $charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';    //随机因子

    //生成随机码
    public function yzm() {
        $_len = strlen($this->charset)-1;
        $code = '';
        for ($i=0;$i<4;$i++) {
            $code .= $this->charset[mt_rand(0,$_len)];
        }
        return $code;
    }


}