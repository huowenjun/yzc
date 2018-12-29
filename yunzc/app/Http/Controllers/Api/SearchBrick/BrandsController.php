<?php
/**
 * 点击品牌列表，进入产品中心.
 * User: 霍文俊
 * Date: 2018/12/3
 * Time: 13:24
 */
namespace App\Http\Controllers\Api\SearchBrick;

use App\Http\Controllers\Api\Search\HistorySearchController;
use App\Http\Controllers\Api\PassportController;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\BrandEvaluates;
use App\Models\EvaluateLogs;
use App\Models\Tile;
use App\Models\Users;
use Illuminate\Support\Facades\Redis;

class BrandsController extends Controller
{
    public $obj;
    public $brand_evaluate;
    public $evaluate_log;
    public $tiles;
    public function __construct()
    {
        $this->obj = new Brand();
        $this->brand_evaluate = new BrandEvaluates();
        $this->evaluate_log = new EvaluateLogs();
        $this->tiles = new Tile();
    }

    /**
     * 展示品牌详情---基本信息
     */
    public function brandInfo()
    {
        $data = request()->all();
        if(!isset($data['brand_id'])){
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $res = $this->obj->sel_info($data['brand_id']);
        if(isset($res)){
            $res->avg_evaluate = $this->avgEvaluate($data)->original['data']->sum;
        }else{
            $res = new \stdClass();
        }
        return msg_ok(PassportController::YSE_STATUS,'品牌基本信息',$res);
    }

    /**
     * 品牌介绍
     */
    public function brandIntroduce()
    {
        $data = request()->all();
        if(!isset($data['brand_id'])){
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $res = $this->obj->sel_introduce($data['brand_id']);
        return msg_ok(PassportController::YSE_STATUS,'品牌介绍',$res);
    }

    /**
     * 品牌用户评价
     */
    public function brandEvaluate()
    {
        $data = request()->all();
        if(!isset($data['brand_id'])){
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        if(!isset($data['now_page'])){
            $data['now_page'] = 1;
        }
        $data['page'] = ($data['now_page']-1)*10;
        $res = new \stdClass();
        $res->list = $this->brand_evaluate
            ->select(['name','img','content','grade','brand_id','photo','brand_evaluates.created_at'])
            ->join('users','users.id','=','brand_evaluates.users_id')
            ->where(['brand_id'=>$data['brand_id']])
            ->orderBy('brand_evaluates.created_at','desc')
            ->offset($data['page'])
            ->limit(10)
            ->get();
        $res->last_page = ceil(($this->brand_evaluate
            ->where(['brand_id'=>$data['brand_id']])
            ->count())/10);
        $res->current_page = intval($data['now_page']);
        return msg_ok(PassportController::YSE_STATUS,'品牌评论数据',$res);
    }
    /**
     * 品牌总体评价
     */
    public function avgEvaluate($data){
        $res = new \stdClass();
        $sum = $this->evaluate_log
            ->where(['brand_id'=>$data['brand_id']])
            ->avg('grade');
        $res->sum =  sprintf("%.1f",$sum);
        return msg_ok(PassportController::YSE_STATUS,'总体评价',$res);
    }

    /**
     * 品牌用户评价提交
     */
    public function setBrandEvaluate()
    {
        $data = request()->all();
        if (empty($data) ||
            !array_key_exists('content',$data) ||
            !array_key_exists('grade',$data)||
            !array_key_exists('brand_id',$data)||
            !array_key_exists('api_token',$data)||
            !array_key_exists('photo',$data)
        ) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $redis = GetAppToken($data['api_token']);
        if($redis['code'] == 200){
            $data['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        unset($data['api_token']);
        $redis = new Redis();
        $redis::incr($data['users_id'].'_b');
        $bool1 = $this->brand_evaluate->create($data);
        $bool2 = $this->evaluate_log->create(['brand_id'=>$data['brand_id'],'grade'=>$data['grade']]);
        if($bool1&&$bool2){
            return msg_ok(PassportController::YSE_STATUS,'添加成功',$data);
        }
        return msg_err(PassportController::NO_STATUS,'添加失败');
    }

    /**
     *品牌下产品中心数据
     */
    public function getProductData()
    {
        $data = request()->all();
        if(!isset($data['brand_id'])){
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        if(!isset($data['now_page'])){
            $data['now_page'] = 1;
        }
        $data['page'] = ($data['now_page']-1)*9;
        $where = ['brand_id'=>$data['brand_id']];
        $res = new \stdClass();
        $res->list = Tile::select(['id','brand_id','img','name'])
            ->where($where)
            ->orderBy('updated_at','desc')
            ->offset($data['page'])
            ->limit(10)
            ->get();
        $res->last_page = ceil((Tile::where($where)->count())/9);
        $res->current_page = intval($data['now_page']);
        return msg_ok(PassportController::YSE_STATUS,'产品中心数据',$res);
    }

    /**
     * 产品搜索1论坛2瓷砖3拼单
     */
    public function searchEvaluate()
    {
        $arr = request()->all();
        if(!isset($arr['now_page'])){
            $arr['now_page'] = 1;
        }
        if(!isset($arr['status'])||$arr['status']!=2)
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
                $objectD = $this->tiles->search_datas($arr);
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
        $res['data'] = $this->tiles->search_data($arr);
        $res['last_page'] = ceil($this->tiles->search_count(isset($arr['key'])?$arr['key']:'')/10);
        //当前页码
        $res['current_page'] = intval($arr['now_page']);
        return msg_ok(PassportController::YSE_STATUS,'瓷砖搜索数据',$res);
    }

    /**
     * 产品回复(产品评论)
     */
//    public function setProductEvaluate(){
//        $data = request()->all();
//        if (empty($data) ||
//            !array_key_exists('content',$data) ||
//            !array_key_exists('tile_id',$data)||
//            !array_key_exists('api_token',$data)||
//            !array_key_exists('photo',$data)
//        ) {
//            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
//        }
//        $redis = GetAppToken($data['api_token']);
//        if($redis['code'] == 200){
//            $data['users_id'] = $redis['u_id'];
//        }else{
//            return msg_err($redis['code'],$redis['msg']);
//        }
//        unset($data['api_token']);
//        $bool = $this->brand_evaluate->create($data);
//        if($bool){
//            $redis = new Redis();
//            $redis::incr($data['users_id'].'_t');
//            return msg_ok(PassportController::YSE_STATUS,'添加成功',$data);
//        }
//        return msg_err(PassportController::NO_STATUS,'添加失败');
//    }
    /**
     * 我的评价总数
     */
    public function sumP()
    {
        $data = request()->all();
        if (empty($data) ||
            !array_key_exists('api_token',$data)
        ) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $redis = GetAppToken($data['api_token']);
        if($redis['code'] == 200){
            $data['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $redis = new Redis();
        $key = $data['users_id'].'_b';
        return msg_ok(PassportController::YSE_STATUS,'我的评价总数',['sum'=>$redis::get($key)]);
    }

    /**
     * 我的评论列表
     */
    public function myP()
    {
        $data = request()->all();
        if (empty($data) ||
            !array_key_exists('api_token',$data)||
            !isset($data['brand_id'])
        ) {
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
        $data['page'] = ($data['now_page']-1)*10;
        $res['list'] = $this->brand_evaluate
            ->select(['name','img','content','grade','brand_id','photo','brand_evaluates.created_at'])
            ->join('users','users.id','=','brand_evaluates.users_id')
            ->where(['users_id'=>$data['users_id']])
            ->where(['brand_id'=>$data['brand_id']])
            ->orderBy('brand_evaluates.created_at','desc')
            ->offset($data['page'])
            ->limit(10)
            ->get();
        $res['last_page'] = ceil(($this->brand_evaluate
                ->where(['users_id'=>$data['users_id']])
                ->where(['brand_id'=>$data['brand_id']])
                ->count())/10);
        $res['current_page'] = intval($data['now_page']);
        return msg_ok(PassportController::YSE_STATUS,'产品评论数据',$res);
    }

    public function myB()
    {
        $data = request()->all();
        if (empty($data) ||
            !array_key_exists('api_token',$data)
        ) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $redis = GetAppToken($data['api_token']);
        if($redis['code'] == 200){
            $data['users_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $redis = new Redis();
        $redis::del($data['users_id'].'_t');
        $list = $this->brand_evaluate
            ->select(['brands.id','brands.name','brands.name','brands.trade_mark','brands.initial'])
            ->distinct()
            ->join('brands','brands.id','=','brand_evaluates.brand_id')
            ->where(['users_id'=>$data['users_id']])
            ->orderBy('brands.initial')
            ->get();
        foreach ($list as &$v) {
            $v->initial = strtoupper($v->initial);
        }
        return msg_ok(PassportController::YSE_STATUS,'产品评论数据',$list);
    }
}