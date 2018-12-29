<?php

namespace App\Http\Controllers\Api;

use App\Models\AipImageSearchLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Aipimagessearch\AipImageSearch;
use Illuminate\Support\Facades\Storage;
class ImageSearchControlerodel extends Controller
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
                'images' => 'required',
                'pagenum' => 'integer',
                'page' => 'integer',
            ],[],[
                'images' => '图片',
                'pagenum' => '单页数量',
                'page' => '页码',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }
        $files = $request->file('images');
        if(!$files){
            return msg_err(401, '文件不存在');
        }
        $page = $request->get('page')>=1?:1;
        $pagenum = $request->get('pagenum')?:10;
        $start_num = ($page-1)*$pagenum;

        if ($files && $files->isValid()) {
            //取扩展名
            $ext = $files->getClientOriginalExtension();
            //临时文件的绝对路径
            $realPath = $files->getRealPath();
            $fileName =  'images_search/' . date("Ymd") . '/' . date("YmdHis") . '-' . uniqid() . '.' . $ext;
            $bool = Storage::disk('admin')->put($fileName, file_get_contents($realPath));
            if($bool){
                $image = file_get_contents('./uploads/'.$fileName);
                $client = new AipImageSearch(APP_ID, API_KEY, SECRET_KEY);
                // 调用相似图检索—检索, 图片参数为本地图片
//                $r = $client->similarSearch($image);
                $options = array();
                $options["tags"] = "";
                $options["tag_logic"] = "";
                $options["pn"] = $start_num;
                $options["rn"] = $pagenum;
                $list['xiangtong'] = $client->similarSearch($image, $options);
                // 带参数调用相似图检索—检索, 图片参数为本地图片
                $list['xiangsi'] = $client->similarSearch($image, $options);
                $options = array();
                $options["class_id1"] = '';
                $options["class_id2"] = '';
                $options["pn"] = $start_num;
                $options["rn"] = $pagenum;
                $list['shangpin']=$client->productSearch($image, $options);
                return $list;
            }
        }
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
     * @param  \App\Models\AipImageSearchLog  $aipImageSearchLog
     * @return \Illuminate\Http\Response
     */
    public function show(AipImageSearchLog $aipImageSearchLog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AipImageSearchLog  $aipImageSearchLog
     * @return \Illuminate\Http\Response
     */
    public function edit(AipImageSearchLog $aipImageSearchLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AipImageSearchLog  $aipImageSearchLog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AipImageSearchLog $aipImageSearchLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AipImageSearchLog  $aipImageSearchLog
     * @return \Illuminate\Http\Response
     */
    public function destroy(AipImageSearchLog $aipImageSearchLog)
    {
        //
    }
}
