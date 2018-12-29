<?php
/**
 * Created by PhpStorm.
 * User: 霍文俊
 * Date: 2018/12/3
 * Time: 14:37
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class BrandEvaluates extends Model
{
    //
    protected $fillable = ['users_id','content','grade','brand_id','photo'];

}
