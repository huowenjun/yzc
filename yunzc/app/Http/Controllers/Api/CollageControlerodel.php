<?php

namespace App\Http\Controllers\Api;
use Validator;
use App\Models\Collage;
use App\Models\Cities;
use App\User;
use App\Models\CollagesUserList;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\Search\HistorySearchController;

class CollageControlerodel extends Controller
{
    /*拼单列表*/
    public  function getlist(Request $request){
        $room_city = request('room_city');
        $pagenum = request('pagenum')?request('pagenum'):10;
        if($room_city){
            $where['room_city'] = $room_city;
        }
        $where['status'] = 1;
        $keywords = $request->get('keywords');
        $list =  Collage::select('id','title','photo','description','num as nums','discounts','room_city','unit','symbol','status')
            ->where($where)
//            ->where('status',1)
            ->when($keywords, function ($query) use ($keywords) {
                return $query->where('title','like', '%'.$keywords.'%')->orWhere('keywords','like','%'.$keywords.'%');
            })->orderBy('sort','asc')
            ->paginate($pagenum);

        $list2 = $this->obj_array($list);
        return msg_ok(200,'success',$list2);
    }
    function obj_array($obj){
        $list = $obj->toArray();
        $list2['current_page'] = $list['current_page'];
        $list2['last_page'] = $list['last_page'];
        $list2['data'] = $list['data'];
        return $list2;
    }
    /*拼单详情*/
    public  function getdetail(Request $request){

        $validator = Validator::make($request->all(),
            [
                'g_id' => 'required|numeric',
//                'room_city' => 'required|numeric'
            ],[],[
                'g_id' => '商品id',
//                'room_city' => '城市id'
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }

        $collage = new \App\Models\Collage();
        $info =  $collage->setTable('a')
            ->from('collages as a')
            ->with(['single_list.userinfo', 'merchant'])
            ->leftJoin('cities as b', 'b.area_code', '=', 'a.room_city')
            ->select('a.id','a.title','a.images','a.num','a.content','a.discounts','a.goods_images','a.description','a.stime as stimes','a.etime as etimes','a.room_city','b.area_name','a.s_uid','a.unit','a.symbol','a.signle_num')
            ->where('a.id',$request->get('g_id'))
//            ->where('a.room_city',$request->get('room_city'))
            ->where('a.status',1)
            ->orderBy('sort','asc')
            ->get();

        if($info->toArray()){
            $info = $info[0]->toArray();
            if($info){
                $info =  array_merge($info,['newtime'=>time()]);
            }
            return msg_ok(200,'success',$info);
        }else{
            return msg_err(401,'商品不存在或已删除');
        }

    }
    // 城市列表
    function getcities(){
        header("Content-type:text/html;charset=utf-8");
        $city = Cities::get([\DB::raw('area_code id'), \DB::raw('area_name as text')]);
        return $city;
    }
    //商户列表
    public function getuser()
    {
        return User::where('status',1)->get(['id', \DB::raw('name as text')]);
    }
   /*添加拼单*/
    public function add_single(Request $request){

        $validator = Validator::make($request->all(),
            [
                'g_id' => 'required|numeric',
                'num' => 'required|numeric|min:1',
                'mobile' => 'required|digits:11|regex:/^1[3456789][0-9]{9}$/'
            ],[],[
                'g_id' => '商品id',
                'num' => '拼单数量',
                'mobile' => '联系电话'
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }
        $data = $request->all();

        $redis = GetAppToken($request->get('api_token'));

        if($redis['code'] == 200){
            $data['u_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }

        $single = new CollagesUserList;
        $collage = new Collage;
        $single_num = $single->where('g_id',$data['g_id'])->where('u_id',$data['u_id'])->count();
        if($single_num){
            return msg_err(401,'已参与拼单，不能重复参与');
        }
        DB::beginTransaction();
        $r = $single->create($data);
        $r2 = $collage->where('id',$data['g_id'])->increment('signle_num',$data['num']);
        if($r->id){
            DB::commit();
            return msg_ok(200,'拼单成功');

        }else{
            DB::rollBack();
            return msg_err(401,'拼单失败');
        }
    }

    /*拼单人列表*/
    public function single_list(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'g_id' => 'required|numeric',
            ],[],[
                'g_id' => '商品id',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }
        $data = $request->all();
        $pagenum = isset($data['pagenum']) ? $data['pagenum'] : 10;

        $single = new CollagesUserList;
        $vo = $single
            ->select('id','g_id','u_id','num')
            ->with('userinfo')
            ->where('g_id',$data['g_id'])->orderBy('num','DESC')->paginate($pagenum);
        $list2 = $this->obj_array($vo);
        return msg_ok(200,'success',$list2);
    }
    /*我的拼单列表*/
    public  function my_signle(Request $request){
        $validator = Validator::make($request->all(),
            [
                'api_token' => 'required'
            ],[],[
                'api_token' => '用户标识 token'
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }
        $data = $request->all();
        $pagenum = isset($data['pagenum']) ? $data['pagenum'] : 10;

        $collage = new CollagesUserList;
        $redis = GetAppToken($request->get('api_token'));

        if($redis['code'] == 200){
            $data['u_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $list =  $collage->with('goodsinfo')
            ->select('id','g_id','u_id','status','ctime')
            ->orderBy('status','asc','id','desc')
            ->where('u_id',$data['u_id'])
            ->paginate($pagenum);

        $list2 = $this->obj_array($list);

        $list2 =  array_merge($list2,['newtime'=>time()]);
        return msg_ok(200,'success',$list2);
    }

    /**
     * 拼单1论坛2瓷砖3拼单
     */
    public function searchCollage()
    {
        $arr = request()->all();
        $obj = new Collage();
        if(!isset($arr['now_page'])){
            $arr['now_page'] = 1;
        }
        if(!isset($arr['status'])||$arr['status']!=3)
        {
            return msg_err(PassportController::NO_STATUS,'参数错误或缺少参数');
        }
        if(isset($arr['api_token'])){
            $redis = GetAppToken($arr['api_token']);
            if($redis['code'] == 200){
                $arr['users_id'] = $redis['u_id'];
            }else{
                return msg_err($redis['code'],$redis['msg']);
            }
        }
        $historySM = new HistorySearchController();
        $hData = $historySM->data($arr);
        switch ($hData){
            case 101014:
                break;
            case 101013:
                $objectD = $obj->search_datas($arr);
                $arr['values'] = objectA($objectD);
                $historySM->insertHistory($arr);
                break;
            case 200:
                $unserializeD = unserialize($historySM->redisData($arr));
                $d = dataPage(['data'=>$unserializeD,'page'=>$arr['now_page']]);
                return msg_ok(PassportController::YSE_STATUS,'搜索缓存数据',$d);//数组做分段
                break;
            default:
                return msg_err(PassportController::NO_STATUS,'网络错误');
        }
        $res['data'] = $obj->search_data($arr);
        $res['last_page'] = ceil($obj->search_count(isset($arr['key'])?$arr['key']:'')/10);
        //当前页码
        $res['current_page'] = intval($arr['now_page']);
        return msg_ok(PassportController::YSE_STATUS,'瓷砖搜索数据',$res);
    }
}
