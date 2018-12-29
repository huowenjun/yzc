<?php
/**
 * 免责说明
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Disclaimer;


class DisclaimerController extends Controller
{

    public function index()
    {
        $disclaimerObj = new Disclaimer();
        return msg_ok(PassportController::YSE_STATUS,'免责说明',arr($disclaimerObj->sel_data()));
    }

}