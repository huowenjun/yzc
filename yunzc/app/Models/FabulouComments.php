<?php
/**
 * 评论点赞记录
 * User: hwj
 * Date: 2018/11/29
 * Time: 13:19
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FabulouComments extends Model
{
    protected $fillable = ['users_id','one_comment_id','yse_no_fabulou','created_at'];
    public function sel_data($fabulouInfo)
    {
        return \DB::table('fabulou_comments')
            ->where(['users_id'=>$fabulouInfo['users_id'],'one_comment_id'=>$fabulouInfo['one_comment_id']])
            ->first();
    }

    public function up_data($fabulouInfo)
    {
        return \DB::table('fabulou_comments')
            ->where(['users_id'=>$fabulouInfo['users_id'],'one_comment_id'=>$fabulouInfo['one_comment_id']])
            ->update(['yse_no_fabulou'=>$fabulouInfo['yse_no_fabulou'],'updated_at'=>date('Y-m-d H:i:s')]);
    }

    public function sel_yse_no_fabulou($commentInfo){
       return \DB::table('fabulou_comments')
            ->select('yse_no_fabulou')
            ->where(['users_id'=>$commentInfo['users_id'],'one_comment_id'=>$commentInfo['one_comment_id']])
            ->first();
    }

}