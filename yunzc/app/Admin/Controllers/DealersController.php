<?php

namespace App\Admin\Controllers;

use App\Models\Dealers;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\User;
class DealersController extends Controller
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
        $grid = new Grid(new Dealers);

        $grid->id('Id');
        $grid->uname('联系人姓名');
        $grid->utel('联系人电话');
        $grid->position('职位');
        $grid->attr('经销商属性')->display(function($sta){
            switch($sta){
                case 1:
                    return '一级经销商';
                    break;
                case 2:
                    return '二级经销商';
                    break;
                case 3:
                    return '三级经销商';
                    break;
            }
        });
//        $grid->dealers_name('Dealers name');
//        $grid->jingwei('Jingwei');
        $grid->address('Address');
//        $grid->address2('Address2');
//        $grid->room_city('Room city');
        $grid->ctime('创建时间');
//        $grid->utime('Utime');
        $grid->status('状态')->display(function($sta){
          return $sta == 1?'上架':'下架';
        });
        $grid->pid('服务商')->display(function($uid){
            return User::where('id',$uid)->value('name');
        });

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
        $show = new Show(Dealers::findOrFail($id));

        $show->id('Id');
        $show->uname('Uname');
        $show->utel('Utel');
        $show->position('Position');
        $show->attr('Attr');
        $show->dealers_name('Dealers name');
        $show->jingwei('Jingwei');
        $show->address('Address');
        $show->address2('Address2');
        $show->room_city('Room city');
        $show->ctime('Ctime');
        $show->utime('Utime');
        $show->status('Status');
        $show->pid('Pid');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Dealers);

        $form->text('uname', 'Uname');
        $form->text('utel', 'Utel');
        $form->text('position', 'Position');
        $form->number('attr', 'Attr')->default(1);
        $form->text('dealers_name', 'Dealers name');
        $form->text('jingwei', 'Jingwei');
        $form->text('address', 'Address');
        $form->text('address2', 'Address2');
        $form->number('room_city', 'Room city');
        $form->number('ctime', 'Ctime');
        $form->number('utime', 'Utime');
        $form->number('status', 'Status')->default(1);
        $form->number('pid', 'Pid');

        return $form;
    }
}
