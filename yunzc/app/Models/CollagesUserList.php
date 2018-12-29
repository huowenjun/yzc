<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CollagesUserList extends Model
{
    use SoftDeletes;

    protected $table = 'collages_user_list';

    protected $dateFormat = 'U';

    const CREATED_AT = 'ctime';
    const UPDATED_AT = 'utime';

    protected $dates = [
        'ctime',
        'deleted_at'
    ];
    protected function getCtimeAttribute($time)
    {
        return $time ? date('Y-m-d H:i',$time) : '';
    }
    protected function getUtimeAttribute($time)
    {
        return $time ? date('Y-m-d H:i',$time) : '';
    }
    public function cities()
    {
        return $data = $this->hasOne('App\Models\Cities','area_code','room_city');

    }
    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = ['u_id','g_id','num','mobile'];
    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = ['id','ctime','status','deleted_at'];
    /*用户信息*/
    public function userinfo(){
        return $data = $this->belongsTo('App\User','u_id','id')->select('id','name','img');
    }
    /*商品信息*/
    public function goodsinfo(){
        return $data = $this
            ->belongsTo('App\Models\Collage','g_id','id')
            ->select('id','title','photo','description','num as nums','discounts','room_city','unit','symbol','status','stime as stimes','etime as etimes');
    }
}
