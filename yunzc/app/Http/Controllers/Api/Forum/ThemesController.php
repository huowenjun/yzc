<?php
/**
 * 主题论坛
 * User: Administrator
 * Date: 2018/11/23
 * Time: 15:29
 */
namespace App\Http\Controllers\Api\Forum;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\PassportController;
use App\Models\Theme;

class ThemesController extends Controller
{
    /**
     * 主题论坛列表选择接口
     * @return int
     */
    public function themeForum()
    {
        $themeObj = new Theme();
        $info = $themeObj->sel_data();
        return msg_ok(PassportController::YSE_STATUS,'主题论坛列表',$info);
    }
}