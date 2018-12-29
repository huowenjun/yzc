<?php

namespace App\Http\Controllers\Api;

use App\Models\Brand;
use App\Models\BrandEvaluates;
use App\Models\SystemMessage;
use App\Models\UserMessageLookLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
class BrandEvaluatesControlerodel extends Controller
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
            $where['user_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $pagenum = $request->get('pagenum')?:10;
        $brand = Brand::where($where)->value('id');
        $where2['is_read'] = 0;
        $where2['brand_id'] = $where3['brand_id'] = $brand;
        $page = $request->get('page')?:1;
        $list = BrandEvaluates::where($where2)->orderBy('id','DESC')->paginate($pagenum,['*'],'page',1);   //未读数据永远按第一页去读，改状态
        $count_num =  $list->count();  //返回的未读数据数
        if($count_num >0){  //未读条数小于单页条数
            $req = BrandEvaluates::where($where3)->paginate($pagenum);   //获取总数
            $last_page = $req->lastPage(); //总页码
            $save_ids = array_column($list->toArray()['data'],'id'); //未读数据 id集合
            if($count_num < $pagenum){
                //，返回去掉未读限制的数据，补全条数，
                $list2 = BrandEvaluates::where($where3)->orderBy('id','DESC')->paginate($pagenum);
                //修改未读数据的状态 加载出来即已读
                BrandEvaluates::whereIn('id',$save_ids)->update(['is_read'=>1]);
                return msg_ok(200,'success',$this->obj_array_un($list2,$page,$last_page));
            }
            //修改未读数据的状态 加载出来即已读
            BrandEvaluates::whereIn('id',$save_ids)->update(['is_read'=>1]);
            return msg_ok(200,'success',$this->obj_array_un($list,$page,$last_page));
        }else{
            $list = BrandEvaluates::where($where3)->orderBy('id','DESC')->paginate($pagenum);
            return msg_ok(200,'success',obj_array($list));
        }
    }
    /*未读文件的 返回参数拼装*/
    public function obj_array_un($obj,$page =1,$last_page){
        $list = $obj->toArray();
        $list2['current_page'] = $page;
        $list2['last_page'] = $last_page;
        $list2['data'] = $list['data'];
        return $list2;
    }

    /*系统消息*/
    public function message_list(Request $request){
        $redis = GetAppToken($request->get('api_token'));
        $data = $request->all();
        if($redis['code'] == 200){
            $data['u_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $pagenum = $request->get('pagenum')?:10;
        $list = SystemMessage::select('id','type as type_name','title','content','img')->orderBy('id','DESC')->paginate($pagenum);
        return msg_ok(200,'success',obj_array($list));
    }
    /*系统消息详情*/
    public function message_detail(Request $request){
        $validator = Validator::make($request->all(),
            [
                'g_id' => 'required|numeric',
            ],[],[
                'g_id' => '消息id',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }
        $redis = GetAppToken($request->get('api_token'));
        $data = $request->all();
        if($redis['code'] == 200){
            $data['u_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $list = SystemMessage::select('id','type as type_name','title','content','img')->find($data['g_id']);
        if(!$list){
            return msg_ok(401,'系统消息不存在或已被删除');
        }else{
            /*添加 查看记录*/
            $add['message_id'] = $data['g_id'];
            $add['u_id'] = $data['u_id'];
            $res = UserMessageLookLog::where($add)->first();
            if(!$res){
                UserMessageLookLog::create($add);
            }
            return msg_ok(200,'success',$list);
        }

    }
    /*商户系统消息 未读数目*/
    public function message_count(Request $request){
        $validator = Validator::make($request->all(),
            [
                'type' => 'required|numeric',
            ],[],[
                'type' => '用户类型',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }
        $list = [];

        $redis = GetAppToken($request->get('api_token'));

        if($redis['code'] == 200){
            $where['u_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $type = $request->get('type');
        if($type == 2){
            $brand = Brand::where('user_id',$where['u_id'])->value('id');
            $where2 ['is_read'] = 0;
            $where2 ['brand_id'] = $brand;
            $list['brand_num'] = BrandEvaluates::where($where2)->count();  //品牌下的未读评价数量
        }
        $znum = SystemMessage::count(); //系统消息的总数
        $look_num = UserMessageLookLog::where($where)->count();  //用户已读消息的总数
        $list['sys_mes'] = max(($znum-$look_num),0); //最小为0
        return msg_ok(200,'success',$list);

    }
}
