<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Recommend extends Model
{
    use SoftDeletes;
    const NUM = 3;
    protected $dates = ['deleted_at'];
    //

    /**
     * 大咖数据的列表
     * @param $data
     * @return array|int
     */
    public function show()
    {

        $data = \DB::table('recommends')
            ->select(['id','img','title','description'])
            ->where('deleted_at','=',NULL)
            ->orderBy('updated_at','desc')
            ->limit(6)
            ->get();

        return $data;
    }

    /**
     * 大咖数据的展示
     */
    public function getShow($data)
    {
        $res = \DB::table('recommends')
            ->select(['images','reason','img','title','description','introduction'])
            ->where('deleted_at','=',NULL)
            ->where('id',$data)
            ->first();
        if(isset($res)){
            $res->images = json_decode_photo($res->images);
        }
        return $res;
    }

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
}
