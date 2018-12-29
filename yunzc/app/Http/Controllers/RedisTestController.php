<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class RedisTestController extends Controller
{
    //
    public function test(){
        Redis::set('name', 'Taylor');
        $value = Redis::get('name');

    }
}
