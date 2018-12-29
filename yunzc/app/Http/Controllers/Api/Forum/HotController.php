<?php
/**
 * 热门论坛
 * User: hwj
 * Date: 2018/11/30
 * Time: 10:05
 */
namespace App\Http\Controllers\Api\Forum;

use App\Http\Controllers\Api\PassportController;
use App\Http\Controllers\Controller;
use App\Models\Forum;


class HotController extends Controller
{
    public function hotForum()
    {
        $res = new \stdClass();
        $data = request()->all();
        $forumM = new Forum();
        if(!isset($data['now_page']))
        {
            $data['now_page'] = 1;
        }
        $res->list = $forumM->hot_forum($data);//总列表
        $res->last_page = ceil($forumM->hot_count()/10);//总条数
        $res->current_page = $data['now_page'];//当前页码
        return msg_ok(PassportController::YSE_STATUS,'热门列表',$res);
    }
}