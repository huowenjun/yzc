<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suggestions extends Model
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
    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = ['u_id','type','title','content','images'];
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
}
