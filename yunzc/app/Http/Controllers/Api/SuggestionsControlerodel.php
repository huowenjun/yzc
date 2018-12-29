<?php

namespace App\Http\Controllers\Api;

use App\Models\Suggestions;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Models\AipImageSearchLog;
class SuggestionsControlerodel extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
                'type' => 'required|numeric',
                'title' => 'required|max:100',
                'content' => 'required|max:10000',

            ],[],[
                'type' => '类型',
                'title' => '标题',
                'content' => '反馈描述',
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
        $where ['type'] = $data['type'];
        $where ['title'] = $data['title'];
        $where ['u_id'] = $data['u_id'];
        $where ['content'] = $data['content'];
        $single_num = Suggestions::where($where)->count();
        if($single_num){
            return msg_err(401,'已提交过相同的投诉/建议，操作失败');
        }

        $data['images'] = $request->get('images');
        unset($data['api_token']);
        $r = Suggestions::create($data);
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
     * @param  \App\Models\Suggestions  $suggestions
     * @return \Illuminate\Http\Response
     */
    public function show(Suggestions $suggestions)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Suggestions  $suggestions
     * @return \Illuminate\Http\Response
     */
    public function edit(Suggestions $suggestions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Suggestions  $suggestions
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Suggestions $suggestions)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Suggestions  $suggestions
     * @return \Illuminate\Http\Response
     */
    public function destroy(Suggestions $suggestions)
    {
        //
    }
}
