<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Member
 *
 * @property int $id
 * @property string $name 用户昵称
 * @property string $tel 手机号
 * @property string $password 密码
 * @property string $head 头像
 * @property string $first_login_at 首次登陆
 * @property string $last_login_at 最后一次登录
 * @property string $account 用户账号
 * @property string $remark 备注
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Member whereAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Member whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Member whereFirstLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Member whereHead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Member whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Member whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Member whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Member wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Member whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Member whereTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Member whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Member extends Model
{
    //
}
