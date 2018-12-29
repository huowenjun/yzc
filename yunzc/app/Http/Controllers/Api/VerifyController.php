<?php

namespace App\Http\Controllers\Api;

use App\Models\Verification;
use App\User;
use Carbon\Carbon;
use chuanglan\ChuanglanSmsApi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use Validator;

class VerifyController extends Controller
{
    //验证码接口
    public function get_code(Request $request){
        $validator = Validator::make($request->all(),
            [
                'tel' => 'required|regex:/^1[3456789][0-9]{9}$/',
                'type' => 'required|in:1,2,3,4,5',
                'code'=>'required|regex:/^\w{4}$/',
            ],[],[
                'tel'=>'手机号码',
                'type' => '验证码类型',
                'code'=>'验证码',
            ]);
        if ($validator->fails()) {
            return msg_err(PassportController::NO_STATUS,$validator->errors()->first());
        }
        $input = $request->all();
        $redis = new Redis();
        $bool = $redis::get(strtolower($input['code']));
        if(!$bool)
        {
            return msg_err(PassportController::NO_STATUS,'验证码验证不通过');
        }
        $user = User::where(['tel'=>$input['tel']])->count();
        //生成验证码bingfasong
        $code = rand(1000,9999);
        $chuanglan = new ChuanglanSmsApi();
        //类型：1=注册，2=登陆，3=找回密码
        $msg = '';
        switch ($input['type']){
            case 1:
                $msg = '注册';
                if($user){
                    return msg_err(401,'用户已存在');
                }
                break;
            case 2:
                if(!$user){
                    return msg_err(401,'用户不存在');
                }
                $msg = '登陆';
                break;
            case 3:
                if(!$user){
                    return msg_err(401,'用户不存在');
                }
                $msg = '找回密码';
                break;
            case 4:
//                if(!$user){
//                    return msg_err(401,'用户不存在');
//                }
                $msg = '绑定手机';
                break;
            case 5:
//                if(!$user){
//                    return msg_err(401,'用户不存在');
//                }
                $msg = '修改密码';
        }
        $chuanglan->sendSMS($input['tel'],'您的'.$msg.'验证码为:'.$code.',十分钟有效');
        $input['code'] = $code;
        //十分钟有效
        $dateTime = Carbon::now()->addMinutes(10)->toDateTimeString();
        $data['expiration'] = $dateTime;
        $data['type'] = $input['type'];
        $data['code'] = $code;
        $data['tel'] = $input['tel'];
        $verifyCode = new Verification();
        $verifyCode::create($data);
        return msg_ok(PassportController::YSE_STATUS,'发送成功',null);
    }

}
