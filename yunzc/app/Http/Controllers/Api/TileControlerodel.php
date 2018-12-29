<?php

namespace App\Http\Controllers\Api;

use App\Models\Brand;
use App\Models\Dealers;
use App\Models\Tile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Models\CategoryTile;
use Overtrue\Pinyin\Pinyin;
class TileControlerodel extends Controller
{
    /**
     * 商户-商品管理-列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'status' => 'required|numeric',

            ],[],[
                'status' => '上下架状态',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }
        $redis = GetAppToken($request->get('api_token'));
        if($redis['code'] == 200){
            $data['u_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $gids = Brand::where('user_id',$data['u_id'])->pluck('id');

        $where['status'] = $request->get('status');
        $pagenum = $request->get('pagenum')?:10;
        $list = Tile::select('id','name','img','status')->where($where)->whereIn('brand_id',$gids)->orderBy('sort','ASC','id','DESC')->paginate($pagenum);
        $list2 = obj_array($list);
        return msg_ok(200,'success',$list2);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * @param  \App\Models\Tile  $tile
     * @return \Illuminate\Http\Response
     */
    public function show(Tile $tile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tile  $tile
     * @return \Illuminate\Http\Response
     */
    public function edit(Tile $tile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tile  $tile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tile $tile)
    {
        //
    }

    /**
     * 商品切换上下架状态
     *
     * @param  \App\Models\Tile  $tile
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tile $tile)
    {
        //
    }

    public function status_toggle(Request $request){
        $validator = Validator::make($request->all(),
            [
                'g_id'=>'required|numeric',
                'status' => 'required|numeric',

            ],[],[
                'g_id' => '商品id',
                'status' => '状态',
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
        $where ['id'] = $data['g_id'];
        $gids = Brand::where('user_id',$data['u_id'])->pluck('id');
        $single_num = Tile::where($where)->whereIn('brand_id',$gids)->get();

        if(!$single_num->toArray()){
            return msg_err(401,'商品信息不存在，不能编辑');
        }
        unset($data['api_token']);
        unset($data['u_id']);
        unset($data['g_id']);
        $r = Tile::where($where)->whereIn('brand_id',$gids)->update($data);
        if($r){
            return msg_ok(200,'编辑成功');
        }else{
            return msg_err(401,'编辑失败');
        }
    }
    /*商户首页-数量*/
    public function index_num(Request $request){
        $data = $request->all();
        $redis = GetAppToken($request->get('api_token'));
        if($redis['code'] == 200){
            $data['u_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $list = [];
        $gids = Brand::where('user_id',$data['u_id'])->pluck('id');
        $list['brand_up'] = Tile::whereIn('brand_id',$gids)->where('status',1)->count();
        $list['brand_down'] = Tile::whereIn('brand_id',$gids)->where('status',0)->count();
        $list['dealers_num'] = Dealers::where('pid',$data['u_id'])->count();
        return msg_ok(200,'success',$list);
    }
    /*主打产品 列表*/
    public function main_list(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
//                'status' => 'required|numeric',

            ],[],[
//                'status' => '上下架状态',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }
        $where = array();
        $where['status'] = 1;
        $where['is_main'] = 1;
        $pagenum = $request->get('pagenum')?:9;
//        return $where;
        $list = Tile::select('id','name','img')->where($where)->orderBy('sort','ASC','id','DESC')->limit($pagenum)->get();
        return msg_ok(200,'success',$list);
    }
    /*产品瓷砖-详情*/
    function tile_detail(Request $request){
        $validator = Validator::make($request->all(),
            [
                'g_id' => 'required|numeric',
            ],[],[
                'g_id' => '商品id',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }
        $where['status'] = 1;
        $where['id'] = $request->get('g_id');

        $info = Tile::with(['categories.category','brand.dealers'=> function ($query) {
            $room_city = request('room_city')?:'110000';
            $query->where('room_city', $room_city);
        }])->select('*','images as api_images','photos as api_photos')->where($where)->first();
        $arr = [];

        if($info){
            foreach($info['categories'] as &$v){
                $arr[$v['category']['name']]['name'] = $v['category']['name'];
                $arr[$v['category']['name']]['values'][] = $v['name'];
            }
            unset($info['categories']);
            unset($info['images']);
            unset($info['photos']);
            $info['categories'] = array_values($arr);
            return msg_ok(200,'success',$info);
        }else{
            return msg_ok(401,'商品不存在或已删除');
        }
    }

    /*瓷砖列表搜索*/
    public function get_tile_search(Request $request){
        $validator = Validator::make($request->all(),
            [
                'brand_id'=>'numeric',

            ],[],[
                'brand_id' => '品牌',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }
        $where = [];
        if(!empty($request->get('brand_id'))){
            $where ['brand_id'] = $request->get('brand_id');
        }
        $pagenum = $request->get('pagenum')?$request->get('pagenum'):10;

//whereHas('czsx', function ($query) {
//        $query->whereNotNull();
//    })

//        $list = Tile::select('id','img','name','brand_id')
//            ->where($where)->with(['brand','czsx' => function ($query) {
//            $ids = explode(',',request('categories'));
//            $query->wherePivotIn('category_id', $ids);
//        }])->paginate($pagenum);
//        return obj_array($list,2);
        $category_ids = request('categories')?explode(',',request('categories')):array();
        if(!empty($category_ids)){
            $tile_ids = CategoryTile::whereIn ('category_id',$category_ids)->distinct()->pluck('tile_id as id');
            $list = Tile::whereIn('id',$tile_ids)->where($where)->select('id','img','name','brand_id')->paginate($pagenum);
        }else{
            $list = Tile::where($where)->select('id','img','name','brand_id')->paginate($pagenum);
        }
        return msg_ok(200,'success',obj_array($list,2));
    }
    /*瓷砖 类型 列表*/

    public function tile_type_list(Request $request){
        $validator = Validator::make($request->all(),
            [
                'type'=>'required|numeric',
            ],[],[
                'type' => '分类',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }
        $where['type'] = $request->get('type');
        $pagenum = $request->get('pagenum')?:10;
        $category_ids = request('categories')?explode(',',request('categories')):array();
        if(!empty($category_ids)){
            $tile_ids = CategoryTile::whereIn ('category_id',$category_ids)->distinct()->pluck('tile_id as id');
            $list = Tile::whereIn('id',$tile_ids)->where($where)->select('id','img','name')->paginate($pagenum);
        }else{
            $list = Tile::where($where)->select('id','name','img')->paginate($pagenum);
        }
        return msg_ok(200,'success',obj_array($list));
    }
    /*分享接口 xx*/
    public function tiles_share(Request $request){
        $validator = Validator::make($request->all(),
            [
                'id' => 'required|numeric',

            ],[],[
                'id' => '瓷砖',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,'异常参数');
        }
        $id = $request->get('id');
        $info = Tile::where('id',$id)->select('id','name','img','description')->first();

        if($info){
            $info = $info->toArray();
            $info['link'] = getenv('APP_URL')."/tiles_detail/".$id;
            return msg_ok(200,'success',$info);
        }else{
            return msg_ok(401,'数据不存在');
        }
    }
}
