<?php
/**
 * 版本控制--关于我们
 * User: hwj
 * Date: 2018/11/24
 * Time: 10:28
 */
namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Versions;

class VersionsController extends Controller
{
    public $vObj;
    public function __construct()
    {
        $this->vObj = new Versions();
    }

    /**
     * 关于我们
     * @return int  1:安卓 0：ios
     */
    public function aboutUs()
    {
        $versionData = request()->all();
        if (empty($versionData) ||
            !isset($versionData['num'])
        ) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        return msg_ok(PassportController::YSE_STATUS,'关于我们',$this->vObj->sel_data($versionData['num']));
    }
    /**
     * 版本控制
     */
    public function version()
    {
        $versionData = request()->all();
        if (empty($versionData) ||
            !isset($versionData['num'])||
            !isset($versionData['version'])
        ) {
            return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
        }
        $res = $this->vObj->sel_data($versionData['num']);
        $bool = 1;
        if($versionData['num'] == 0)//ios版本修改
        {
            if($res->now_version_i != $versionData['version']){//修改版本ios
                $bool = $this->vObj->up_data($versionData);
            }
        }elseif ($versionData['num'] == 1){//安卓版本修改
            if($res->now_version_a != $versionData['version']){//修改版本安卓
                $bool = $this->vObj->up_data($versionData);
            }
        }
        if(!$bool)
        {
            return msg_err(PassportController::YSE_STATUS,'修改失败',new \stdClass());
        }
        return msg_ok(PassportController::YSE_STATUS,'修改成功',new \stdClass());

    }
}