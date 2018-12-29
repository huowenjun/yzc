<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Overtrue\Pinyin\Pinyin;

class Brand extends Model
{
    const STATUS_OK = 1;
    public function setImagesAttribute($pictures)
    {
        if (is_array($pictures)) {
            $this->attributes['images'] = json_encode($pictures);
        }
    }

    public function getImagesAttribute($pictures)
    {
        return json_decode($pictures, true);
    }

    public function setHonorsAttribute($pictures)
    {
        if (is_array($pictures)) {
            $this->attributes['honors'] = json_encode($pictures);
        }


    }

    public function getHonorsAttribute($pictures)
    {
        return json_decode($pictures, true);
    }

    public function setNameAttribute($name){
        $pinyin = new Pinyin();
        $letter = implode(' ',$pinyin->convert($name));
        $initial = substr($letter,0,1);
        $this->attributes['letter'] = $letter;
        $this->attributes['initial'] = $initial;
        $this->attributes['name'] = $name;
    }
    function dealers(){
        return $this->hasMany(Dealers::class,'pid','user_id')->where('status',1);
    }
    public function tiles(){
        return $this->hasMany(Tile::class,'brand_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function forum(){
        return $this->morphMany(Forum::class,'contentable');
    }
    /**
     * 品牌论坛列表
     */
    public function sel_data()
    {
        $where = ['status'=>self::STATUS_OK];
        $res = \DB::table('brands')
            ->select(['id','name','trade_mark','initial'])
            ->where($where)
            ->orderBy('initial')
            ->get();
        foreach ($res as &$v) {
            $v->initial = strtoupper($v->initial);
        }
        return $res;
    }
    /**
     * 品牌基本信息
     */
    public function sel_info($id)
    {
        $res = \DB::table('brands')
            ->select(['id','name','images','trade_mark'])
            ->where(['id'=>$id])
            ->first();
        if(isset($res->images)){
            $res->images = json_decode_photo($res->images);
        }
        return $res;
    }

    /**
     * 公司介绍
     */
    public function sel_introduce($id)
    {
        $res = \DB::table('brands')
            ->select(['id','description','link','honors','service_tel'])
            ->where(['id'=>$id])
            ->first();
        if(isset($res->honors)){
             $res->honors = json_decode_photo($res->honors);
        }
        return $res;
    }



}
