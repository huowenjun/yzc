<?php
/**
 * 热门城市
 * User: hwj
 * Date: 2018/11/21
 * Time: 15:24
 */
namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cities;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;


class CityController extends Controller
{
    use HasResourceActions;
    /**
     * @var array $term
     * 术语
     */
    public $term = array(
        'header'=>'热门城市',
        'indexDescription'=>'展示',
        'editDescription'=>'修改',
    );
    public $statu = array(
        0=>'一般',
        1=>'热门',
    );


    public function index(Content $content)
    {
        return $content
            ->header($this->term['header'])
            ->description($this->term['indexDescription'])
            ->body($this->grid());
    }

    public function edit($id, Content $content)
    {
        return $content
            ->header($this->term['header'])
            ->description($this->term['editDescription'])
            ->body($this->form()->edit($id));
    }

    protected function form()
    {
        $form = new Form(new Cities());
        $form->display('area_name','城市');
        $form->radio('status','是否热门')->options(['0' => '一般', '1'=> '热门']);
        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
            $tools->disableDelete();
        });
        return $form;
    }

    protected function grid()
    {
        $grid = new Grid(new Cities());
        $grid->filter(function($filter){
            $filter->disableIdFilter();
            $filter->like('area_name','城市');
            $filter->equal('status','是否热门')->select($this->statu);
        });
        $grid->id('编号');
        $grid->area_name('城市');
        $grid->status('是否热门');
        $grid->disableCreateButton();//去掉添加按钮
        $grid->disableRowSelector();//去掉操作按钮
        $grid->actions(function ($actions) {
            // 隐藏删除按钮
            $actions->disableDelete();
        });
        return $grid;
    }
}