<?php
/**
 * 论坛数据
 * User: Administrator
 * Date: 2018/11/24
 * Time: 13:24
 */
namespace App\Http\Controllers\Api\Forum;

use App\Http\Controllers\Api\Search\HistorySearchController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\PassportController;
use App\Models\Forum;
use App\Models\Fabulous;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use Validator;
class ForumController extends Controller
{
    /**
     *论坛列表分类数据以及详情
     * 在此常去的论坛有api_token的时候app就传，没有的话，就不传，不记录不登录用户的数据
     */
    public function forumList()
    {
        $data = request()->all();
        $forumObj = new Forum();
        if (isset($data['parent_type'])&&isset($data['id'])&&isset($data['type'])){//详情
            $res = $forumObj->sel_show($data);
            //浏览量（阅读量）+1，redis记录用户常去的论坛
            $bool = $this->browse($data);
            if(!$bool)
            {
                \Log::info('LTID=>'.$data['id'].' not browse +1 as '.$data['users_id']);
            }
        }else{//数据列表
            if (!isset($data['now_page'])) {
                $data['now_page'] = 1;
            }
            $res = $forumObj->sel_list($data);
        }
        return msg_ok(PassportController::YSE_STATUS,'论坛数据',$res);
    }

    /**
     * redis记录用户常去的论坛
     * 浏览量（阅读量）+1，
    */
    protected function browse($data)
    {
        //修改当前数据 $res=DB::table(‘user’)->where(‘id’,’=’,4)->increment(‘user_age’);
        $forum = new Forum();
        $bool = $forum->inc($data);//浏览量（阅读量）+1
        $this->offtenGo($data);
        return $bool;

    }

