<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertising extends Model
{
    public function sel_data()
    {
        $res = \DB::table('advertisings')
            ->select(['title','img','link',])
            ->where('status','=','1')
            ->orderBy('updated_at','desc')
            ->get();
        return $res;
    }
    public function sel_count()
    {
        $res = \DB::table('advertisings')
           ->count();
        return $res;
    }

}
