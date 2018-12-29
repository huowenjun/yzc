<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\City
 *
 * @property int $id
 * @property string $area_name
 * @property string $area_code
 * @property int $level
 * @property string $city_code
 * @property string $center
 * @property int $parent_id
 * @property string $letter
 * @property string $initial
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City whereAreaCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City whereAreaName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City whereCenter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City whereCityCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City whereInitial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City whereLetter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\City whereUpdatedAt($value)
 *
 *

 */
class City extends Model
{
    //
    protected $fillable = ['area_name','level','city_code','area_code','center','parent_id','letter','initial'];
    public function designInstitute(){
        return $this->hasMany(DesignInstitute::class);
    }

    public function forum(){
        return $this->morphMany(Forum::class,'contentable');
    }
}
