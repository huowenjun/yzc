<?php
/**
 * 评论
 * User: hwj
 * Date: 2018/11/29
 * Time: 9:17
 */
namespace App\Http\Controllers\Api\Forum;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\PassportController;
use App\Models\FabulouComments;
use App\Models\Forum;
use App\Models\OneComments;
use App\Models\TwoComments;


class CommentController extends Controller
{
    public $forumoObj;
    public function __construct()
    {
        $this->forumoObj = new Forum();
    }

    /**
     * 一级评论提交
     * 发表一次评论，论坛内容回帖数+1
     * 发表评论需要登录
     * token
     */
    public function oneComment()
    {
        $commentInfo = request()->all();
        if (empty($commentInfo) ||
            !isset($commentInfo['forum_id']) ||
            !isset($commentInfo['api_token'])||
            !isset($commentInfo['content'])
        ) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $redis = GetAppToken($commentInfo['api_token']);
        if($redis['code'] == 200){
            $commentInfo['users_id'] = $redis['u_id'];
            unset($commentInfo['api_token']);
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $commentInfo['content'] = Sensitive($commentInfo['content']);
        $one_comments_id = OneComments::insertGetId($commentInfo);
        if ($one_comments_id) {
            $forumBool = $this->forumoObj->replies_inc($commentInfo['forum_id']);
            if($forumBool)
            {
                $commentInfo['id'] = $one_comments_id;
                return msg_ok(PassportController::YSE_STATUS,'评论发布成功',$commentInfo);
            }
        }else{
            return msg_err(PassportController::NO_STATUS,'评论发布失败',$commentInfo);
        }

    }

    /**
     * 二级评论提交(针对于一级评论)
     * 发表一次评论，论坛内容回帖数+1
     * 回复评论需要登录
     */
    public function twoComment()
    {
        $commentInfo = request()->all();
        if (empty($commentInfo) ||
            !isset($commentInfo['parent_id']) ||
            !isset($commentInfo['api_token'])||
            !isset($commentInfo['content'])||
            !isset($commentInfo['forum_id'])||
            !isset($commentInfo['one_users_id'])//一级评论发布者id
        ) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $redis = GetAppToken($commentInfo['api_token']);
        if($redis['code'] == 200){
            $commentInfo['users_id'] = $redis['u_id'];
            unset($commentInfo['api_token']);
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        if($commentInfo['users_id']==$commentInfo['one_users_id'])
        {
            return msg_err(PassportController::NO_STATUS,'不可以回复自己的评论');

        }
        $commentInfo['content'] = Sensitive($commentInfo['content']);
        $commentInfo['updated_at'] = date('Y-m-d H:i:d');
        $commentId = TwoComments::insertGetId($commentInfo);
        if ($commentId) {
            $forumBool = $this->forumoObj->replies_inc($commentInfo['forum_id']);
            if($forumBool)
            {
                $commentInfo['id'] = $commentId;
                return msg_ok(PassportController::YSE_STATUS,'评论回复成功',$commentInfo);
            }
        }else{
            return msg_err(PassportController::NO_STATUS,'评论回复失败',$commentInfo);
        }
    }

    /**
     * 展示评论
     *
     */
    public function showComment($commentInfo=false)
    {
        //一级评论（评论人头像，name，评论内容，时间，点赞数量）=》2级评论（name，内容，时间）
        //查一级评论（论坛内容id，）
        if($commentInfo==false){
            $commentInfo = request()->all();
        }
        if (empty($commentInfo) ||
            !isset($commentInfo['forum_id'])
        ) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        if(isset($commentInfo['api_token'])&&
            $commentInfo['api_token']!=''
        ){
            $redis = GetAppToken($commentInfo['api_token']);
            if($redis['code'] == 200){
                $commentInfo['users_id'] = $redis['u_id'];
                unset($commentInfo['api_token']);
            }else{
                return msg_err($redis['code'],$redis['msg']);
            }
        }else{
            $commentInfo['users_id'] = 0;
        }

        $oneCommentM = new OneComments();
        $res = new \stdClass();
        if(!isset($commentInfo['now_page'])){
            $commentInfo['now_page'] = 1;
        }
        $res->info = $oneCommentM->sel_data($commentInfo);//一级评论数据(10条)
        $res->last_page = ceil($oneCommentM->sel_con($commentInfo)/3);//一级评论数据(总页码)
        $res->current_page = intval($commentInfo['now_page']);//一级评论数据(当前页码)
        //过滤出一级评论中的id，去二级评论表中去查数据
        $twoComment = new TwoComments();
        foreach ($res->info as $k=>$v)
        {
            if($commentInfo['users_id'] != 0){
                $data = $this->yse_no_fabulou(['users_id'=>$commentInfo['users_id'],'one_comment_id'=>$res->info[$k]->one_id]);
                if($data){
                    $res->info[$k]->yse_no_fabulous = $data->yse_no_fabulou;
                }else{

                    $res->info[$k]->yse_no_fabulous = 0;
                }
            }else{
                $res->info[$k]->yse_no_fabulous = 0;
            }
            $twoRes = new \stdClass();
            $parent_id = $res->info[$k]->one_id;
            $twoRes->info = $twoComment->sel_data(['parent_id'=>$parent_id,'now_page'=>1,'size'=>3]); //二级评论数据(3条)
            $twoRes->count = $twoComment->sel_con(['parent_id'=>$parent_id]);//二级评论总条数
            $twoRes->last_page = ceil($twoComment->sel_con(['parent_id'=>$parent_id])/3);//一级评论数据(总页码)
            $twoRes->current_page = 1;//二级评论数据(当前页码)
            $res->info[$k]->two_comment = $twoRes;
        }
        return msg_ok(PassportController::YSE_STATUS,'论坛评论',$res);

    }

    protected function yse_no_fabulou($commentInfo)
    {
        $fabulouM = new FabulouComments();
        $data = $fabulouM->sel_yse_no_fabulou($commentInfo);
        return $data;
    }

    /**
     * 评论点赞/取消赞提交
     * apitoken,
     */
    public function setFabulou()
    {
        $fabulouInfo = request()->all();
        if (empty($fabulouInfo) ||
            !isset($fabulouInfo['api_token'])||
            !isset($fabulouInfo['one_comment_id'])||
            !isset($fabulouInfo['yse_no_fabulou'])//0取消，1点赞
        ) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $redis = GetAppToken($fabulouInfo['api_token']);
        if($redis['code'] == 200){
            $fabulouInfo['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        //查当前用户是否对该条评论已经点过赞
        $fabulouCommentM = new FabulouComments();
        $bool = $fabulouCommentM->sel_data($fabulouInfo);
        $oneCommmentM = new OneComments();
        if($bool)//修改数据
        {
            $bool = $fabulouCommentM->up_data($fabulouInfo);
            if(!$bool){
                return msg_err(PassportController::NO_STATUS,'点赞失败');
            }
            if($fabulouInfo['yse_no_fabulou'] == 1){
                $oneCommmentM->fabulous_inc($fabulouInfo);
                $msg = '点赞成功';
            }else{
                $oneCommmentM->fabulous_dinc($fabulouInfo);
                $msg = '取消点赞成功';
            }
            return msg_ok(PassportController::YSE_STATUS,$msg,new \stdClass());
        }
        //添加数据
        $bool = $fabulouCommentM->create($fabulouInfo);
        if(!$bool)
        {
            return msg_err(PassportController::NO_STATUS,'点赞失败');
        }
        $oneCommmentM->fabulous_inc($fabulouInfo);
        return msg_ok(PassportController::YSE_STATUS,'点赞成功',new \stdClass());

    }

    /**
     * 回帖总数量展示
     */
    public function Sumreplie($sumrepliesInfo=false)
    {
//        $sumrepliesInfo = request()->all();
        if (!isset($sumrepliesInfo['forum_id'])) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $forumM = new Forum();
        $num = $forumM->replies_num($sumrepliesInfo);
        $num->replies_num = num2tring($num->replies_num);
        return $num;
    }

    /**
     * 二级评论分页
     */
    public function twoPageComment()
    {
        $twoCommentInfo = request()->all();
        $twoComment = new TwoComments();
        if (empty($twoCommentInfo) ||
            !isset($twoCommentInfo['parent_id'])
        ) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        if(!isset($twoCommentInfo['now_page'])){
            $twoCommentInfo['now_page'] = 1;
        }
        if (!isset($twoCommentInfo['size'])) {
            $twoCommentInfo['size'] = 3;
        }
        $twoRes = new \stdClass();
        $twoRes->info = $twoComment->sel_data($twoCommentInfo); //二级评论数据(3条)
        $twoRes->last_page = ceil($twoComment->sel_con($twoCommentInfo)/$twoCommentInfo['size']);//一级评论数据(总页码)
        $twoRes->current_page =  $twoCommentInfo['now_page'];//二级评论数据(当前页码)

        return msg_ok(PassportController::YSE_STATUS,'二级评论',$twoRes);
    }

    /**
     *未读消息--评论总条数
     */
    public function noReadComment()
    {
        $noReadCommentInfo = request()->all();
        $redis = GetAppToken($noReadCommentInfo['api_token']);
        if($redis['code'] == 200){
            $noReadCommentInfo['one_users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $noReadCommentM = new TwoComments();
        $res = new \stdClass();
        $num = $noReadCommentM->count_no_read($noReadCommentInfo);
        $res->num = $num;
        return msg_ok(PassportController::YSE_STATUS,'评论未读数量',$res);
    }

    /**
     * 查看未读评论数据
     */
    public function seeNoReadComment()
    {
        $noReadCommentInfo = request()->all();
        $redis = GetAppToken($noReadCommentInfo['api_token']);
        if($redis['code'] == 200){
            $noReadCommentInfo['one_users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        if(!isset($noReadCommentInfo['now_page'])){
            $noReadCommentInfo['now_page'] = 1;
        }
        $noReadCommentM = new TwoComments();
        $res = new \stdClass();
        $res->info = $noReadCommentM->no_sel_data($noReadCommentInfo); //二级评论数据(5条)未读
        $res->last_page = ceil($noReadCommentM->no_sel_con($noReadCommentInfo)/5);//一级评论数据(总页码)
        $res->current_page =  $noReadCommentInfo['now_page'];//二级评论数据(当前页码)
        return msg_ok(PassportController::YSE_STATUS,'查看未读消息--评论列表',$res);
    }

    /**
     * 一级评论详情
     * @return \Illuminate\Http\JsonResponse
     */
    public function oneCommentInfo(){
        $all = request()->all();
        if(!isset($all['id'])){
            return msg_err(PassportController::NO_STATUS,'缺少参数');
        }
        $oneCommentM = new OneComments();
        $res = $oneCommentM->info($all);
        if(isset($all['api_token'])&&$all['api_token']!='')
        {
            $redis = GetAppToken($all['api_token']);
            if($redis['code'] == 200){
                $all['users_id'] = $redis['u_id'];
            }else{
                return msg_err($redis['code'],$redis['msg']);
            }
        }else{
            $all['users_id'] = 0;
        }
        if($all['users_id'] != 0){
            $data = $this->yse_no_fabulou(['users_id'=>$all['users_id'],'one_comment_id'=>$res->one_id]);
            if($data){
                $res->yse_no_fabulous = $data->yse_no_fabulou;
            }else{

                $res->yse_no_fabulous = 0;
            }
        }else{
            $res->yse_no_fabulous = 0;
        }
        if(!$res){
            return msg_err(PassportController::NO_STATUS,'无数据');
        }
        return msg_ok(PassportController::YSE_STATUS,'一级评论详情',$res);
    }

}