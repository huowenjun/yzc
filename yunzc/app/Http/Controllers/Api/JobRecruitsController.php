<?php
/**
 *  求职招聘
 * User: Administrator
 * Date: 2018/11/26
 * Time: 9:56
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobRecruits;



class JobRecruitsController extends Controller
{
    const CREATE_STATUS_TURE_YES = '添加成功';
    const CREATE_STATUS_TURE_NO = '添加失败';
    const TURE = '查询成功';

    public static function obj()
    {
        return new \stdClass();
    }
    public function getJobRecruits()
    {
        $jobRecruitsObj = new JobRecruits();
        $jobRecruitsInfo = request()->all();
        $info = $this->obj();
        if (isset($jobRecruitsInfo['id'])) {//取详情--id
            $info = $jobRecruitsObj->sel_job_recruits_info($jobRecruitsInfo['id']);
            $info = res_time($info);
        }else{//取列表1时间先后2审核通过，3。城市范围4。数据条数
            if(empty($jobRecruitsInfo) ||
                !array_key_exists('type',$jobRecruitsInfo)){
                return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
            }
            //默认10条+默认页码1
            if(!isset($jobRecruitsInfo['now_page'])){
                $jobRecruitsInfo['now_page'] = 1;
            }
            $info->data = $jobRecruitsObj->sel_job_recruits_list($jobRecruitsInfo);
            $info->last_page = ceil($jobRecruitsObj->sel_job_recruits_page($jobRecruitsInfo)/10);
            $info->current_page = intval($jobRecruitsInfo['now_page']);
        }
        return msg_ok(PassportController::YSE_STATUS,self::TURE,$info);
    }
    public function setJobRecruits()
    {
        $jobRecruitsObj = new JobRecruits();
        $jobRecruitsInfo = request()->all();
        if (empty($jobRecruitsInfo) ||
            !array_key_exists('type',$jobRecruitsInfo) ||
            !array_key_exists('position',$jobRecruitsInfo)||
            !array_key_exists('education',$jobRecruitsInfo)||
            !array_key_exists('experience',$jobRecruitsInfo)||
            !array_key_exists('experience_info',$jobRecruitsInfo)||
            !array_key_exists('salary',$jobRecruitsInfo)||
            !array_key_exists('station_time',$jobRecruitsInfo)||
            !array_key_exists('sketch',$jobRecruitsInfo)||
            !array_key_exists('contacts_name',$jobRecruitsInfo)||
            !array_key_exists('contacts_tel',$jobRecruitsInfo)||
            !array_key_exists('photo',$jobRecruitsInfo)||
            !array_key_exists('api_token',$jobRecruitsInfo)
        ) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        if(!tel_preg($jobRecruitsInfo['contacts_tel'])){
            return msg_err(PassportController::NO_STATUS,'手机号格式不正确');
        }
        $redis = GetAppToken($jobRecruitsInfo['api_token']);
        if($redis['code'] == 200){
            $jobRecruitsInfo['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $jobRecruitsInfo = handleData($jobRecruitsInfo);
        //数据验证通过
        $bool = $jobRecruitsObj->create($jobRecruitsInfo);
        if(!$bool){
            return msg_err(PassportController::NO_STATUS,self::CREATE_STATUS_TURE_NO);
        }
        set_release_num($jobRecruitsInfo['users_id']);
        return msg_ok(PassportController::YSE_STATUS,self::CREATE_STATUS_TURE_YES,$this->obj());
    }
    /**
     * 获取我发布的
     */
    public function getMyJobRecruits()
    {
        $jobRecruitsObj = new JobRecruits();
        $jobRecruitsInfo = request()->all();
        $info = $this->obj();
        if (isset($jobRecruitsInfo['id'])) {//取详情--id
            $info = $jobRecruitsObj->sel_job_recruits_info($jobRecruitsInfo['id']);
            $info = res_time($info);
        }else{//取列表1时间先后2审核通过，3。城市范围4。数据条数
            $redis = GetAppToken($jobRecruitsInfo['api_token']);
            if($redis['code'] == 200){
                $jobRecruitsInfo['users_id'] = $redis['u_id'];
            }else{
                return msg_err($redis['code'],$redis['msg']);
            }
            if(!array_key_exists('f',$jobRecruitsInfo)){
                return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
            }
            //默认10条+默认页码1
            if(!isset($jobRecruitsInfo['now_page'])){
                $jobRecruitsInfo['now_page'] = 1;
            }
            $info->data = $jobRecruitsObj->sel_job_recruits_list($jobRecruitsInfo);
            $info->last_page = ceil($jobRecruitsObj->sel_job_recruits_page($jobRecruitsInfo)/10);
            $info->current_page = intval($jobRecruitsInfo['now_page']);
        }
        return msg_ok(PassportController::YSE_STATUS,self::TURE,$info);
    }

    /**
     * 删除
     */
    public function delMyJobRecruits()
    {
        $jobRecruitsObj = new JobRecruits();
        $jobRecruitsInfo = request()->all();
        $redis = GetAppToken($jobRecruitsInfo['api_token']);
        if($redis['code'] == 200){
            $jobRecruitsInfo['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        if (!array_key_exists('id',$jobRecruitsInfo)) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $bool = $jobRecruitsObj->del_my_data($jobRecruitsInfo['id']);
        if(!$bool){
            return msg_err(PassportController::NO_STATUS,'删除失败',new \stdClass());
        }
        return msg_ok(PassportController::YSE_STATUS,'删除成功',new \stdClass());
    }
}