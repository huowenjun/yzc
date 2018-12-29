<?php

namespace App\Http\Controllers\Api;

use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class AddressController extends Controller
{

    const M = 5*24*60*60;
    const HM = 1*24*60*60;
      /**
     * 城市列表.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $redis = new Redis();
        $db = \DB::table('cities');
        $redis_city = $redis::get('city');
        $redis_h_city = $redis::get('h_city');
        $msg = '';
        if(isset($redis_city)) {
            $data['list'] = unserialize($redis_city);
            $msg .= 'city缓存数据';
        }else{
            //列表
            $list = $db
                ->select(['id','area_name','area_code','initial'])
                ->orderBy('initial')
                ->get();
            foreach ($list as &$v) {
                $v->initial = strtoupper($v->initial);
            }
            $redis::set('city',serialize($list));
            $redis::expire('city',self::M);
            $data['list'] = $list;
            $msg .= 'city';
        }
        if(isset($redis_h_city)){
            $data['hot_city'] = unserialize($redis_h_city);
            $msg .= 'hot_city缓存数据';
        }else{
            $hot_city = $db
                ->select(['id','area_name','area_code','initial'])
                ->where('status','=',1)
                ->get();
            $redis::set('h_city',serialize($hot_city));
            $redis::expire('h_city',self::HM);
            $data['hot_city'] = $hot_city;
            $msg .= '+h_city';
        }
        return msg_ok(PassportController::YSE_STATUS,$msg,$data);
    }

    /**
     * 根据城市名获取城市信息
     */
    public function citiesInfo()
    {
        $cities = request()->all();
        if (empty($cities) ||
            !array_key_exists('city_name',$cities)) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $list = \DB::table('cities')
            ->select(['id','area_name','area_code','initial'])
            ->where('area_name', 'like', '%'.$cities['city_name'].'%')
            ->get();
        foreach ($list as &$v) {
            $v->initial = strtoupper($v->initial);
        }
        $list = arr($list);
        return msg_ok(PassportController::YSE_STATUS,'城市信息',$list);
    }


    /**
     * 获取图片基地址
     */
    public function get_base_url(){
        return msg_ok(PassportController::YSE_STATUS,'success',[getenv('APP_URL').'/uploads/']);
    }

}
