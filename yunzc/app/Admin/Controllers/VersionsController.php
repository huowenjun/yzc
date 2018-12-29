<?php
namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Versions;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;


class VersionsController extends Controller
{
    use HasResourceActions;
    /**
     * @var array $term
     * 术语
     */
    public $term = array(
        'header'=>'版本管理',
        'indexDescription'=>'展示',
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


    protected function grid()
    {
        $grid = new Grid(new Versions());
        $grid->server_name('名称');
        $grid->content('内容');
        $grid->now_version_a('安卓版本号');
        $grid->now_version_i('苹果版本号');
        $grid->server_tel('客服电话');
        $grid->disableCreateButton();//去掉添加按钮
        $grid->disableRowSelector();//去掉操作按钮
        $grid->actions(function ($actions) {
            // 隐藏删除按钮
            $actions->disableDelete();
            $actions->disableView();

        });
        return $grid;
    }

    /**
     * 添加与修改页面的数据处理
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Versions());
        $form->text('server_name', 'APP名称');
        $form->image('server_img', 'APPlogo')->uniqueName();
        $form->display('content', '内容');
        $form->display('server_tel', '客服电话');
        $form->text('now_version_a','安卓版本');
        $form->text('now_version_i','苹果版本号');
        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
            $tools->disableDelete();
        });
//添加编辑页按钮
        return $form;
    }

}