<?php
/**
 * Created by PhpStorm.
 * User: 霍文俊
 * Date: 2018/12/5
 * Time: 10:58
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryYes extends Model
{
    protected $table = 'history_yes';

    public function sel_data($data)
    {
        foreach ($data as $k=>$v){
            $where[$k] =$v;
        }
        unset($where['api_token']);
        return \DB::table('history_yes')
            ->select(['id','key','status'])
            ->where($where)
            ->take(5)
            ->orderBy('num','desc')
            ->get();
    }

    public function like_data($data)
    {
        $where = [
            'status'=>$data['status'],
            'key'=>$data['key'],
            'users_id'=>$data['users_id']
        ];
        return \DB::table('history_yes')
            ->select(['id','key','values','updated_at'])
            ->where($where)
            ->orderBy('num','desc')
            ->first();
    }

}