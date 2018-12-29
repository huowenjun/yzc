<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dealers extends Model
{
    protected $table = 'dealers';
    protected $dateFormat = 'U';

    const CREATED_AT = 'ctime';
    const UPDATED_AT = 'utime';

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
    protected $fillable = ['pid','uname','utel','position','attr','dealers_name','jingwei','room_city','address','address2','status'];
    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = ['id','ctime','utime'];
    protected function getRoomNameAttribute($id)
    {
        return Cities::where('area_code',$id)->value('area_name');
    }
    protected function getBrandNameAttribute($id)
    {
        return Brand::where('users_id',$id)->value('name');
    }
}
