<?php
/**
 * 品牌论坛
 * User: Administrator
 * Date: 2018/11/23
 * Time: 14:59
 */

namespace App\Http\Controllers\Api\Forum;

use App\Http\Controllers\Api\PassportController;
use App\Http\Controllers\Controller;
use App\Models\Brand;

class BrandsController extends Controller
{
    public $obj;
    const TERM = '品牌论坛列表';

    public function __construct()
    {
        $this->obj = new Brand();
    }

    /**
     * 品牌论坛列表选择接口
     * @return int
     */
    public function brandForum()
    {
        $info = $this->obj->sel_data();
        return msg_ok(PassportController::YSE_STATUS, self::TERM, $info);
    }

}