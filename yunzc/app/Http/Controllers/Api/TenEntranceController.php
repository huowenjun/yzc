<?php
/**
 * 首页和我的十个入口
 * User: HWJ
 * Date: 2018/11/14
 * Time: 13:29
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agents;
use App\Models\ClearingHouses;
use App\Models\GetrentSetrents;
use App\Models\JobRecruits;
use App\Models\SearchBricks;
use App\Models\Shops;
use Illuminate\Support\Facades\Redis;


class TenEntranceController extends Controller
{
    const CREATE_STATUS_TURE_YES = '添加成功';
    const CREATE_STATUS_TURE_NO = '添加失败';
    const TURE = '查询成功';

    public static function obj()
    {
        return new \stdClass();
    }

    /**
     * 广播找砖
     */
    public function searchBricks()
    {
        $searchBricksObj = new SearchBricks();
        $searchBricksInfo = request()->all();
        if (isset($searchBricksInfo['tmp'])&&$searchBricksInfo['tmp']=1) {//1查询列表，2查询条数
            $info = $this->obj();
            if (isset($searchBricksInfo['id'])) {//取详情--id
                $info = $searchBricksObj->sel_agents_info($searchBricksInfo['id']);
            }else{//取列表1时间先后2审核通过，4。数据条数
                if(isset($searchBricksInfo['num']))
                {
                    $info = $searchBricksObj->sel_agents_list($searchBricksInfo['num']);
                }
            }
            return msg_ok(PassportController::YSE_STATUS,self::TURE,$info);
        }else{//用户提交数据
            if (empty($searchBricksInfo) ||
                !array_key_exists('specifications',$searchBricksInfo) ||
                !array_key_exists('category',$searchBricksInfo)||
                !array_key_exists('style',$searchBricksInfo)||
                !array_key_exists('brands_id',$searchBricksInfo)||
                !array_key_exists('sketch',$searchBricksInfo)||
                !array_key_exists('photo',$searchBricksInfo)
            ) {
                return msg_err(PassportController::NO_STATUS,PassportController::PARAM);
            }
            $redis = GetAppToken($searchBricksInfo['api_token']);
            if($redis['code'] == 200){
                $searchBricksInfo['users_id'] = $redis['u_id'];
            }else{
                return msg_err($redis['code'],$redis['msg']);
            }
            $searchBricksInfo = handleData($searchBricksInfo);
            //数据验证通过
            $bool = $searchBricksObj->create($searchBricksInfo);
            if(!$bool){
                return msg_err(PassportController::NO_STATUS,self::CREATE_STATUS_TURE_NO);
            }
            return msg_ok(PassportController::YSE_STATUS,self::CREATE_STATUS_TURE_YES,$this->obj());
        }
    }
}