<?php

namespace App\Admin\Controllers;

use App\Models\Feedback;
use App\User;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Yankewei\LaravelSensitive\Sensitive;
class FeedbackController extends Controller
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
        $grid = new Grid(new Feedback);
        /*禁用创建按钮*/
        $grid->disableCreateButton();
        /*禁用导出数据按钮*/
        $grid->disableExport();
        /*禁用行操作列*/
        $grid->disableActions();
        $grid->id('Id');
        $grid->u_id('姓名/昵称')->display(function ($userId) {
            return User::where('id',$userId)->value('name');
        });
        $grid->mobile('手机号');
        $grid->content('反馈内容')->popover('auto','反馈内容详情');
        $grid->ctime('创建时间');


        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
//    protected function detail($id)
//    {
//        $show = new Show(Feedback::findOrFail($id));
//
//        $show->id('Id');
//        $show->u_id('U id');
//        $show->textarea('Textarea');
//        $show->ctime('Ctime');
//        $show->mobile('Mobile');
//
//        return $show;
//    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Feedback);
        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
            $tools->disableDelete();
        });
        $form->number('u_id', 'U id');
        $form->text('textarea', 'Textarea');
        $form->number('ctime', 'Ctime');
        $form->mobile('mobile', 'Mobile');

        return $form;
    }
}
