<?php

namespace App\Admin\Controllers;

use App\Models\Suggestions;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\User;
class SuggestionsController extends Controller
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
        $grid = new Grid(new Suggestions);
//        $grid->disableFilter();
        $grid->disableCreateButton();
        $grid->disableExport();
        /*禁用批量操作*/
        $grid->disableRowSelector();
        $grid->actions(function ($actions) {
//            $actions->disableDelete();  /*关闭删除按钮*/
            $actions->disableEdit();    /*关闭编辑按钮*/
        });
        $grid->id('Id');
        $grid->u_id('用户昵称')->display(function($id){
            return User::where('id',$id)->value('name');
        });
        $grid->type('类型')->display(function($val){
            return $val == 1?'投诉':'建议';
        });
        $grid->title('标题');
        $grid->content('反馈描述')->limit(30);
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
        $show = new Show(Suggestions::findOrFail($id));
        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableDelete();
            });
        $show->id('Id');
        $show->u_id('用户昵称')->as(function($id){
            return User::where('id',$id)->value('name');
        });
        $show->type('类型')->as(function($val){
           return $val == 1?'投诉':'建议';
        });
        $show->title('标题');
        $show->content('反馈描述')->limit(50);
        $show->images('图片')->unescape()->as(function($val){
            $imgs = '';
            $path = getenv('APP_URL').'/uploads/';
            if(!empty(is_array($val))){
                foreach($val as $k=>$v){
                    $imgs .="<a href='$path$v'target='_blank'><img style='max-width:200px;max-height:200px;' src='$path$v'></a>";
                }
            }
            return $imgs;
        });
        $show->ctime('创建时间');
        $show->utime('更新时间');


        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Suggestions);

        $form->number('u_id', 'U id');
        $form->number('type', 'Type')->default(1);
        $form->text('title', 'Title');
        $form->textarea('content', 'Content');
        $form->number('ctime', 'Ctime');
        $form->number('utime', 'Utime');
        $form->textarea('images', 'Images');

        return $form;
    }
}
