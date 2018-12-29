<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tile extends Model
{
    //
    function categories(){
        return $this->belongsToMany(Category::class);
    }
    function czsx(){
        return $this->belongsToMany(Category::class)->select('name');
    }
//    function category(){
//        return $this->belongsTo(Category::class,'id','parent_id')->select('id','name');
//    }
    function brand(){
        return $this->belongsTo(Brand::class)->select('id','name','trade_mark','service_tel','user_id');
    }

    public function setImagesAttribute($pictures)
    {
        if (is_array($pictures)) {
            $this->attributes['images'] = json_encode($pictures);
        }
    }
    public function getImgAttribute($img){
        return resize_img_src($img,300,200);
    }
    public function getImagesAttribute($pictures)
    {
        return json_decode($pictures, true);
    }
    public function getApiImagesAttribute($pictures)
    {
        $arr = json_decode($pictures, true);
        $arr2 = [];
        foreach($arr as $k=>$v){
            $name = pathinfo($v,PATHINFO_BASENAME);
//            $arr2[$k]['name'] = get_between($name,'-','.');
            $arr2[$k]['name'] = substr($name, 0,(strlen($name) - strpos($name, '.'))*(-1));
            $arr2[$k]['s_img'] = resize_img_src($v,300,200);
            $arr2[$k]['b_img'] = $v;
        }
        return $arr2;
    }
    public function getApiPhotosAttribute($pictures)
    {
        $arr = json_decode($pictures, true);
        $arr2 = [];
        foreach($arr as $k=>$v){
            $name = pathinfo($v,PATHINFO_BASENAME);
            $arr2[$k]['name'] = substr($name, 0,(strlen($name) - strpos($name, '.'))*(-1));
            $arr2[$k]['s_img'] = resize_img_src($v,300,200);
            $arr2[$k]['b_img'] = $v;
        }
        return $arr2;
    }
    public function setPhotosAttribute($pictures)
    {
        if (is_array($pictures)) {
            $this->attributes['photos'] = json_encode($pictures);
        }
    }

    public function getPhotosAttribute($pictures)
    {
        return json_decode($pictures, true);
    }

    public function search_datas($arr)
    {
        if(!isset($arr['key'])||$arr['key']=='')
        {
            return new \stdClass();
        }
        $data =  \DB::table('tiles')
            ->select(['id','img','name'])
            ->where('name','like','%'.$arr['key'].'%')
            ->where(['status'=>1])
            ->orderBy('updated_at','desc')
            ->get();
        foreach ($data as $k=>$v)
        {
            $data[$k]->img = resize_img_src($data[$k]->img,300,200);
        }
        return $data;
    }
    public function search_data($arr)
    {
        $arr['page'] = ($arr['now_page']-1)*10;
        if(!isset($arr['key'])||$arr['key']=='')
        {
            return new \stdClass();
        }
        $data =  \DB::table('tiles')
            ->select(['id','img','name'])
            ->where('name','like','%'.$arr['key'].'%')
            ->where(['status'=>1])
            ->orderBy('updated_at','desc')
            ->offset($arr['page'])
            ->limit(10)
            ->get();
        foreach ($data as $k=>$v)
        {
            $data[$k]->img = resize_img_src($data[$k]->img,300,200);
        }
        return $data;
    }
    public function search_count($key)
    {
        if(!isset($key)||$key=='')
        {
            return 0;
        }

        return \DB::table('tiles')
            ->where('name','like','%'.$key.'%')
            ->where(['status'=>1])
            ->count();
    }
}
