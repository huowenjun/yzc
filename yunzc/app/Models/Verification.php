<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Verification
 *
 * @property int $id
 * @property string $code 验证码
 * @property int $type 类型：1=注册，2=登陆，3=找回密码
 * @property string $expiration 到期时间
 * @property string $tel 手机号
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Verification whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Verification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Verification whereExpiration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Verification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Verification whereTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Verification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Verification whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Verification extends Model
{
    //
    protected $fillable = ['code','type','tel','expiration'];
}
