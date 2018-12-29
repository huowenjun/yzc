<?php
/**
 * 品牌
 * User: hwj
 * Date: 2018/11/26
 * Time: 14:04
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use Validator;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $categoryObj = new Brand();
        return msg_ok(PassportController::YSE_STATUS,'品牌列表',$categoryObj->sel_data());
    }
    /*商户端修改商户的品牌logo*/
    public function upd_logo(Request $request){
        $validator = Validator::make($request->all(),
            [
                'trade_mark' => 'required',
                'status' => 'required|numeric|size:1',
            ],[],[
                'trade_mark' => '品牌logo',
                'status' => '用户类型',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }
        $data['trade_mark'] = $request->get('trade_mark');
        $redis = GetAppToken($request->get('api_token'));
        if($redis['code'] == 200){
            $where['user_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $r = Brand::where($where)->update($data);
        if($r){
            return  msg_ok(200,'修改成功');
        }else{
            return msg_err(401,'修改失败');
        }
    }
    /*商户端 获取商户品牌信息*/
    public function get_merchant_info(Request $request){
        $validator = Validator::make($request->all(),
            [
//                'status' => 'required|numeric|size:1',
            ],[],[
//                'status' => '用户类型',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }
        $data['trade_mark'] = $request->get('trade_mark');
        $redis = GetAppToken($request->get('api_token'));
        if($redis['code'] == 200){
            $where['a.user_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $info = Brand::from('brands as a')
            ->select('a.id','a.trade_mark','a.name','b.name as user_name')
            ->Leftjoin('users as b','b.id', '=', 'a.user_id')
            ->where($where)
            ->first();
        return  msg_ok(200,'success',$info);
    }
}