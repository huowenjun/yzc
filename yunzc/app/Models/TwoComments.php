<?php
/**
 * 二级评论
 * User: hwj
 * Date: 2018/11/29
 * Time: 9:47
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwoComments extends Model
{
    protected $fillable = ['parent_id','users_id','content','created_at'];

    public function sel_data($data)
    {
        $data['page'] = ($data['now_page']-1)*$data['size'];
        $res = \DB::table('two_comments')
            ->select(['two_comments.id','forum_id','parent_id','name','content','two_comments.created_at'])
            ->join('users','users.id','=','two_comments.users_id')
            ->where('parent_id','=',$data['parent_id'])
            ->offset($data['page'])
            ->limit($data['size'])
            ->get();
        return $res;
    }

    public function sel_con($data)
    {
        return \DB::table('two_comments')
            ->where('parent_id','=',$data['parent_id'])
            ->count();
    }

    public function count_no_read($data)
    {
        return \DB::table('two_comments')
            ->where(['one_users_id'=>$data['one_users_id'],'status'=>0])
            ->count();
    }

    public function no_sel_data($data)
    {
        $data['page'] = ($data['now_page']-1)*5;
        $res = \DB::table('two_comments')
        ->select(['two_comments.id','forum_id','forums.title','parent_id','name','img','two_comments.content','two_comments.created_at','two_comments.status','two_comments.one_users_id'])
        ->join('users','users.id','=','two_comments.users_id')
        ->join('forums','forums.id','=','two_comments.forum_id')
        ->where('two_comments.status','=','0')
        ->where('one_users_id','=',$data['one_users_id'])
        ->orderBy('two_comments.created_at','desc')
        ->limit(5)
        ->get();
        if(count($res)<5){
            $addData = $this->addData(['num'=>count($res),'page'=>$data['page'],'one_users_id'=>$data['one_users_id']]);

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
        $bool = \DB::table('two_comments')
            ->whereIn( 'id',$res)
            ->update(['status'=>1,'updated_at'=>date('Y-m-d H:i:s')]);
        if($bool)
        {
            //读取数据--点赞用户头像，名称，是点赞还是取消赞，论坛标题
            $res = \DB::table('two_comments')
                ->select(['two_comments.id','forum_id','forums.title','parent_id','name','img','two_comments.content','two_comments.created_at','two_comments.status','two_comments.one_users_id'])
                ->join('users','users.id','=','two_comments.users_id')
                ->join('forums','forums.id','=','two_comments.forum_id')
                ->orderBy('two_comments.created_at','desc')
                ->whereIn( 'two_comments.id',$res)
                ->get();
            return $res;
        }
        return array();
    }

    protected function addData($data){

        return \DB::table('two_comments')
            ->select(['two_comments.id','forums.title','parent_id','name','img','two_comments.content','two_comments.created_at','two_comments.status'])
            ->join('users','users.id','=','two_comments.users_id')
            ->join('forums','forums.id','=','two_comments.forum_id')
            ->where('one_users_id','=',$data['one_users_id'])
            ->where('two_comments.status','=','1')
            ->orderBy('two_comments.created_at','desc')
            ->offset($data['page'])
            ->limit(5-$data['num'])
            ->get();
    }

    public function no_sel_con($data)
    {
        return \DB::table('two_comments')
            ->where('one_users_id','=',$data['one_users_id'])
            ->count();
    }
}