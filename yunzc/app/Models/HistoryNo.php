<?php
/**
 * Created by PhpStorm.
 * User: 霍文俊
 * Date: 2018/12/5
 * Time: 10:58
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryNo extends Model
{
    protected $table = 'history_no';

    public function sel_data($data)
    {
        $where = ['status'=>$data['status']];
        return \DB::table('history_no')
            ->select(['id','key','status'])
            ->where($where)
            ->take(5)
            ->orderBy('num','desc')
            ->get();
    }

    public function like_data($data)
    {
        return \DB::table('history_no')
            ->select(['id','key','values','updated_at'])
            ->where(['status'=>$data['status'],'key'=>$data['key']])
            ->orderBy('num','desc')
            ->first();
    }

}