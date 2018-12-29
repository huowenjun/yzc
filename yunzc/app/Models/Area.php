<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Area
 *
 * @property int $id
 * @property string $area_name
 * @property string $area_code
 * @property int $level
 * @property string $city_code
 * @property string $center
 * @property int $parent_id
 * @property string|null $letter
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Area whereAreaCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Area whereAreaName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Area whereCenter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Area whereCityCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Area whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Area whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Area whereLetter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Area whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Area whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Area whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Area extends Model
{
    //
}
