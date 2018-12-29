<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';

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

    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = ['u_id','content','mobile'];
    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = ['id','ctime'];
    public function setContentAttribute($val)
    {

        return   $this->attributes['content'] = htmlspecialchars($val);

    }

}
