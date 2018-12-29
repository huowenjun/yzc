<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Disclaimer extends Model
{
    public function sel_data()
    {
        return \DB::table('disclaimers')
            ->select('explain')
            ->get();
    }
}
