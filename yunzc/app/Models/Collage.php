<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Collage extends Model
{
    use SoftDeletes;
    /**
     * 模型的日期字段的存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';

//    /**
//     * 该模型是否被自动维护时间戳
//     *
//     * @var bool
//     */
//    public $timestamps = false;
    protected function setStimeAttribute($time)
    {
        return $this->attributes['stime'] =strtotime($time);

    }
    protected function setEtimeAttribute($time)
    {
        return $this->attributes['etime'] =strtotime($time);
    }
    const CREATED_AT = 'ctime';
    const UPDATED_AT = 'utime';
    /**
     * 需要被转换成日期的属性。
     *
     * @var array
     */
    protected $dates = [
        'ctime',
        'utime',
        'deleted_at',
        'stime',
        'etime'
    ];
    //
    protected function getNumsAttribute($num)
    {
        if($num < 10000){
            return strval($num);
        }elseif($num>=10000 && $num<100000000){
            return ceil($num/10000).'万';
        }elseif($num >= 100000000){
            return ceil($num/100000000).'亿';
        }
    }
    protected function getCtimeAttribute($time)
    {
        return $time ? date('Y-m-d H:i:s',$time) : '';
    }
    protected function getUtimeAttribute($time)
    {
        return $time ? date('Y-m-d H:i:s',$time) : '';
    }
    protected function getStimeAttribute($time)
    {
        return $time ? date('Y-m-d H:i:s',$time) : '';
    }
    protected function getEtimeAttribute($time)
    {
        return $time ? date('Y-m-d H:i:s',$time) : '';
    }
//    protected function getStatusAttribute($val)
//    {
//        return $val == 1?'上架':'下架';
//    }
    protected function getUnitAttribute($val)
    {
        return $val?$val:'个';
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
    public function setGoodsImagesAttribute($pictures)
    {
        if (is_array($pictures)) {
            $this->attributes['goods_images'] = json_encode($pictures);
        }
    }

    public function getGoodsImagesAttribute($pictures)
    {
        return json_decode($pictures, true);
    }
    public function setContentAttribute($val)
    {

        return   $this->attributes['content'] = htmlspecialchars($val);

    }
    public function cities()
    {
        return $data = $this->hasOne('App\Models\Cities','area_code','room_city');

     }
    /*拼单列表*/
    public function single_list()
    {
        return $data = $this->hasMany('App\Models\CollagesUserList','g_id','id')->select('num','id','u_id','g_id')->orderBy('num','DESC')->limit(6);
    }
    /*商家信息*/
    public function merchant(){
        return $data = $this->hasOne('App\User','id','s_uid')->select('id','name','img','security','memo');
    }

    public function search_datas($arr)
    {
        if(!isset($arr['key'])||$arr['key']=='')
        {
            return new \stdClass();
        }
        return \DB::table('collages')
            ->select(['id','title','photo','description','num as nums','discounts','room_city','unit','symbol','status'])
            ->where('title','like','%'.$arr['key'].'%')
            ->where(['status'=>1])
            ->orderBy('sort','asc')
            ->get();
    }
    public function search_data($arr)
    {
        $arr['page'] = ($arr['now_page']-1)*10;
        if(!isset($arr['key'])||$arr['key']=='')
        {
            return new \stdClass();
        }
        return \DB::table('collages')
            ->select(['id','title','photo','description','num as nums','discounts','room_city','unit','symbol','status'])
            ->where('title','like','%'.$arr['key'].'%')
            ->where(['status'=>1])
            ->orderBy('sort','asc')
            ->offset($arr['page'])
            ->limit(10)
            ->get();
    }
    public function search_count($key)
    {
        if(!isset($key)||$key=='')
        {
            return 0;
        }

        return \DB::table('collages')
            ->where('title','like','%'.$key.'%')
            ->where(['status'=>1])
            ->count();

    }

}
