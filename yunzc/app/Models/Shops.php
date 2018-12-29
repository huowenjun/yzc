<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/15
 * Time: 9:50
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Shops extends Model
{
    use SoftDeletes;

    /**
     * 需要被转换成日期的属性。
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function getrentsetrents()
    {
        return $this->hasOne(GetrentSetrents::class);
    }

    public function show()
    {
        return \DB::table('shops')
            ->select(['id','shop_type'])
            ->where('deleted_at','=',NULL)
            ->get();
    }
}