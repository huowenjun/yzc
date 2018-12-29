<?php
/**
 * 品类
 * User: hwj
 * Date: 2018/11/22
 * Time: 14:58
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;


class CategoryController extends Controller
{
    public function index()
    {
       $categoryObj = new Category();
       return msg_ok(PassportController::YSE_STATUS,'品类列表',$categoryObj->sel_list(15));
    }
}