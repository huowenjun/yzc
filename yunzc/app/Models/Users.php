<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Users extends Model
{
    use SoftDeletes;

    /**
     * 需要被转换成日期的属性。
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * 转态数据的处理
     * @param $status
     * @return string
     */
    public function getStatusAttribute($status)
    {
        return $status ? '商家' : '客户';

    }
    public function generateToken()
    {
        $this->api_token = str_random(60);
        $this->save();

        return $this->api_token;
    }
    public function clearinghouses()
    {
        return $this->hasMany(ClearingHouses::class);
    }

    public function jobrecruits()
    {
        return $this->hasMany(JobRecruits::class);
    }

    public function getrentsetrents()
    {
        return $this->hasMany(GetrentSetrents::class);
    }

    public function agents()
    {
        return $this->hasMany(Agents::class);
    }
    public function searchbricks()
    {
        return $this->hasOne(SearchBricks::class);
    }
    /**
     * 登录
     */
    public function sel_login($data)
    {
        return \DB::table('users')
            ->select(['id','name','status','api_token','tel','img','mobile_id'])
            -> where('openid', '=', $data)
            -> take(1)
            -> get();
    }

    /**
     * 绑定手机号
     */
    public function up_bind($data)
    {
        return DB::table('users')
            ->where('openid','=',$data['openid'])
            ->update(['tel'=>$data['tel'],
                'name'=>$data['tel'],
                'updated_at'=>date('Y-m-d H:i:s'),
                'parent_code'=>$data['parent_code'],
                'referral_code'=>$data['referral_code'],
            ]);
    }
    public function sel_bind($data){
        return \DB::table('users')
            ->select(['id','name','status','api_token','tel','img'])
            ->where('openid','=',$data)
            ->take(1)
            ->get();
    }
    /**
     * 编辑用户信息
     */
    public function sel_usersinfo($data)
    {
        return \DB::table('users')
            ->where('id','=',$data)
            ->take(1)
            ->get();
    }
    public function save_userinfo($data){
        $filed = array();
        foreach ($data as $k=>$v)
        {
            if(!empty($v)){
                $filed[$k]=$v;
            }
        }
        $filed['updated_at'] = date('Y-m-d H:i:s');
        return DB::table('users')
            ->where('id','=',$data['id'])
            ->update($filed);
    }

    public function sel_user($id)
    {
        return \DB::table('users')
            ->select(['id','name','tel','status','img','created_at'])
            ->where('id','=',$id)
            ->first();
    }



}