<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Cities;
class Procurement extends Model
{

    protected $dateFormat = 'U';
    const CREATED_AT = 'ctime';
    const UPDATED_AT = 'utime';
//    protected $dates = [
//        'ctime',
//        'deleted_at'
//    ];
    protected function getCtimeAttribute($time)
    {
        return $time ? date('Y-m-d H:i',$time) : '';
    }
    protected function getUtimeAttribute($time)
    {
        return $time ? date('Y-m-d H:i',$time) : '';
    }
    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = ['u_id','brand','type','models','material','area','room_city','address','brick_time','images'];
    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = ['id','ctime','utime'];
    public function setImagesAttribute($image)
    {
        if (is_array($image)) {
            $this->attributes['images'] = json_encode($image);
        }else{
            $this->attributes['images'] = $image;
        }
    }
    public function getImagesAttribute($image)
    {
        return json_decode($image, true);
    }
    protected function getBrandNameAttribute($id)
    {
        return Brand::where('id',$id)->value('name');
    }
    protected function getTypeNameAttribute($id)
    {
        return Category::where('id',$id)->value('name');
    }
    protected function getModelsNameAttribute($id)
    {
        return Category::where('id',$id)->value('name');
    }
    protected function getMaterialNameAttribute($id)
    {
        return Category::where('id',$id)->value('name');
    }
    protected function getRoomNameAttribute($id)
    {
        return Cities::where('area_code',$id)->value('area_name');
    }
    /*用户信息*/
    public function userinfo(){
        return $data = $this->belongsTo('App\User','u_id','id')->select('id','name','img','tel');
    }
}