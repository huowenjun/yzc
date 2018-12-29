<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function success($data = [])
    {
        return response()->json([
            'code'    => 200,
            'message' => config('errorcode.code')[200],
            'data'    => $data,
        ]);
    }

    public function fail($code,  $message = '',$data = '')
    {
        return response()->json([
            'code'    => $code,
            'message' => $message ?  $message : config('errorcode.code')[(int) $code],
            'data'    => new \stdClass(),
        ]);
    }
    public function Logdate()
    {
        return date('Y-m-d H:i:s');
    }



}
