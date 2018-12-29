<?php
/**
 * 免责说明
 * User: hwj
 * Date: 2018/11/21
 * Time: 17:32
 */
namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Disclaimer;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;


class DisclaimerController extends Controller
{
    use HasResourceActions;
    /**
     * @var array $term
     * 术语
     */
    public $term = array(
        'header'=>'免责说明',
        'indexDescription'=>'展示',
        'createDescription'=>'添加',
        'editDescription'=>'修改',
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
        $form = new Form(new Disclaimer());
        $form->textarea('explain','说明')->rows(100);
        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
            $tools->disableDelete();
        });
        return $form;
    }
    protected function grid()
    {
        $grid = new Grid(new Disclaimer());
        $grid->explain('说明');
        $grid->disableRowSelector();//去掉操作按钮
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->actions(function ($actions) {
            // 隐藏删除按钮
            $actions->disableDelete();
            //$actions->disableShow();
        });
        return $grid;
    }
}