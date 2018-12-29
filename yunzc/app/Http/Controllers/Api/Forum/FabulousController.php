<?php
/**
 * 赞
 * User: hwj
 * Date: 2018/11/28
 * Time: 16:22
 */
namespace App\Http\Controllers\Api\Forum;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\PassportController;
use App\Models\Fabulous;

class FabulousController extends Controller
{
    /**
     * 未读点赞数量
     * @return \Illuminate\Http\JsonResponse
     */
    public function noRead()
    {
        $data = request()->all();
        if(empty($data)||
            !isset($data['api_token'])
        ){
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $redis = GetAppToken($data['api_token']);
        if($redis['code'] == 200){
            $data['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $fabulousObj = new Fabulous();
        $num = $fabulousObj->no_read($data['users_id']);
        $res = new \stdClass();
        $res->num = $num;
        return msg_ok(PassportController::YSE_STATUS,'未读点赞次数',$res);

    }

    /**
     * 查看未读消息--点赞未读
     * @return \Illuminate\Http\JsonResponse
     */
    public function seeNoRead()
    {
        $data = request()->all();
        if(empty($data)||
            !isset($data['api_token'])
        ){
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $redis = GetAppToken($data['api_token']);
        if($redis['code'] == 200){
            $data['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        if(!isset($data['now_page'])){
            $data['now_page'] = 1;
        }
        $res = new \stdClass();
        $fabulousObj = new Fabulous();
        $res->data = $fabulousObj->see_no_read($data);
        $res->last_page = ceil($fabulousObj->sel_job_recruits_page($data)/10);
        $res->current_page = intval($data['now_page']);
        return msg_ok(PassportController::YSE_STATUS,'未读点赞记录',$res);
    }
}