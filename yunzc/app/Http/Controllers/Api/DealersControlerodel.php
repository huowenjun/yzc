<?php

namespace App\Http\Controllers\Api;

use App\Models\Dealers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
class DealersControlerodel extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
        $redis = GetAppToken($request->get('api_token'));
        if($redis['code'] == 200){
            $where['pid'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $where['room_city'] = $request->get('room_city');
        $pagenum = $request->get('pagenum')?:10;
        $list = Dealers::select('id','dealers_name','room_city as room_name','status','pid as brand_name')->where($where)->paginate($pagenum);
        $list2 = obj_array($list);
        return msg_ok(200,'success',$list2);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'uname' => 'required|max:50',
                'utel' => 'required|max:20',
                'position' => 'required|max:50',
                'attr' => 'required|numeric',
                'dealers_name' => 'required',
                'room_city'=>'required|numeric',
                'address'=>'required',
                'address2'=>'required',
                'jingwei'=>'required',

            ],[],[
                'uname' => '联系人姓名',
                'utel' => '联系电话',
                'position' => '经销商职位',
                'attr' => '经销商属性',
                'dealers_name' => '经销商名称',
                'room_city' => '所在地区',
                'address' => '手动输入的详细地址',
                'address2' => '定位的详细地址',
                'jingwei' => '经纬度',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }
        $data = handleData($request->all());
        $redis = GetAppToken($request->get('api_token'));
        if($redis['code'] == 200){
            $data['pid'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $where ['uname'] = $data['uname'];
        $where ['utel'] = $data['utel'];
        $where ['position'] = $data['position'];
        $where ['attr'] = $data['attr'];
        $where ['pid'] = $data['pid'];
        $where ['dealers_name'] = $data['dealers_name'];
        $where ['room_city'] = $data['room_city'];
        $where ['address'] = $data['address'];
        $where ['address2'] = $data['address2'];
        $where ['jingwei'] = $data['jingwei'];
        $single_num = Dealers::where($where)->count();
        if($single_num){
            return msg_err(401,'已添加过相同信息的经销商，重复操作');
        }
        unset($data['api_token']);
        $r = Dealers::create($data);
        if($r->id){
            return msg_ok(200,'提交成功');
        }else{
            return msg_err(401,'提交失败');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Dealers  $dealers
     * @return \Illuminate\Http\Response
     */
    public function show(Dealers $dealers)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Dealers  $dealers
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'g_id' => 'required|numeric',

            ],[],[
                'g_id' => '经销商id',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }
       $info = Dealers::where('id',request('g_id'))->select('id','uname','utel','position','attr','dealers_name','jingwei','address','address2','room_city')->first();
        if($info){
            return msg_ok(200,'success',$info);
        }else{
            return msg_err(401,'经销商信息不存在或已删除');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Dealers  $dealers
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Dealers $dealers)
    {
        $validator = Validator::make($request->all(),
            [
                'g_id'=>'required|numeric',
                'uname' => 'required|max:50',
                'utel' => 'required|max:20',
                'position' => 'required|max:50',
                'attr' => 'required|numeric',
                'dealers_name' => 'required',
                'room_city'=>'required|numeric',
                'address'=>'required',
                'address2'=>'required',
                'jingwei'=>'required',

            ],[],[
                'g_id' => '经销商',
                'uname' => '联系人姓名',
                'utel' => '联系电话',
                'position' => '经销商职位',
                'attr' => '经销商属性',
                'dealers_name' => '经销商名称',
                'room_city' => '所在地区',
                'address' => '手动输入的详细地址',
                'address2' => '定位的详细地址',
                'jingwei' => '经纬度',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }
        $data = $request->all();
        $redis = GetAppToken($request->get('api_token'));
        if($redis['code'] == 200){
            $data['pid'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $where ['id'] = $data['g_id'];
        $where ['pid'] = $data['pid'];
        $single_num = Dealers::where($where)->get();
        if(!$single_num){
            return msg_err(401,'经销商信息不存在，不能编辑');
        }
        unset($data['api_token']);
        unset($data['pid']);
        unset($data['g_id']);
        $r = Dealers::where($where)->update($data);
        if($r){
            return msg_ok(200,'编辑成功');
        }else{
            return msg_err(401,'编辑失败');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Dealers  $dealers
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'g_id' => 'required|numeric',

            ],[],[
                'g_id' => '经销商id',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }
        $dealers = Dealers::find($request->get('g_id'));

        $r = $dealers->delete();
        if($r){
            return msg_ok(200,'删除成功');
        }else{
            return msg_err(401,'删除失败');
        }
    }
    /*经销商 状态切换*/
    public function status_toggle(Request $request){
        $validator = Validator::make($request->all(),
            [
                'g_id'=>'required|numeric',
                'status' => 'required|numeric',

            ],[],[
                'g_id' => '经销商id',
                'status' => '状态',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }
        $data = $request->all();
        $redis = GetAppToken($request->get('api_token'));
        if($redis['code'] == 200){
            $data['pid'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $where ['id'] = $data['g_id'];
        $where ['pid'] = $data['pid'];

        $single_num = Dealers::where($where)->get();
        if(!$single_num->toArray()){
            return msg_err(401,'经销商信息不存在，不能编辑');
        }
        unset($data['api_token']);
        unset($data['pid']);
        unset($data['g_id']);
        $r = Dealers::where($where)->update($data);
        if($r){
            return msg_ok(200,'编辑成功');
        }else{
            return msg_err(401,'编辑失败');
        }
    }
}
