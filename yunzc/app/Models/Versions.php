<?php
/**
 * 版本控制--关于我们
 * User: hwj
 * Date: 2018/11/24
 * Time: 10:37
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Versions extends Model
{
    public function sel_data($num)
    {
        $db = \DB::table('versions')
        ->select(['content','now_version_a','now_version_i','server_tel'])
        ->first();
        if($num == 1){
            unset($db->now_version_i);
        }else{
            unset($db->now_version_a);
        }
        return $db;
    }

    public function up_data($data)
    {
        $db = \DB::table('versions');
        if($data['num'] == 0)
        {
            $db->update(['now_version_i'=>$data['version']]);
        }elseif($data['num'] == 1){
            $db->update(['now_version_a'=>$data['version']]);
        }
        return $db;
    }
}