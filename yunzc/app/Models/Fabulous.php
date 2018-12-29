<?php
/**
 * 点赞.
 * User: hwj
 * Date: 2018/11/28
 * Time: 15:50
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fabulous extends Model
{
    protected $fillable = ['fabulous_users_id','fabulous_users_yse_id','article_id','status'];

    public function no_read($users_id)
    {
        return \DB::table('fabulouses')
            ->where('fabulous_users_yse_id','=',$users_id)
            ->where('yes_or_r','=',0)
            ->count();
    }

    public function see_no_read($data)
    {
        $data['page'] = ($data['now_page']-1)*10;
        //当前用户未读消息，倒序（时间），未读消息（yse_or_r）,取十条
        $res = \DB::table('fabulouses')
            ->select(['id'])
            ->where('fabulous_users_yse_id','=',$data['users_id'])
            ->where('yes_or_r','=',0)
            ->orderBy('updated_at','desc')
            ->limit(10)
            ->get();
        if(count($res)<10){
            $addData = $this->addData(['num'=>count($res),'page'=>$data['page']]);
            foreach ($addData as $k=>$v)
            {
                $res[] = $addData[$k];
            }
        }
        foreach ($res as $k=>$v)
        {
             $res[$k] = $v->id;
        }
        //修改成已读
        $bool = \DB::table('fabulouses')
            ->whereIn( 'id',$res)
            ->update(['yes_or_r'=>1,'updated_at'=>date('Y-m-d H:i:s')]);

        if($bool)
        {
            //读取数据--点赞用户头像，名称，是点赞还是取消赞，论坛标题
            $info = \DB::table('fabulouses')
                ->select(['users.name','users.img','fabulouses.status','forums.title'])
                ->join('users','users.id','=','fabulouses.fabulous_users_id')
                ->join('forums','forums.id','=','fabulouses.article_id')
                ->whereIn( 'fabulouses.id',$res)
                ->get();
            return $info;
        }
        return new \stdClass();
    }

    protected function addData($data)
    {
        $res1 = \DB::table('fabulouses')
            ->select(['id'])
            ->where('yes_or_r','=',1)
            ->orderBy('updated_at','desc')
            ->offset($data['page'])
            ->limit(10-$data['num'])
            ->get();
        return $res1;
    }

    public function sel_job_recruits_page($data)
    {
        $res = \DB::table('fabulouses')
            ->select(['id'])
            ->where('fabulous_users_yse_id','=',$data['users_id'])
            ->count();
        return $res;
    }
}