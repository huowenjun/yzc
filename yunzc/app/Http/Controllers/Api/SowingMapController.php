<?php
/**
 * 轮播图
 * User: hwj
 * Date: 2018/11/19
 * Time: 14:48
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Carousel;

class SowingMapController extends Controller
{
    const SOWINGMAP_TREM = '轮播图数据';
    public function show()
    {
        $data = request()->all();
        if (empty($data) ||
            !isset($data['type'])) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        //取轮播图按type类型
        $carouselObj = new Carousel();
        $carousel_data = $carouselObj->show($data['type']);
        return msg_ok(PassportController::YSE_STATUS,self::SOWINGMAP_TREM,$carousel_data);
    }
}