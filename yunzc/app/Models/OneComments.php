<?php
/**
 * 一级评论
 * User: hwj
 * Date: 2018/11/29
 * Time: 9:47
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OneComments extends Model
{
    protected $fillable = ['forum_id','users_id','content','created_at'];

    /**
     * 查询数据
     */
    public function sel_data($data)
    {
        $data['page'] = ($data['now_page']-1)*3;
        return \DB::table('one_comments')
            ->select(['one_comments.id as one_id','users.id as one_users_id','forum_id','name','img','content','one_comments.created_at','fabulou_num'])
            ->join('users','users.id','=','one_comments.users_id')
            ->where('forum_id','=',$data['forum_id'])
            ->orderBy('one_comments.created_at','desc')
            ->offset($data['page'])
            ->limit(3)
            ->get();
    }
    public function sel_con($data)
    {
        return \DB::table('one_comments')
            ->where('forum_id','=',$data['forum_id'])
            ->count();
    }
    /**
     * 点赞+1
     */
    public function fabulous_inc($data)
    {
        return \DB::table('one_comments')
            ->where('id','=',$data['one_comment_id'])
            ->increment('fabulou_num');
    }

    /**
     * 点赞-1
     */
    public function fabulous_dinc($data)
    {
        return \DB::table('one_comments')
            ->where('id','=',$data['one_comment_id'])
            ->decrement('fabulou_num');
    }
    public function info($data)
    {
        $res = \DB::table('one_comments')
            ->select(['one_comments.id as one_id','users.id as one_users_id','forum_id','name','img','content','one_comments.created_at','fabulou_num'])
            ->join('users','users.id','=','one_comments.users_id')
            ->where('one_comments.id','=',$data['id'])
            ->first();
        if(isset($res))
        {
            $res->sum_comment = \DB::table('two_comments')->where(['parent_id'=>$res->one_id])->count();
        }
        return $res;
    }
}