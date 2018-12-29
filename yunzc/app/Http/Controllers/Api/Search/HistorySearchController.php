<?php
/**
 * 搜索历史1论坛2瓷砖3拼单
 * User: 霍文俊
 * Date: 2018/12/5
 * Time: 9:57
 */
namespace App\Http\Controllers\Api\Search;

use App\Http\Controllers\Api\PassportController;
use App\Http\Controllers\Controller;
use App\Models\HistoryNo;
use App\Models\HistoryYes;
use Illuminate\Support\Facades\Redis;

class HistorySearchController extends Controller
{
    const M = 1*24*60*60;//5天  5*24*60*60
    public $history_no;
    public $history_yes;
    public function __construct()
    {
        $this->history_no = new HistoryNo();
        $this->history_yes = new HistoryYes();
    }

    /**
     * 历史词汇
     */
    public function historyWord()
    {
        //搜索标志**1论坛2瓷砖3拼单
        $data = request()->all();
        $obj = $this->history_no;
        $msg = '非登录历史搜索词';
        if(!isset($data['status']))
        {
            return msg_err(PassportController::NO_STATUS,'缺少参数');
        }
        if(isset($data['api_token'])&&$data['api_token']!='')
        {
            $redis = GetAppToken($data['api_token']);
            if($redis['code'] == 200){
                $data['users_id'] = $redis['u_id'];
            }else{
                return msg_err($redis['code'],$redis['msg']);
            }
            $obj = $this->history_yes;
            $msg = '登录历史搜索词';
        }
        switch ($data['status'])
        {
            case 1://论坛历史词
                $res = $obj->sel_data($data);
                break;
            case 2://瓷砖历史词
                $res = $obj->sel_data($data);
                break;
            case 3://拼单历史词
                $res = $obj->sel_data($data);
                break;
            default:
                return msg_err(PassportController::NO_STATUS,'参数错误');
        }

        return msg_ok(PassportController::YSE_STATUS,$msg,$res);



    }

    /**
     * 搜索返回值
     */
    public function data($data=false)
    {
        if(!isset($data['key']))
        {
            return 101010;//没有搜索关键词
        }
        //读取redis中是否有这个关键词
        //$data = request()->all();
        $res = $this->redisD($data);
        switch ($res->original['code']){
            case 101011:
                //return 101011;//'无数据，redis添加key,数据不在此服务返回'
            case 101014:
                return 101014;//搜索次数不够10次，数据不在此服务返回
                break;
            case 101012:
//                return 101012;//数据过去时间久，从新添加新数据,数据不在此服务返回(调用接口，数据同步redis和小表)
//                break;
            case 101013:
                return 101013;//搜索次数够10次，同步redis数据和小表数据，数据不在此服务返回(调用接口，数据同步redis和小表)
                break;
            case 200:
                return 200;
//                return json_decode($res->original['data']);
                break;
            default:
                return msg_ok(PassportController::NO_STATUS,'网络异常');
                break;

        }

    }

    /**
     * redis
     */
    protected function redisD($data=false)
    {
        $keys = $data['key'].'_'.$data['status'];
        $et = 11;
        if(isset($data['users_id'])){
            $keys = $data['key'].'_'.$data['status'].'_'.$data['users_id'];
            $et = 10;
        }
        $redis = new Redis();
        $value = $redis::get($keys);
        if(!$value)//数据为空，可能过期，查训小表，如果小表中有数据，查看小表的时间是否超过过期时间，未过期，返回数据，过期，查询数据，回调redisD，数据入库，返回数据
        {
            $res = $historyD = $this->historyD($data);//查询数据
            if(!$res){//无数据
                $redis::incr($keys);
                $redis::expire($keys,self::M);
                //查询数据库
               return msg_err(101011,'无数据，redis添加key,数据不在此服务返回');
            }
            $now_time = date('Y-m-d H:i:s');
            $diffTime = intval((strtotime($now_time) - strtotime($res->updated_at))/(24 * 3600));
            if($diffTime>=1)
            {
                return msg_err(101012,'数据过去时间久，从新添加新数据,数据不在此服务返回');
            }
            $bool = $redis::set($keys,$res->values);
            if(!$bool){
                return msg_err(PassportController::NO_STATUS,'网络错误');
            }
            $redis::expire($keys,self::M);
            return msg_ok(PassportController::YSE_STATUS,'搜索数据',$res);
        }
        $bool = preg_match("/^[1-9]{1}\d{0,9}$/", $value);
        if($bool){
            if($value==$et)
            {
                return msg_err(101013,'搜索次数够10次，同步redis数据和小表数据，数据不在此服务返回');
            }else{
                $redis::incr($keys);
                $redis::expire($keys,self::M);
                return msg_ok(101014,'搜索次数不够10次，数据不在此服务返回');
            }
        }
        //对应数据库+1
        $this->incrM($data);
        return msg_ok(PassportController::YSE_STATUS,'搜索数据',$value);

    }

