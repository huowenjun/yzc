<?php

namespace App\Admin\Controllers;

use App\Models\SystemMessage;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\User;
use Illuminate\Http\Request;
class SystemMessageController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SystemMessage);
        //搜索
        $grid->filter(function($filter){

            // 在这里添加字段过滤器
            $filter->like('title', '标题');

        });
        $grid->disableExport();

        $grid->actions(function ($actions) {
            $actions->disableEdit();
        });
        $grid->id('Id');
        $grid->title('标题');
        $grid->ctime('创建时间');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(SystemMessage::findOrFail($id));
        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
            });;
        $show->id('Id');
        $show->title('标题');
        $show->type('消息类型')->as(function($type){
            return $type == 1?'通知消息':'推荐消息';
        });
        $show->img('图片')->image();
        $show->content('消息内容');
        $show->ctime('创建时间');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new SystemMessage);

        $form->text('title', '标题');
        $form->radio('type','消息类型')->options(['1'=>'通知消息',2=>"推荐消息"])->default(1);
        $form->image('img','图片');
        $form->textarea('content', '消息内容');
        $form->footer(function ($footer) {
            $footer->disableEditingCheck();
        });
        $form->saved(function (Form $form) {
            $tit = $form->model()->title;
            $content = $form->model()->content;
            initPush_admin_all($tit,$content);
        });
        return $form;
    }

}
