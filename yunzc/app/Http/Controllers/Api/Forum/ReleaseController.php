<?php
/**
 * 用户发布论坛
 * User: Administrator
 * Date: 2018/11/23
 * Time: 15:39
 */
namespace App\Http\Controllers\Api\Forum;

use App\Http\Controllers\Api\PassportController;
use App\Http\Controllers\Controller;
use App\Models\Forum;


class ReleaseController extends Controller
{
    const CREATE_STATUS_TURE_YES = '发帖成功';
    const CREATE_STATUS_TURE_NO = '发帖失败';
    const TURE = '查询成功';
    public static function obj()
    {
        return new \stdClass();
    }

    /**
     * 发布论坛
     * @return \Illuminate\Http\JsonResponse
     */
    public function releaseForum()
    {
        $forumObj = new Forum();
        $forumInfo = request()->all();
        if (empty($forumInfo) ||
            !isset($forumInfo['title']) ||
            !isset($forumInfo['content'])||
            !isset($forumInfo['parent_type'])||
            !isset($forumInfo['type'])||
            !isset($forumInfo['photo'])||
            !isset($forumInfo['room_city'])
        ) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $redis = GetAppToken($forumInfo['api_token']);
        if($redis['code'] == 200){
            $forumInfo['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $forumInfo = handleData($forumInfo);
        //数据验证通过
        $bool = $forumObj->create($forumInfo);
        if(!$bool){
            return msg_err(PassportController::NO_STATUS,self::CREATE_STATUS_TURE_NO);
        }
        return msg_ok(PassportController::YSE_STATUS,self::CREATE_STATUS_TURE_YES,$this->obj());
    }

    /**
     * 管理论坛--用户论坛列表
     * 查询关于uid的所有发表过的且没有被删除的论坛
     */
    public function manageForum()
    {
        $foeumObj = new Forum();
        $data = request()->all();
        if(empty($data)){
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $res = $this->obj();
        if (isset($data['id'])) {//取详情--id
            $res = $foeumObj->sel_foeum_info($data['id']);
        }else{
            //从token中获取uid
            $redis = GetAppToken($data['api_token']);
            if ($redis['code'] == 200) {
                $data['users_id'] = $redis['u_id'];
            } else {
                return msg_err($redis['code'], $redis['msg']);
            }
            if (!isset($data['now_page'])) {
                $data['now_page'] = 1;
            }
            //根据uid在论坛表中找到关于uid的数据（条件uid）
            $res->data = $foeumObj->sel_manage($data);//总条数
            $res->last_page = ceil($foeumObj->sel_page($data) / 10);//总页码
            $res->current_page = $data['now_page'];//当前页
        }
        return msg_ok(PassportController::YSE_STATUS,'用户管理论坛',$res);
    }


    /**
     * 管理论坛--用户删除列表
     * 根据id删除
     */
    public function delForum()
    {
       $forumObj = new Forum();
        $data = request()->all();
        if(empty($data) || !isset($data['id']) || !isset($data['api_token'])){
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        //从token中获取uid
        $redis = GetAppToken($data['api_token']);
        if($redis['code'] == 200){
            $data['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
       $bool = $forumObj->del_data($data);
        if($bool == 1){
            return msg_ok(PassportController::YSE_STATUS,'删除成功',new \stdClass());
        }else{
            return msg_err(PassportController::YSE_STATUS,'删除失败',new \stdClass());
        }
    }
}