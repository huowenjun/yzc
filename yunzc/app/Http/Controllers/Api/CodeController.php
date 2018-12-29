<?php
/**
 * Created by PhpStorm.
 * User: 霍文俊
 * Date: 2018/12/11
 * Time: 13:52
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ValidateCodeController;
use Illuminate\Support\Facades\Redis;


class CodeController extends Controller
{
    /*
     * 生产验证码
     */
    public function code()
    {
        $yzm = new ValidateCodeController();
        $code = $yzm->yzm();
        $redis = new Redis();
        $bool = $redis::set(strtolower($code),strtolower($code));
        $redis::expire(strtolower($code),60);
        if(!$bool)
        {
            return msg_err(PassportController::NO_STATUS,'网络错误');
        }
        return msg_ok(PassportController::YSE_STATUS,'验证码',['code'=>$code]);
    }


}