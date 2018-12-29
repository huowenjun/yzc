<?php
/**
 * Created by PhpStorm.
 * User: liule
 * Date: 2018/10/19
 * Time: 17:15
 */

namespace App\Admin\Controllers;


use App\Models\City;
use Illuminate\Http\Request;

class ApiController
{

    public function cities(Request $request)
    {
        $q = $request->get('q');

        return City::where('area_name', 'like', "%$q%")->paginate(null, ['id', 'area_name as text']);
    }
}