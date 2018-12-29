<?php

namespace App\Models;

use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Category
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Category[] $children
 * @property-read \App\Models\Category $parent
 * @mixin \Eloquent
 */
class Category extends Model
{
    //
    use ModelTree, AdminBuilder;

    protected $table = 'categories';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setParentColumn('parent_id');
        $this->setOrderColumn('order');
        $this->setTitleColumn('name');
    }

    function category(){
        return $this->hasOne(Category::class,'id','parent_id')->select('id','name','order')->orderBy('order','asc');
    }
    function tiles(){
        return $this->belongsToMany(Category::class,'category_tile','category_id','tile_id');
    }

    /**
     * 属性
     * @param $data
     * @return \Illuminate\Support\Collection
     */
    public function sel_list($data)
    {
        return \DB::table('categories')
            ->select(['id','name'])
            ->where('parent_id','=',$data)
            ->orderBy('order')
            ->get();
    }


}