    /**
     * 历史数据
     */
    protected function historyD($data=false)
    {
        if(isset($data['api_token']))
        {
            $redis = GetAppToken($data['api_token']);
            if($redis['code'] == 200){
                $data['users_id'] = $redis['u_id'];
            }else{
                return msg_err($redis['code'],$redis['msg']);
            }
            return $this->history_yes->like_data($data);
        }
        return $this->history_no->like_data($data);
    }

    /**
     * 查询关键字+1
     */
    protected function incrM($data){
        if(isset($data['users_id'])){//关键词history——yes+1
            $this->history_yes
                ->where('key','=',$data['key'])
                ->where('users_id','=',$data['users_id'])
                ->increment('num');;
        }else{//关键词history——no+1
            $this->history_no
                ->where('key','=',$data['key'])
                ->increment('num');;
        }
    }

    /**
     * 数据存入小表回调redis存入redis
     * @param bool $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function insertHistory($data=false)
    {

        if(!isset($data['value'])&&!isset($data['key'])&&!isset($data['status'])){
            return msg_err(PassportController::NO_STATUS,'缺少参数');
        }
        if(empty($data['values'])){
            return ;
        }
        $obj = $this->history_no;
        if(!isset($data['users_id']))//没有token---history_no
        {
            $b = $obj->where('key','=',$data['key'])->first();
            if($b){//修改
                $bool1 = $obj->
                where('key','=',$data['key'])->
                updata([
                    'values'=>serialize($data['values']),
                    'updated_at'=>date('Y-m-d H:i:s'
                    )]);
            }else{
                $bool1 = $obj->
                insert([
                    'key'=>$data['key'],
                    'values'=>serialize($data['values']),
                    'num'=>11,
                    'status'=>$data['status']
                ]);
            }
            $redis = new Redis();
            $bool2 = $redis::set($data['key'].'_'.$data['status'],serialize($data['values']));
        }else{//有token---history_yes
            $obj = $this->history_yes;
            $b = $obj->where(['key'=>$data['key'],'users_id'=>$data['users_id']])->first();
            if($b){
                $bool1 = $obj->where(['key'=>$data['key'],'users_id'=>$data['users_id']])->update(['values'=>serialize($data['values']),'updated_at'=>date('Y-m-d H:i:s')]);
            }else{
                $bool1 =$obj->insert(['key'=>$data['key'],'values'=>serialize($data['values']),'num'=>10,'status'=>$data['status'],'users_id'=>$data['users_id'],'updated_at'=>date('Y-m-d H:i:s')]);
            }
            $redis = new Redis();
            $bool2 =$redis::set($data['key'].'_'.$data['status'].'_'.$data['users_id'],serialize($data['values']));
        }
        if($bool1&&$bool2)
        {
            return msg_ok(PassportController::YSE_STATUS,'已到缓存区',new \stdClass());
        }else{
            return msg_err(PassportController::NO_STATUS,'网络异常');
        }


    }

    public function redisData($data=false)
    {
        $keys = $data['key'].'_'.$data['status'];
        if(isset($data['users_id'])){
            $keys = $data['key'].'_'.$data['status'].'_'.$data['users_id'];
        }
        $redis = new Redis();
        $value = $redis::get($keys);
        return $value;
    }

}