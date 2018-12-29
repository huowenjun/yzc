<?php
/**
 * 招商代理
 * User: Administrator
 * Date: 2018/11/26
 * Time: 10:37
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agents;

class AgentsController extends Controller
{
    const CREATE_STATUS_TURE_YES = '添加成功';
    const CREATE_STATUS_TURE_NO = '添加失败';
    const TURE = '查询成功';
    public $aObj;

    public function __construct()
    {
        $this->aObj = new Agents();
    }

    public static function obj()
    {
        return new \stdClass();
    }

    public function getAgents()
    {
        $agentsInfo = request()->all();
        $info = $this->obj();
        if (isset($agentsInfo['id'])) {//取详情--id
            $info = $this->aObj->sel_agents_info($agentsInfo['id']);
            $info = res_time($info);
        }else{//取列表1时间先后2审核通过，3。城市范围4。数据条数
            if(empty($agentsInfo) ||
                !isset($agentsInfo['type'])){
                return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
            }
            if(isset($agentsInfo['room_city']))
            {
                if(!isset($agentsInfo['now_page'])){
                    $agentsInfo['now_page'] = 1;
                }
                //分页数据
                $info->data = $this->aObj->sel_agents_list($agentsInfo);
                //总页码
                $info->last_page = ceil($this->aObj->sel_agents_page($agentsInfo)/10);
                //当前页码
                $info->current_page = intval($agentsInfo['now_page']);
            }
        }
        return msg_ok(PassportController::YSE_STATUS,self::TURE,$info);
    }

    public function setAgents()
    {
        $agentsInfo = request()->all();
        if (empty($agentsInfo) ||
            !isset($agentsInfo['type']) ||
            !isset($agentsInfo['company'])||
            !isset($agentsInfo['brand'])||
            !isset($agentsInfo['room_city'])||
            !isset($agentsInfo['acreage'])||
            !isset($agentsInfo['policy'])||
            !isset($agentsInfo['photo'])||
            !isset($agentsInfo['api_token'])
        ) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $redis = GetAppToken($agentsInfo['api_token']);
        if($redis['code'] == 200){
            $agentsInfo['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $agentsInfo = handleData($agentsInfo);
        //数据验证通过
        $bool = $this->aObj->create($agentsInfo);
        if(!$bool){
            Log::info('用户: '.$agentsInfo['users_id'].'----添加招商代理失败-----时间'.$this->Logdate().'----');
            return msg_err(PassportController::NO_STATUS,self::CREATE_STATUS_TURE_NO);
        }
        set_release_num($agentsInfo['users_id']);
        return msg_ok(PassportController::YSE_STATUS,self::CREATE_STATUS_TURE_YES,$this->obj());
    }
    /**
     * 我发布的招商代理
     */
    public function getMyAgents()
    {
        $agentsInfo = request()->all();
        $info = $this->obj();
        if (isset($agentsInfo['id'])) {//取详情--id
            $info = $this->aObj->sel_agents_info($agentsInfo['id']);
            $info = res_time($info);
            $info = isset($info)?$info:$this->obj();
        }else{//取列表1时间先后2审核通过，3。城市范围4。数据条数
            $redis = GetAppToken($agentsInfo['api_token']);
            if($redis['code'] == 200){
                $agentsInfo['users_id'] = $redis['u_id'];
            }else{
                return msg_err($redis['code'],$redis['msg']);
            }
            if (!isset($agentsInfo['f'])) {
                return msg_err(PassportController::NO_STATUS, PassportController::PARAM);
            }
            if(!isset($agentsInfo['now_page'])){
                $agentsInfo['now_page'] = 1;
            }
            //分页数据
            $info->data = $this->aObj->sel_agents_list($agentsInfo);
            //总页码
            $info->last_page = ceil($this->aObj->sel_agents_page($agentsInfo)/10);
            //当前页码
            $info->current_page = intval($agentsInfo['now_page']);
        }
        return msg_ok(PassportController::YSE_STATUS,self::TURE,$info);
    }

    /**
     * 删除我发布的
     */
    public function delMyAgents()
    {
        $agentsInfo = request()->all();
        $redis = GetAppToken($agentsInfo['api_token']);
        if($redis['code'] == 200){
            $agentsInfo['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        if (!isset($agentsInfo['id'])) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $bool = $this->aObj->del_my_data($agentsInfo['id']);
        if(!$bool){
            Log::info('用户: '.$agentsInfo['users_id'].'----删除招商代理失败-----时间'.$this->Logdate().'----');
            return msg_err(PassportController::NO_STATUS,'删除失败',new \stdClass());
        }
        return msg_ok(PassportController::YSE_STATUS,'删除成功',new \stdClass());
    }

}