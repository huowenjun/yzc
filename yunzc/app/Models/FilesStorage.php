<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FilesStorage extends Model
{
    //
    protected $fillable = ['originalName','ext','type','realPath','fileName'];
}
