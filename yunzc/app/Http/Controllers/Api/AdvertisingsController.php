<?php
/**
 * 广告
 * User: hwj
 * Date: 2018/11/22
 * Time: 13:03
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advertising;


class AdvertisingsController extends Controller
{
    public function index()
    {
        $advertisingObj = new Advertising();
        $data = $advertisingObj->sel_data();
        $count = $advertisingObj->sel_count();
        $num = mt_rand(0,$count-1);
        if(isset($data[$num])){
            $res = $data[$num];
        }else{
            $res = new \stdClass();
        }
        return msg_ok(PassportController::YSE_STATUS,'广告',$res);
    }
}