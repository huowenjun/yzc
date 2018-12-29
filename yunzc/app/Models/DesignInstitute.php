<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DesignInstitute
 * @package App\Models
 */
class DesignInstitute extends Model
{
    //

    public function city(){
        return $this->belongsTo(City::class);
    }

    public function setImagesAttribute($pictures)
    {
        if (is_array($pictures)) {
            $this->attributes['images'] = json_encode($pictures);
        }
    }
    protected function getCtimesAttribute($time){
        return strtotime($time);
    }
    public function getImagesAttribute($pictures)
    {
        return json_decode($pictures, true);
    }
}
