<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carousel extends Model
{
    //
    public function show($data)
    {
        $res = \DB::table('carousels')
            ->select(['id','img','link','description'])
            ->where('type','=',$data)
            ->get();
        $carousel_count = count($res);
        $carousel = array(
            'carousel_data'=>$res,
            'carousel_count'=>$carousel_count,
        );
        return $carousel;
    }
}
