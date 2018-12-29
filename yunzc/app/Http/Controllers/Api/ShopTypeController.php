<?php
/**
 * 店铺类型
 * User: Administrator
 * Date: 2018/11/26
 * Time: 10:45
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shops;



class ShopTypeController extends Controller
{
    const CREATE_STATUS_TURE_YES = '添加成功';
    const CREATE_STATUS_TURE_NO = '添加失败';
    const TURE = '查询成功';
    public function shopType()
    {
        $shopObj = new Shops();
        $info = $shopObj->show();
        return msg_ok(PassportController::YSE_STATUS,self::TURE,$info);

    }
}