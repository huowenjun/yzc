<?php

namespace App\Admin\Controllers;

use App\Models\CollagesUserList;
use App\User;
use App\Models\Collage;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CollagesUserListController extends Controller
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
//    public function create(Content $content)
//    {
//        return $content
//            ->header('Create')
//            ->description('description')
//            ->body($this->form());
//    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CollagesUserList);
        /*筛选*/

        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $uids = CollagesUserList::distinct()->pluck('u_id');

            $user_list = \DB::table('users')->where('status',0)->whereIn('id',$uids)->pluck('name','id');
            $filter->equal('u_id','用户')->select($user_list);
            // 在这里添加字段过滤器
            $goods_list = Collage::pluck('title','id');
            $filter->equal('g_id', '拼单商品')->select($goods_list);
            $filter->like('mobile', '手机号');
            $filter->scope('deleted_at', '被软删除的数据')->onlyTrashed();

        });
        /*禁用创建按钮*/
        $grid->disableCreateButton();
        /*禁用导出数据按钮*/
        $grid->disableExport();
        /*禁用行操作列*/
//        $grid->disableActions();
        /*禁用行选择checkbox*/
        $grid->actions(function ($actions) {
            $actions->disableDelete();  /*关闭删除按钮*/
            $actions->disableEdit();    /*关闭编辑按钮*/
//            $actions->disableView();    /*关闭查看按钮*/
        });
        $grid->disableRowSelector();
        $grid->id('Id');
        $grid->u_id('姓名/昵称')->display(function($userId) {

            return User::where('id',$userId)->value('name')?:'用户不存在';
        });
        $grid->g_id('商品')->display(function($id) {
            return Collage::find($id)->title;
        });
        $grid->num('拼单数量')->sortable();
        $grid->mobile('手机号');
        $grid->status('状态')->select([
            1 => '拼单进行中',
            5 => '拼单成功',
            10 => '拼单失败',
        ]);
        $grid->ctime('拼单时间');
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
        $show = new Show(CollagesUserList::findOrFail($id));
        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableDelete();
            });;
        $show->id('ID');
        $show->u_id('姓名/昵称')->as(function ($userId) {
            return User::find($userId)->name;
        });

        $show->g_id('商品')->as(function($id) {
            return Collage::find($id)->title;
        });
        $show->num('拼单数量');
        $show->mobile('手机号');
        $show->status('拼单状态')->as(function($sta){
            switch($sta){
                case '1':
                    return "拼单进行中";
                    break;
                case '5':
                    return "拼单成功";
                    break;
                case '10':
                    return "拼单失败";
                    break;
            }
        });
        $show->ctime('拼单时间');
//        $show->deleted_at('Deleted at');
        $show->utime('修改时间');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CollagesUserList);
        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
            $tools->disableDelete();
        });
//        $form->number('u_id', 'U id');
//        $form->number('g_id', 'G id');
//        $form->number('num', 'Num');
//        $form->mobile('mobile', 'Mobile');
//        $form->number('status', 'Status')->default(1);
        $form->select('status','状态')->options([
            1 => '拼单进行中',
            5 => '拼单成功',
            10 => '拼单失败',
        ]);
        return $form;
    }
}