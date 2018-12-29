<?php
/**
 * Created by PhpStorm.
 * User: hwj
 * Date: 2018/11/15
 * Time: 13:38
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Agents extends Model
{
    use SoftDeletes;

    /**
     * 需要被转换成日期的属性。
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    protected $fillable = ['users_id','type','company','brand','room_city','acreage','policy','photo'];
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
    /**
     * 转态数据的处理
     * @param $status
     * @return string
     */
    public function getTypeAttribute($type)
    {
        return $type == 1 ? '招分销' : '招总代';
    }

    /**
     * int(0:待审核，1：审核通过，2：未通过)
     */
    public function getExamineAttribute($examines)
    {
        $examine = [0=>'待审核',1=>'通过',2=>'未通过'];
        return $examine[$examines];
    }
    public function sel_agents_info($id)
    {
        $res = \DB::table('agents')
            ->join('cities','cities.area_code','=','agents.room_city')
            ->select(['type','company','brand','area_name','acreage','policy','photo','agents.created_at'])
            ->where('agents.deleted_at','=',NULL)
            ->where('examine','=','1')
            ->where('agents.id',$id)
            ->first();
        if(isset($res)){
            $res->photo = json_decode_photo($res->photo);
        }
        return $res;
    }

    public function sel_agents_list($data)
    {
        $data['page'] = ($data['now_page']-1)*10;
        if(isset($data['f'])){
            $res = \DB::table('agents')
                ->join('cities','cities.area_code','=','agents.room_city')
                ->select(['agents.id','type','company','brand','area_name','acreage','policy','photo'])
                ->where('users_id','=',$data['users_id'])
                ->where('agents.deleted_at','=',NULL)
                ->where('examine','=','1')
                ->orderBy('agents.updated_at','desc')
                ->offset($data['page'])
                ->limit(10)
                ->get();
        }else{
            $res = \DB::table('agents')
                ->join('cities','cities.area_code','=','agents.room_city')
                ->select(['agents.id','type','company','brand','area_name','acreage','policy','photo'])
                ->where('type','=',$data['type'])
                ->where('agents.deleted_at','=',NULL)
                ->where('examine','=','1')
                ->where('agents.room_city','=',$data['room_city'])
                ->orderBy('agents.updated_at','desc')
                ->offset($data['page'])
                ->limit(10)
                ->get();
        }
        if(isset($res[0])){
            foreach ($res as $k=>$v)
            {
                $res[$k]->photo = json_decode_photo($res[$k]->photo);
            }
        }

        return $res;
    }
    public function sel_agents_page($data)
    {
        if(isset($data['f'])){
            return \DB::table('agents')
                ->join('cities','cities.area_code','=','agents.room_city')
                ->select(['agents.id','type','company','brand','area_name','acreage','policy','photo'])
                ->where('agents.deleted_at','=',NULL)
                ->where('examine','=','1')
                ->count();
        }
        return \DB::table('agents')
            ->join('cities','cities.area_code','=','agents.room_city')
            ->select(['agents.id','type','company','brand','area_name','acreage','policy','photo'])
            ->where('agents.deleted_at','=',NULL)
            ->where('examine','=','1')
            ->where('agents.room_city','=',$data['room_city'])
            ->count();
    }

    public function del_my_data($id)
    {
        $date = date('Y-m-d H:i:s');
        return \DB::table('agents')
            ->where(['id'=>$id])
            ->update(['deleted_at'=>$date,'updated_at'=>$date]);
    }
}