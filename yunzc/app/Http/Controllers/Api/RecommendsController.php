<?php
/**
 * 大咖推荐
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/19
 * Time: 15:49
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recommend;
use Illuminate\Support\Facades\Redis;

class RecommendsController extends Controller
{
    const SHOW_TREM_NO = '客官,已经没有了';
    const GETSHOW = '无数据';
    const GETSHOW_STATUS_NO = 0;
    public $rObj;
    const HM = 1*24*60*60;
    public function __construct()
    {
        $this->rObj = new Recommend();
    }

    /**
     * 只显示三条,可以左右划，就是翻页，小于3条不显示
     * get
     * num
     */
    public function show()
    {
        $redis = new Redis();
        $msg = '缓存大咖数据';
        $recommend_data = $redis::get('r_data');
        if(!isset($recommend_data)){
            $recommend_data = $this->rObj->show();
            $redis::set('r_data',serialize($recommend_data));
            $msg = '大咖数据';
            $redis::expire('r_data',self::HM);
        }else{
            $recommend_data = unserialize($recommend_data);
        }


        return msg_ok(PassportController::YSE_STATUS,$msg,$recommend_data);
    }

    /**
     * 查看详情
     * id
     * get
     */
    public function getShow()
    {
        $data = request()->all();
        if (empty($data) ||
            !isset($data['id'])) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $recommend_data = $this->rObj->getShow($data['id']);
        return msg_ok(PassportController::YSE_STATUS,'大咖详情',$recommend_data);
    }
}