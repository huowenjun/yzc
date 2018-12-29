<?php
/**
 * Created by PhpStorm.
 * Date: 2018/12/20
 * Time: 11:44
 */

namespace App\Http\Controllers;

use App\Models\Tile;
use App\Models\Forum;
use App\Models\Versions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class IndexController extends Controller
{
    //瓷砖分享页面
    public function tiles_detail()
    {
        $sys = Versions::select('server_name','server_img')->first();
        return view('tiles_detail',['gid'=>request('id'),'room_city'=>request('room_city'),'sys'=>$sys]);
    }
    //论坛分享页面
    public function forums_detail()
    {
        $id = request('id');
        if(!$id){
            return false;
        }
        $info = Forum::where(['id'=>$id,'examine'=>1])->first();

        if(empty($info) || $info == null){
            die('数据不存在或已删除');
        }
        $info->toArray();
        if($info['photo']){
            $info['photo'] = json_decode_photo($info['photo']);
        }
        $info['created_at'] = date("Y-m-d",strtotime($info['created_at']));
        $info['type_name'] = forums_com($info['parent_type'],$info['type']);
        $sys = Versions::select('server_name','server_img')->first();
        return view('forums_detail',['info'=>$info,'sys'=>$sys]);
    }
    public function app_down(Request $request){
        $sta = $request->get('sta') ? :1;
        if($sta == 1){
            return "https://www.pgyer.com/r4G4";
        }else{
            return "https://www.pgyer.com/RxBw";
        }
    }

    public function resizeImg($img='',$w='',$h =''){
        $img = base64_decode($img);
        $iscz = Storage::exists($img);
        if($iscz == false){
           die;
        }
        $w = $w?:50;
        $h = $h?:50;
        $img2 =  \Image::make('uploads/'.$img)->resize($w,$h);
        return $img2->response('jpg');
    }
}