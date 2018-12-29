<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemMessage extends Model
{
    protected $table='system_message';
    protected $dateFormat = 'U';

    const CREATED_AT = 'ctime';
    const UPDATED_AT = 'utime';

    protected $dates = [
        'ctime',
        'utime'
    ];
    protected function getCtimeAttribute($time)
    {
        return $time ? date('Y-m-d H:i',$time) : '';
    }
    protected function getUtimeAttribute($time)
    {
        return $time ? date('Y-m-d H:i',$time) : '';
    }
    protected function getTypeNameAttribute($sta)
    {
       switch($sta)
       {
           case 1:
               return '通知消息';
           break;
           case 2:
               return '推荐消息';
           break;
       }
    }
    /**
     * 删除
     *
     *@return // NO
     */
    public static function boot()
    {
        parent::boot();

        static::deleted(function ($model)
        {
            //这样可以拿到当前操作id
//            dd($model->id);
            UserMessageLookLog::where('message_id',$model->id)->delete();

        });
    }

}
