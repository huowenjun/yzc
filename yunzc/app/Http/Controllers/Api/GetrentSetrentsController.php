<?php
/**
 * 寻租转租
 * User: Administrator
 * Date: 2018/11/26
 * Time: 10:17
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GetrentSetrents;



class GetrentSetrentsController extends Controller
{
    const CREATE_STATUS_TURE_YES = '添加成功';
    const CREATE_STATUS_TURE_NO = '添加失败';
    const TURE = '查询成功';
    public $gObj;
    public function __construct()
    {
        $this->gObj = new GetrentSetrents();
    }

    public static function obj()
    {
        return new \stdClass();
    }

    /**
     * 获取数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGetrentSetrents()
    {
        $getrentSetrentsInfo = request()->all();
        $info = $this->obj();
        if (isset($getrentSetrentsInfo['id'])) {//取详情--id
            $info = $this->gObj->sel_getrent_setrents_info($getrentSetrentsInfo['id']);
            $info = res_time($info);
        }else{//取列表1时间先后2审核通过，3。城市范围4。数据条数
            if(empty($getrentSetrentsInfo) ||
                !isset($getrentSetrentsInfo['type'])){
                return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
            }

            if(!isset($getrentSetrentsInfo['now_page'])){
                $getrentSetrentsInfo['now_page'] = 1;
            }
            //f分页数据
            $info->data = $this->gObj->sel_getrent_setrents_list($getrentSetrentsInfo);
            //总页码
            $info->last_page = ceil($this->gObj->sel_getrent_setrents_page($getrentSetrentsInfo)/10);
            //当前页码
            $info->current_page = intval($getrentSetrentsInfo['now_page']);
        }

        return msg_ok(PassportController::YSE_STATUS,self::TURE,$info);
    }

    /**
     * 设置数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function setGetrentSetrents()
    {
        $getrentSetrentsInfo = request()->all();
        if (empty($getrentSetrentsInfo) ||
            !isset($getrentSetrentsInfo['type']) ||
            !isset($getrentSetrentsInfo['acreage'])||
            !isset($getrentSetrentsInfo['money'])||
            !isset($getrentSetrentsInfo['room_city'])||
            !isset($getrentSetrentsInfo['address'])||
            !isset($getrentSetrentsInfo['start_time'])||
            !isset($getrentSetrentsInfo['set_money'])||
            !isset($getrentSetrentsInfo['shops_id'])||
            !isset($getrentSetrentsInfo['matching'])||
            !isset($getrentSetrentsInfo['contacts_name'])||
            !isset($getrentSetrentsInfo['contacts_tel'])||
            !isset($getrentSetrentsInfo['photo'])||
            !isset($getrentSetrentsInfo['api_token'])
        ) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        if(!tel_preg($getrentSetrentsInfo['contacts_tel'])){
            return msg_err(PassportController::NO_STATUS,'手机号格式不正确');
        }
        $redis = GetAppToken($getrentSetrentsInfo['api_token']);
        if($redis['code'] == 200){
            $getrentSetrentsInfo['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $getrentSetrentsInfo = handleData($getrentSetrentsInfo);
        //数据验证通过

        $bool = $this->gObj->create($getrentSetrentsInfo);
        if(!$bool){
            return msg_err(PassportController::NO_STATUS,self::CREATE_STATUS_TURE_NO);
        }
        set_release_num($getrentSetrentsInfo['users_id']);
        return msg_ok(PassportController::YSE_STATUS,self::CREATE_STATUS_TURE_YES,$this->obj());
    }

    /**
     * 我发布的
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMyGetrentSetrents()
    {
        $getrentSetrentsInfo = request()->all();
        $info = $this->obj();
        if (isset($getrentSetrentsInfo['id'])) {//取详情--id
            $info = $this->gObj->sel_getrent_setrents_info($getrentSetrentsInfo['id']);
            $info = res_time($info);
        }else {//取列表1时间先后2审核通过，3。城市范围4。数据条数
            $redis = GetAppToken($getrentSetrentsInfo['api_token']);
            if($redis['code'] == 200){
                $getrentSetrentsInfo['users_id'] = $redis['u_id'];
            }else{
                return msg_err($redis['code'],$redis['msg']);
            }
            if (!isset($getrentSetrentsInfo['f'])) {
                return msg_err(PassportController::NO_STATUS, PassportController::PARAM);
            }
            if (!isset($getrentSetrentsInfo['now_page'])) {
                $getrentSetrentsInfo['now_page'] = 1;
            }
            //f分页数据
            $info->data = $this->gObj->sel_getrent_setrents_list($getrentSetrentsInfo);
            //总页码
            $info->last_page = ceil($this->gObj->sel_getrent_setrents_page($getrentSetrentsInfo) / 10);
            //当前页码
            $info->current_page = intval($getrentSetrentsInfo['now_page']);
        }
        return msg_ok(PassportController::YSE_STATUS,self::TURE,$info);
    }

    /**
     * 删除我发布的
     */
    public function delMyGetrentSetrents()
    {
        $getrentSetrentsInfo = request()->all();
        $redis = GetAppToken($getrentSetrentsInfo['api_token']);
        if($redis['code'] == 200){
            $getrentSetrentsInfo['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        if (!isset($getrentSetrentsInfo['id'])) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $bool = $this->gObj->del_my_data($getrentSetrentsInfo['id']);
        if(!$bool){
            return msg_err(PassportController::NO_STATUS,'删除失败',$this->obj());
        }
        return msg_ok(PassportController::YSE_STATUS,'删除成功',$this->obj());
    }


}