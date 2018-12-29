<?php
/**
 * 材质
 * User: hwj
 * Date: 2018/11/22
 * Time: 14:58
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;


class MaterialController extends Controller
{
    public function index()
    {
        $categoryObj = new Category();
        return msg_ok(PassportController::YSE_STATUS,'材质列表',$categoryObj->sel_list(1));
    }
}