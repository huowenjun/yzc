<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Theme extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    public function forum(){
        return $this->morphMany(Forum::class,'contentable');
    }

    /**
     * 主题论坛列表
     */
    public function sel_data()
    {
        $res = \DB::table('themes')
            ->select(['id','name'])
            ->where('status','=','1')
            ->where('deleted_at','=',Null)
            ->get();
        return $res;
    }
}
