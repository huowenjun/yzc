<?php
/**
 * 清仓特卖
 * User: Administrator
 * Date: 2018/11/26
 * Time: 8:55
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClearingHouses;



class ClearingHousesController extends Controller
{
    const CREATE_STATUS_TURE_YES = '添加成功';
    const CREATE_STATUS_TURE_NO = '添加失败';
    const TURE = '查询成功';
    public $cObj;
    public function __construct()
    {
        $this->cObj = new ClearingHouses();
    }

    public static function obj()
    {
        return new \stdClass();
    }

    /**
     * 获取清仓数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClearingHouses() {
        $clearingHousesInfo = request()->all();
        $info = $this->obj();
        if (isset($clearingHousesInfo['id'])) {//取详情--id
            $info = $this->cObj->sel_clearing_houses_info($clearingHousesInfo['id']);
            $info = res_time($info);
        }else{//取列表1时间先后2审核通过，3。数据条数

            //默认10条+默认页码1
            if(!isset($clearingHousesInfo['now_page'])){
                $clearingHousesInfo['now_page'] = 1;
            }
            //分页数据
            $info->data = $this->cObj->sel_clearing_houses_list($clearingHousesInfo);
            //总页码
            $info->last_page = ceil($this->cObj->sel_clearing_houses_page($clearingHousesInfo)/10);
            //当前页码
            $info->current_page = intval($clearingHousesInfo['now_page']);
        }
        return msg_ok(PassportController::YSE_STATUS,self::TURE,$info);
    }

    /**
     * 设置清仓入库数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function setClearingHouses() {
        $clearingHousesInfo = request()->all();
        if (empty($clearingHousesInfo) ||
            !isset($clearingHousesInfo['univalent']) ||
            !isset($clearingHousesInfo['unit'])||
            !isset($clearingHousesInfo['number'])||
            !isset($clearingHousesInfo['company'])||
            !isset($clearingHousesInfo['mode'])||
            !isset($clearingHousesInfo['invoice'])||
            !isset($clearingHousesInfo['room_city'])||
            !isset($clearingHousesInfo['address'])||
            !isset($clearingHousesInfo['remarks'])||
            !isset($clearingHousesInfo['contacts_name'])||
            !isset($clearingHousesInfo['contacts_tel'])||
            !isset($clearingHousesInfo['photo'])||
            !isset($clearingHousesInfo['api_token'])
        ) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        if(!tel_preg($clearingHousesInfo['contacts_tel'])){
            return msg_err(PassportController::NO_STATUS,'手机号格式不正确');
        }
        $redis = GetAppToken($clearingHousesInfo['api_token']);
        if($redis['code'] == 200){
            $clearingHousesInfo['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }

        $clearingHousesInfo = handleData($clearingHousesInfo);
        //数据验证通过
        $bool = $this->cObj->create($clearingHousesInfo);
        if(!$bool){
            return msg_err(PassportController::NO_STATUS,self::CREATE_STATUS_TURE_NO);
        }
        set_release_num($clearingHousesInfo['users_id']);
        return msg_ok(PassportController::YSE_STATUS,self::CREATE_STATUS_TURE_YES,$this->obj());
    }

    /**
     * 获取我发的清仓特卖列表以及详情
     * api_token
     */
    public function getMyClearingHouses()
    {
        $clearingHousesInfo = request()->all();
        $info = $this->obj();
        if (isset($clearingHousesInfo['id'])) {//取详情--id
            $info = $this->cObj->sel_clearing_houses_info($clearingHousesInfo['id']);
            $info = res_time($info);
        }else{//取列表1时间先后2审核通过，4。数据条数
                //默认10条+默认页码1
            $redis = GetAppToken($clearingHousesInfo['api_token']);
            if($redis['code'] == 200){
                $clearingHousesInfo['users_id'] = $redis['u_id'];
             }else{
                return msg_err($redis['code'],$redis['msg']);
             }
            if(!isset($clearingHousesInfo['now_page'])){
                $clearingHousesInfo['now_page'] = 1;
            }
            if (!array_key_exists('f',$clearingHousesInfo)) {
                return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
            }
            //分页数据
            $info->data = $this->cObj->sel_clearing_houses_list($clearingHousesInfo);
            //总页码
            $info->last_page = ceil($this->cObj->sel_clearing_houses_page($clearingHousesInfo)/10);
            //当前页码
            $info->current_page = intval($clearingHousesInfo['now_page']);

        }
        return msg_ok(PassportController::YSE_STATUS,self::TURE,$info);
    }

    /**
     * 删除我的
     */
    public function delMyClearingHouses()
    {
        $clearingHousesInfo = request()->all();
        $redis = GetAppToken($clearingHousesInfo['api_token']);
        if($redis['code'] == 200){
            $clearingHousesInfo['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        if (!array_key_exists('id',$clearingHousesInfo)) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $bool = $this->cObj->delMyData($clearingHousesInfo['id']);
        if(!$bool){
            return msg_err(PassportController::NO_STATUS,'删除失败',$this->obj());
        }
        return msg_ok(PassportController::YSE_STATUS,'删除成功',$this->obj());
    }
}