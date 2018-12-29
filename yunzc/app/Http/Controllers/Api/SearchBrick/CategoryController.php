<?php
/**
 * 找砖属性
 * User: 霍文俊
 * Date: 2018/11/22
 * Time: 14:58
 */
namespace App\Http\Controllers\Api\SearchBrick;

use App\Http\Controllers\Api\PassportController;
use App\Http\Controllers\Controller;
use App\Models\Category;


class CategoryController extends Controller
{
    const CATEGOTY = 15;//品类id
    const ID = 1;//材质ID
    const TECHNOLOGY = 6;//工艺
    const SURFACE = 47;//表面
    const APPROPRIATE = 52;//适用范围
    const PRODUCTIONMANU = 59;//生产厂商
    const PARTS = 63;//配件
    public $obj;
    public function __construct()
    {
        $this->obj = new Category();
    }

    /**
     * 属性品类接口
     * 父级ID = 15
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
       return msg_ok(PassportController::YSE_STATUS,'品类列表',$this->obj->sel_list(self::CATEGOTY));
    }

    /**
     * 属性材质接口
     * 材质主父级id==1
     */
    public function materia()
    {
        return msg_ok(PassportController::YSE_STATUS,'材质列表',$this->obj->sel_list(self::ID));
    }

    /**
     * 属性工艺
     * 父级ID = 6
     */
    public function technology()
    {
        return msg_ok(PassportController::YSE_STATUS,'工艺列表',$this->obj->sel_list(self::TECHNOLOGY));
    }

    /**
     * 属性表面接口
     * 父级ID=47
     */
    public function surface()
    {
        return msg_ok(PassportController::YSE_STATUS,'表面列表',$this->obj->sel_list(self::SURFACE));
    }

    /**
     * 适用范围接口
     * 父级ID = 52
     */
    public function appropriate()
    {
        return msg_ok(PassportController::YSE_STATUS,'适用范围列表',$this->obj->sel_list(self::APPROPRIATE));
    }

    /**
     * 生产厂商属性接口
     * 父级ID = 59
     */
    public function productionManu()
    {
        return msg_ok(PassportController::YSE_STATUS,'生产厂商列表',$this->obj->sel_list(self::PRODUCTIONMANU));
    }

    /**
     * 配件属性接口
     * 父级ID = 63
     */
    public function parts()
    {
        return msg_ok(PassportController::YSE_STATUS,'配件属性列表',$this->obj->sel_list(self::PARTS));
    }
}