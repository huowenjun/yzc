<?php

namespace App\Http\Controllers\Api;

use App\Models\Procurement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Support\Facades\Log;
class ProcurementControlerodel extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * 首页-采购快讯列表
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'room_city' => 'required|numeric',

            ],[],[
                'room_city' => '城市id',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }
        $where['status'] = 1;
        $where['room_city'] = $request->get('room_city');

        $pagenum = $request->get('pagenum')?:10;
        $list = Procurement::select('id','area','brand as brand_name','brick_time','address','room_city as room_name','area','ctime as ctimes','images')
            ->orderBy('utime','DESC')
            ->where($where)->paginate($pagenum);
        $list2 = obj_array($list);
        return msg_ok(200,'success',$list2);
    }
    public function my_list(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
//                'room_city' => 'required|numeric',

            ],[],[
//                'room_city' => '城市id',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }
//        $where['status'] = 1;
//        $where['room_city'] = $request->get('room_city');
        $where = [];
        $redis = GetAppToken($request->get('api_token'));
        if($redis['code'] == 200){
            $where['u_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $pagenum = $request->get('pagenum')?:10;
        $list = Procurement::select('id','area','brand as brand_name','u_id','brick_time','address','room_city as room_name','area','ctime as ctimes','images')
            ->orderBy('utime','DESC')
            ->where($where)->paginate($pagenum);
        $list2 = obj_array($list);
        return msg_ok(200,'success',$list2);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'brand' => 'required|numeric',
                'type' => 'required|numeric',
                'models' => 'required|numeric',
                'material' => 'required|numeric',
                'area' => 'required|numeric',
                'room_city'=>'required|numeric',
//                'address'=>'required',
                'brick_time'=>'required|numeric',

            ],[],[
                'brand' => '品牌要求',
                'type' => '瓷砖类型',
                'models' => '型号要求',
                'material' => '材质要求',
                'area' => '采购面积',
                'room_city' => '所在地区',
//                'address' => '详细地址',
                'brick_time' => '用砖时间',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }
        $data = handleData($request->all());
        $redis = GetAppToken($request->get('api_token'));
        if($redis['code'] == 200){
            $data['u_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $where ['brand'] = $data['brand'];
        $where ['type'] = $data['type'];
        $where ['models'] = $data['models'];
        $where ['material'] = $data['material'];
        $where ['u_id'] = $data['u_id'];
        $where ['area'] = $data['area'];
        $where ['room_city'] = $data['room_city'];
        $where ['address'] = $data['address'];
        $where ['brick_time'] = $data['brick_time'];
        $single_num = Procurement::where($where)->count();
        if($single_num){
            return msg_err(401,'已提交过相同的采购快讯，操作失败');
        }
        $redis = GetAppToken($request->get('api_token'));
        if($redis['code'] == 200){
            $data['u_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }

        $data['images'] = $request->get('js_images');
        unset($data['js_images']);
        unset($data['api_token']);
        $r = Procurement::create($data);
        if($r->id){
            if(!set_release_num($data['u_id'])){
                Log::info('用户: '.$data['u_id'].'添加采购快讯，发布数量自增加失败');
            }
            return msg_ok(200,'提交成功');
        }else{
            return msg_err(401,'提交失败');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Procurement  $procurement
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'g_id' => 'required|numeric',

            ],[],[
                'g_id' => '采购快讯id',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }
        $data = $request->all();
        $where['id'] = $data['g_id'];
//        $where['status'] = 1;
        $info = Procurement::where($where)->select('id','u_id','area','brand as brand_name','type as type_name','models as models_name','material as material_name','brick_time','address','area','ctime as ctimes','images','room_city as room_name')->first();
        $info = $info->toArray();
        if($info){
            $info['newtime'] = time();
            return msg_ok(200,'success',$info);
        }else{
            return msg_err(401,'数据不存在或已删除');
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * 删除 采购快讯
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'g_id' => 'required|numeric',

            ],[],[
                'g_id' => '采购快讯id',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }
        $data = $request->all();
        $where['id'] = $data['g_id'];
        $info = Procurement::where($where)->value('id');

        if($info){
            $r = Procurement::destroy($where['id']);
            if($r){
                return msg_ok(200,'删除成功');
            }else{
                return msg_err(401,'删除失败');
            }
        }else{
            return msg_err(401,'数据不存在或已删除');
        }
    }
}
