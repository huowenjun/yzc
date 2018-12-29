<?php

namespace App\Http\Controllers\Api;

use App\Models\DesignInstitute;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
class DesignInstituteController extends Controller
{
    /**
     * 设计院列表
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
        $data = $request->all();
        $where = [];
        $where2 = [];
        if(!empty($data['room_city'])){
            $where['city_id'] = $data['room_city'];
        }
        if(!empty($data['scale'])){
            switch($data['scale']){
                case 1:
                    $where2 =[
                        ['scale','>=',1],
                        ['scale','<=',10]
                    ];
                    break;
                case 3:
                    $where2 =[
                        ['scale','>=',10],
                        ['scale','<=',50]
                    ];
                    break;
                case 6:
                    $where2 =[
                        ['scale','>=',50],
                        ['scale','<=',100]
                    ];
                    break;
                case 9:
                    $where2 =[
                        ['scale','>',100]
                    ];
                    break;
            }
        }
        $pagenum = $request->get('pagenum')?$request->get('pagenum'):10;
//        \DB::connection()->enableQueryLog();#开启执行日志
        $list = DesignInstitute::select('id','title','photo','description','money','address')
            ->where('status',1)->where($where)->where($where2)->orderBy('updated_at','DESC')->paginate($pagenum);
//        return \DB::getQueryLog();
        return msg_ok(200,'success',obj_array($list));

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
     * @param  \App\Models\DesignInstitute  $designInstitute
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'g_id' => 'required|numeric',
            ],[],[
                'g_id' => '设计院id',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }
        $where ['a.id'] = $request->get('g_id');
        $where ['a.status'] = 1;
        $mode = new DesignInstitute;
        $info = $mode->setTable('a')
            ->from('design_institutes as a')
            ->leftJoin('cities as b', 'b.area_code', '=', 'a.city_id')
            ->select('a.id','a.title','a.description','a.scale','a.images','a.address','a.created_at as ctimes','a.city_id as room_city','a.money','a.u_name','a.u_mobile','b.area_name as room_name')
            ->where($where)->get();

        if($info->toArray()){
            $info = $info[0]->toArray();
            if($info){
                $info =  array_merge($info,['newtime'=>time()]);
            }
            return msg_ok(200,'success',$info);
        }else{
            return msg_err(401,'设计院信息不存在或已删除');
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DesignInstitute  $designInstitute
     * @return \Illuminate\Http\Response
     */
    public function edit(DesignInstitute $designInstitute)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DesignInstitute  $designInstitute
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DesignInstitute $designInstitute)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DesignInstitute  $designInstitute
     * @return \Illuminate\Http\Response
     */
    public function destroy(DesignInstitute $designInstitute)
    {
        //
    }
}
