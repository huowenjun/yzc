<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AipImageSearchLog extends Model
{
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
    protected function setUtimeAttribute($time)
    {
        return time();
    }
    protected $table='aip_image_search_log';
    protected $fillable = ['log_id','cont_sign','error_code','error_msg','address','images'];
    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = ['id','ctime','utime'];
}
