<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Disclaimer;
class DisclaimerController extends Controller
{

    /**
     * Show the application dashboard.
     *免责声明页面
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $info = Disclaimer::first();
        return view('disclaimer',['info'=>$info->toArray()]);
    }
    public  function demo(){
        return view('demo');
    }
}
