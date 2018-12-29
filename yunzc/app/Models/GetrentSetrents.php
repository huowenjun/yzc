<?php
/**
 * Created by PhpStorm.
 * User: hwj
 * Date: 2018/11/15
 * Time: 10:57
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class GetrentSetrents extends Model
{
    use SoftDeletes;

    /**
     * 需要被转换成日期的属性。
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    protected $fillable = ['users_id','type','acreage','money','room_city','address','start_time','set_money','shops_id','matching','contacts_name','contacts_tel','photo'];

    /**
     * 关联用户表
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function users()
    {
        return $this->belongsTo(Users::class);
    }
    public function getPhotoAttribute($image)
    {
        return json_decode($image, true);
    }
    public function shops()
    {
        return $this->belongsTo(Shops::class);
    }

    /**&
     * 类型数据的处理
     * @param $type
     * @return string
     */
    public function getTypeAttribute($type)
    {
        return $type == 1 ? '店铺寻租' : '店铺转租';

    }

    /**
     * int(0:待审核，1：审核通过，2：未通过)
     */
    public function getExamineAttribute($examine)
    {
        switch ($examine){
            case 0:
                return'待审核';
                break;
            case 1:
                return '通过';
                break;
            case 2:
                return '未通过';
                break;
        }
    }

    /**
     * 查询单条
     */
    public function sel_getrent_setrents_info($data)
    {
        $res = \DB::table('getrent_setrents')
            ->join('cities','cities.area_code','=','getrent_setrents.room_city')
            ->join('shops','shops.id','=','getrent_setrents.shops_id')
            ->select(['type','acreage','money','area_name','address','start_time','set_money','shop_type','matching','contacts_name','contacts_tel','photo','getrent_setrents.created_at'])
            ->where('getrent_setrents.deleted_at','=',NULL)
            ->where('getrent_setrents.id','=',$data)
            ->where('examine','=','1')
            ->first();
        if(isset($res))
        {
            $res->photo = json_decode_photo($res->photo);
        }
        return $res;
    }
    /**
     * 查询列表
     */
    public function sel_getrent_setrents_list($data)
    {
        $data['page'] = ($data['now_page']-1)*10;
        $where = array(
            'getrent_setrents.deleted_at'=>NULL,
            'getrent_setrents.examine'=>'1',
        );
        if(isset($data['f'])){
            $where['users_id']=$data['users_id'];
            $res =  \DB::table('getrent_setrents')
                ->join('cities','cities.area_code','=','getrent_setrents.room_city')
                ->join('shops','shops.id','=','getrent_setrents.shops_id')
                ->select(['getrent_setrents.id','type','acreage','money','area_name','address','start_time','set_money','shop_type','matching','contacts_name','contacts_tel','photo'])
                ->where($where)
                ->orderBy('getrent_setrents.updated_at','desc')
                ->offset($data['page'])
                ->limit(10)
                ->get();
        }else{
            $where['type']=$data['type'];
            if($data['room_city']!=0)
            {
                $where['getrent_setrents.room_city']=$data['room_city'];
            }
            $res =  \DB::table('getrent_setrents')
                ->join('cities','cities.area_code','=','getrent_setrents.room_city')
                ->join('shops','shops.id','=','getrent_setrents.shops_id')
                ->select(['getrent_setrents.id','type','acreage','money','area_name','address','start_time','set_money','shop_type','matching','contacts_name','contacts_tel','photo'])
                ->where($where)
                ->orderBy('getrent_setrents.updated_at','desc')
                ->offset($data['page'])
                ->limit(10)
                ->get();
        }
        if(!isset($res[0])){
            return array();
        }
        foreach ($res as $k=>$v)
        {
            $res[$k]->photo = json_decode_photo($res[$k]->photo);
        }
        return $res;
    }
    public function sel_getrent_setrents_page($data)
    {
        if(isset($data['f']))
        {
            return \DB::table('getrent_setrents')
                ->join('cities','cities.area_code','=','getrent_setrents.room_city')
                ->join('shops','shops.id','=','getrent_setrents.shops_id')
                ->where('users_id','=',$data['users_id'])
                ->where('getrent_setrents.deleted_at','=',NULL)
                ->where('getrent_setrents.examine','=','1')
                ->count();
        }else{
            return \DB::table('getrent_setrents')
                ->join('cities','cities.area_code','=','getrent_setrents.room_city')
                ->join('shops','shops.id','=','getrent_setrents.shops_id')
                ->where('type','=',$data['type'])
                ->where('getrent_setrents.deleted_at','=',NULL)
                ->where('getrent_setrents.examine','=','1')
                ->where('getrent_setrents.room_city','=',$data['room_city'])
                ->count();
        }

    }
    public function del_my_data($id)
    {
        $date = date('Y-m-d H:i:s');
        return \DB::table('getrent_setrents')
            ->where(['id'=>$id])
            ->update(['deleted_at'=>$date,'updated_at'=>$date]);
    }
}