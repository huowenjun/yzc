<?php
/**
 * Created by PhpStorm.
 * User: hwj
 * Date: 2018/11/15
 * Time: 9:44
 */
namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Shops;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;


class ShopsController extends Controller
{
    use HasResourceActions;

    /**
     * @var array $term
     * 术语
     */
    public $term = array(
        'header'=>'店铺类型',
        'indexDescription'=>'展示',
        'showDescription'=>'查看',
        'createDescription'=>'添加',
        'editDescription'=>'修改',
        'id'=>'编号',
        'shop_type'=>'店铺类型',
        'created_at'=>'添加时间',
    );

    /**
     * 店铺类型展示
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header($this->term['header'])
            ->description($this->term['indexDescription'])
            ->body($this->grid());
    }

    /**
     * 店铺类型添加
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header($this->term['header'])
            ->description($this->term['createDescription'])
            ->body($this->form());
    }

    /**
     * 店铺类型的修改
     * @param $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header($this->term['header'])
            ->description($this->term['editDescription'])
            ->body($this->form()->edit($id));
    }

    public function show($id, Content $content)
    {
        return $content
            ->header($this->term['header'])
            ->description($this->term['showDescription'])
            ->body($this->detail($id));
    }

    protected function detail($id)
    {
        $show = new Show(Shops::findOrFail($id));
        $show->shop_type($this->term['shop_type']);
        return $show;
    }

    protected function grid()
    {
        $grid = new Grid(new Shops());
        $grid->filter(function($filter){
            $filter->disableIdFilter();
            $filter->like('shop_type',$this->term['shop_type']);
        });
        $grid->id($this->term['id']);
        $grid->shop_type($this->term['shop_type']);
        $grid->created_at($this->term['created_at']);
        return $grid;
    }

    protected function form()
    {
        $form = new Form(new Shops());
        $form->text('shop_type',$this->term['shop_type']);
        return $form;
    }
}