    /**
     * 常去的论坛记录
     * 数据格式    offten_go_$users_id = array（
     *      $parent_type#$type = 次数
     *      ）
     */
    protected function offtenGo($data)
    {
        if(!isset($data['api_token']))
        {
            return;
        }
        $redis = GetAppToken($data['api_token']);
        if($redis['code'] == 200){
            $data['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $users_id = $data['users_id'];
        $parent_type = $data['parent_type'];
        $type = $data['type'];
        $redis = new Redis();
        $key = 'offten_go_'.$users_id;//key值模型
        //查询redis是否有改key
        $bool = $redis::get($key);
        if(!$bool){
            $value = array(
                $parent_type.'#'.$type=>1
            );
            $bool = $redis::set($key,json_encode($value));
            if(!$bool)
            {
                \Log::info('offten_go=>'.$users_id.' not +1 as  '.$parent_type.'#'.$type);
            }
            return ;
        }
        //redis中有次key
        $usersData = json_decode($bool,true);
        if(!isset($usersData[$parent_type.'#'.$type])){
            $usersData[$parent_type.'#'.$type] = 0;
        }
        $arr_value = $usersData[$parent_type.'#'.$type];
        $usersData[$parent_type.'#'.$type] = $arr_value+1;
        $bool = $redis::set($key,json_encode($usersData));
        if(!$bool)
        {
            \Log::info('offten_go=>'.$users_id.' not +1 as  '.$parent_type.'#'.$type);
        }
        return ;
    }

    /**
     * 获取常去的论坛
     * api_token
     */
    public function getOfftenGo()
    {
        $data = request()->all();
        $key = 'offten_go_';
        if(!isset($data['api_token'])){
            return msg_ok(PassportController::YSE_STATUS,'常去的论坛',array());
        }
        $redis = GetAppToken($data['api_token']);
        if($redis['code'] == 200){
            $data['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $key.=$data['users_id'];
        $redis = new Redis();
        $offten_go_value = $redis::get($key);
        $res = array();
        if(!empty($offten_go_value)){
            $offten_go_value = json_decode($offten_go_value,true);
            arsort($offten_go_value);//数组排序
            $offten_go_value = array_slice($offten_go_value,0,12);
            $i = 0;
            foreach ($offten_go_value as $key=>$value){//key的形式2#3
                    $typeArr = explode('#',$key);//分割字符串
                    $res[$i] = $this->offtenGoData($typeArr);
                    $res[$i]->parent_type=$typeArr[0];
                    $i++;
            }
        }
        return msg_ok(PassportController::YSE_STATUS,'常去的论坛',$res);
    }

    /**
     * 收藏论坛内容接口
     * $redis = new Redis();
      $redis::incr('number');//自增
      $redis::decr('number1');//自减
      $redis::sadd('num1',3);
      $redis::smembers('num1');
     */
    public function collection()
    {
        $data = request()->all();
        $key1 = 'collection_';
        if(empty($data)||
            !isset($data['api_token'])||
            !isset($data['id'])||
            !isset($data['parent_type'])||
            !isset($data['type'])||
            !isset($data['f'])//标示，是收藏还是取消收藏0:取消 1：收藏
        ){
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        if(empty($data['id']))
        {
            return msg_err(PassportController::NO_STATUS,'收藏内容为空');
        }
        $redis = GetAppToken($data['api_token']);
        if($redis['code'] == 200){
            $data['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $key1.=$data['users_id'];
        $key2 = 'collection_type_'.$data['users_id'];//集合key
        $redis = new Redis();
        if($data['f'] == 1)
        {
            $bool1 = $redis::sadd($key1,$data['id']);//收藏的论坛内容
            $bool2 = $redis::sadd($key2,$data['parent_type'].'#'.$data['type']);//收藏论坛二级类型
        }elseif ($data['f'] == 0)
        {
            $bool1 = $redis::srem($key1,$data['id']);//取消收藏
            $bool2 = $redis::srem($key2,$data['parent_type'].'#'.$data['type']);//取消收藏
        }
        if($bool1&&$bool2)//收藏成功
        {
            $res = new \stdClass();
            //数据库论坛内容+1
            $forumObj = new Forum();
            if($data['f'] == 1){
                $message = '收藏成功';
                $bool = $forumObj->collection_inc($data);
                $res->f = 0;
            }elseif ($data['f'] == 0){
                $message = '取消收藏成功';
                $bool = $forumObj->collection_dinc($data);
                $res->f = 1;
            }

            if(!$bool)
            {
                \Log::info('collection=>'.$data['id'].' not collection '.$data['f'].' as '.$data['users_id']);
            }

            return msg_ok(PassportController::YSE_STATUS,$message,$res);
        }
            return msg_ok(PassportController::NO_STATUS,'网络错误',new \stdClass());

    }

    /**
     * 收藏总数接口
     */
    public function collectionSum()
    {
        $data = request()->all();
        $key = 'collection_';
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
        $res = new \stdClass();
        $key.=$data['users_id'];
        $redis = new Redis();
        $res->len = num2tring($redis::scard($key));
        return msg_ok(PassportController::YSE_STATUS,'共收藏论坛内容的总数',$res);
    }

    /**
     * 收藏论坛内容的列表
     */


    /**
     * 我的收藏列表
     * token
     *
     */
    public function myCollection()
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
        $key = 'collection_type_'.$data['users_id'];//集合key
        $redis = new Redis();
        $my_collection_value = $redis::smembers($key);
        $res = array();
        if(!empty($my_collection_value)){
            $i = 0;
            foreach ($my_collection_value as $key=>$value){//key的形式2#3
                $typeArr = explode('#',$value);//分割字符串
                $res[$i] = $this->offtenGoData($typeArr);
                $res[$i]->parent_type=$typeArr[0];
                $i++;
            }
        }

        return msg_ok(PassportController::YSE_STATUS,'我的收藏列表',$res);
    }

    /**
     * 处理常去论坛的数据
     */
    protected function offtenGoData($data)
    {
        $forumObj = new Forum();
        return $forumObj->sel_offten_go($data);
    }

    /**
     * 入口判断该用户是否已经收藏过该论坛文章
     */
    public function collectionYesOrNo($data=false)
    {
        if($data==false){
            $data = request()->all();
        }
        $key1 = 'collection_';
        if(empty($data)||
            !isset($data['id'])||
            !isset($data['parent_type'])||
            !isset($data['type'])
        ){
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        if(!isset($data['api_token']))
        {
            $res = new \stdClass();
            $res->f = 1;
            return msg_ok(PassportController::YSE_STATUS,'无token，未知点赞',$res);
        }
        $redis = GetAppToken($data['api_token']);
        if($redis['code'] == 200){
            $data['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $key1.=$data['users_id'];
        $redis = new Redis();
        $bool = $redis::sismember($key1,$data['id']);
        $res = new \stdClass();
        $res->f = 1;
        if($bool)//redis返回1，说明该用户已经收藏该文章。返回app--0
        {
            $res->f = 0;
            return msg_ok(PassportController::YSE_STATUS,'用户已经收藏过',$res);
        }
        return msg_ok(PassportController::YSE_STATUS,'用户没有收藏过',$res);
    }

    /**
     * 点赞/取消点赞（0取消，1点赞）
     */
    public function fabulous()
    {
        $data = request()->all();
        $key1 = 'fabulous_';
        if(empty($data)||
            !isset($data['api_token'])||
            !isset($data['id'])||
            !isset($data['parent_type'])||
            !isset($data['type'])||
            !isset($data['f'])||//标示，是点赞还是取消点赞0:取消 1：收藏
            !isset($data['fabulous_users_yse_id'])//被点赞人id
        ){
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $redis = GetAppToken($data['api_token']);
        if($redis['code'] == 200){
            $data['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $key1.=$data['users_id'];
        $redis = new Redis();
        if($data['f'] == 1)
        {
            $bool1 = $redis::sadd($key1,$data['id']);//点赞的论坛内容
        }elseif ($data['f'] == 0)
        {
            $bool1 = $redis::srem($key1,$data['id']);//取消点赞
        }

        if($bool1)//点赞成功
        {
            $res = new \stdClass();
            //数据库论坛内容+1
            $forumObj = new Forum();
            if($data['f'] == 1){
                $message = '点赞成功';
                $bool = $forumObj->fabulous_inc($data);
                $res->f = 0;
            }elseif ($data['f'] == 0){
                $message = '取消点赞成功';
                $bool = $forumObj->fabulous_dinc($data);
                $res->f = 1;
            }
            if($bool) {//记录点赞
                $this->fabulousW($data);
            } else if(!$bool) {
                \Log::info('fabulous=>'.$data['id'].' not fabulous '.$data['f'].' as '.$data['users_id']);
            }

            return msg_ok(PassportController::YSE_STATUS,$message,$res);
        }
        return msg_ok(PassportController::NO_STATUS,'网络错误',new \stdClass());
    }

    /**
     * 入口判断该用户是否已经点赞过该论坛文章
     */
    public function fabulousYesOrNo($data=false){
        if($data==false){
            $data = request()->all();
        }
        $key1 = 'fabulous_';
        if(empty($data)||
            !isset($data['id'])||
            !isset($data['parent_type'])||
            !isset($data['type'])
        ){
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        if(!isset($data['api_token']))
        {
            $res = new \stdClass();
            $res->f = 1;//标示，是点赞还是取消点赞0:取消 1：收藏
            return msg_ok(PassportController::YSE_STATUS,'无token，未知点赞',$res);
        }
        $redis = GetAppToken($data['api_token']);
        if($redis['code'] == 200){
            $data['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $key1.=$data['users_id'];
        $redis = new Redis();
        $bool = $redis::sismember($key1,$data['id']);
        $res = new \stdClass();
        $res->f = 1;
        if($bool)//redis返回1，说明该用户已经收藏该文章。返回app--0
        {
            $res->f = 0;
            return msg_ok(PassportController::YSE_STATUS,'用户已经点赞过',$res);
        }
        return msg_ok(PassportController::YSE_STATUS,'用户没有点赞过',$res);
    }

    protected function fabulousW($data)
    {
        $fabulousObj = new Fabulous();
        $info['fabulous_users_id'] = $data['users_id'];
        $info['fabulous_users_yse_id'] = $data['fabulous_users_yse_id'];
        $info['article_id'] = $data['id'];
        $info['status'] = $data['f'];
        $bool = $fabulousObj->create($info);
        return $bool;
    }

    /**
     * 随机论坛数据
     */
    public function randomForum()
    {
        $forumM = new Forum();
        //查论坛表中共有多少数据
        $data = $forumM->f_count();
        $num = $this->num(count($data)-1);
        sort($num);
        $d = request()->all();
        if(!isset($d['api_token'])||$d['api_token']==''||$d['api_token']==NULL){
            $d['api_token'] = false;
        }
        foreach ($num as $k=>$v)
        {
            $str[] = $data[$v]->id;
        }
        $res = $forumM->random_list(['id'=>$str,'api_token'=>$d['api_token']]);
        return msg_ok(PassportController::YSE_STATUS,'随机论坛数据',$res);
    }

    public function num($count)
    {
        $id = array();
        for ($i=0;$i<5;$i++){
            $n=mt_rand(0,$count);
            if(in_array($n,$id)){
                $n=mt_rand(0,$count);
            }
            $id[]=$n;
        }
        return $id;
    }

    /**
     * 论坛search
     */
    public function searchForum()
    {
        $arr = request()->all();
        $forumM = new Forum();
        if(!isset($arr['now_page'])){
            $arr['now_page'] = 1;
        }
        if(!isset($arr['status'])||$arr['status']!=1)
        {
            return msg_err(PassportController::NO_STATUS,'参数错误或缺少参数');
        }
        if(isset($arr['api_token'])){
            $redis = GetAppToken($arr['api_token']);
            if($redis['code'] == 200){
                $arr['users_id'] = $redis['u_id'];
            }else{
                return msg_err($redis['code'],$redis['msg']);
            }
        }
        $historySM = new HistorySearchController();
        $hData = $historySM->data($arr);
        switch ($hData){
            case 101014:
                break;
            case 101013:
                $objectD = $forumM->search_datas($arr);
                $arr['values'] = objectA($objectD);
                $historySM->insertHistory($arr);
                break;
            case 200:
                $unserializeD = unserialize($historySM->redisData($arr));
                $d = dataPage(['data'=>$unserializeD,'page'=>$arr['now_page']]);
                return msg_ok(PassportController::YSE_STATUS,'搜索缓存数据',$d);//数组做分段
                break;
            default:
                return msg_err(PassportController::NO_STATUS,'网络错误');
        }

        $res['data'] = $forumM->search_data($arr);
        $res['last_page'] = ceil($forumM->search_count(isset($arr['key'])?$arr['key']:'')/10);
        //当前页码
        $res['current_page'] = intval($arr['now_page']);
        return msg_ok(PassportController::YSE_STATUS,'论坛搜索数据',$res);
    }

    /*分享接口  xx*/
    public function forums_share(Request $request){
        $validator = Validator::make($request->all(),
            [
                'id' => 'required|numeric',

            ],[],[
                'id' => '论坛',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,'异常参数');
        }
        $id = $request->get('id');
        $info = Forum::where('id',$id)->select('id','title','content','photo')->first();

        if($info){
            $info = $info->toArray();
            $info['photo'] = json_decode_photo($info['photo'])[0];
            $info['link'] = getenv('APP_URL')."/forums_detail/".$id;
            return msg_ok(200,'success',$info);
        }else{
            return msg_ok(401,'数据不存在');
        }


    }
}