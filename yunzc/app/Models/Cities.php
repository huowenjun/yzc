<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cities extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function getStatusAttribute($status)
    {
        if ($status == 1) {
            return '热门';
        } elseif($status == 0) {
            return '一般';
        }
    }
}
