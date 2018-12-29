<?php

namespace App\Admin\Controllers;

use App\Models\ProjectCooperation;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\User;
use App\Models\Cities;
class ProjectCooperationController extends Controller
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
            ->header('项目合作-列表')
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
            ->header('详情')
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
        $grid = new Grid(new ProjectCooperation);
        /*禁用创建按钮*/
        $grid->disableCreateButton();
        /*禁用导出数据按钮*/
        $grid->disableExport();
//        $grid->disableRowSelector();
        /*禁用行选择checkbox*/
        $grid->actions(function ($actions) {
//            $actions->disableDelete();  /*关闭删除按钮*/
            $actions->disableEdit();    /*关闭编辑按钮*/
//            $actions->disableView();    /*关闭查看按钮*/
        });
        $grid->filter(function($filter){
            $uids = ProjectCooperation::distinct()->pluck('u_id');
            $user_list = \DB::table('users')->where('status',0)->whereIn('id',$uids)->pluck('name','id');
            $filter->equal('u_id','用户')->select($user_list);
            $filter->like('title', '项目名称');
            $filter->like('content', '项目描述');
            $filter->equal('status','审核状态')->select([ 0 =>'待审核', 1 => '通过', 2 => '未通过']);
        });
        $grid->id('Id');
        $grid->u_id('姓名/昵称')->display(function($userId) {
            return User::find($userId)->name;
        });
        $grid->title('项目名称')->limit(50);
        $grid->status('审核')->select([
            0 =>'待审核',
            1 => '通过',
            2 => '未通过',
        ]);
        $grid->content('项目描述')->limit(50);
        $grid->room_city('所在地区')->display(function($id) {
            $city = Cities::where('area_code',$id)->value('area_name');
            return $city;
        });
        $grid->address('详细地址');
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
        $show = new Show(ProjectCooperation::findOrFail($id));
        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
//                $tools->disableDelete();
            });
        $show->id('Id');
        $show->u_id('姓名/昵称')->as(function($val){
            return User::where('id',$val)->value('name');
        });
        $show->title('项目名称');
        $show->content('项目描述')->unescape();
        $show->room_city('所在地区')->as(function($val){
            return  Cities::where('area_code',$val)->value('area_name');
        });
        $show->address('详细地址');
        $show->status('审核')->as(function($val){
            switch($val){
                case 0:
                    return '待审核';
                    break;
                case 1:
                    return '审核通过';
                    break;
                case 2:
                    return '审核未通过';
                    break;
            }
        });
        $show->images('参考图片')->unescape()->as(function($val){
            $imgs = '';
            $path = getenv('APP_URL').'/uploads/';
            foreach($val as $k=>$v){
                $imgs .="<a href='$path$v'target='_blank'><img style='max-width:200px;max-height:200px;' src='$path$v'></a>";
            }
            return $imgs;
        });
        $show->ctime('创建时间');
//        $show->utime('更新时间');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ProjectCooperation);

        $form->text('title', 'Title');
        $form->textarea('content', 'Content');
        $form->number('room_city', 'Room city');
        $form->radio('status', '审核')->options(['0' => '待审核', '1'=> '通过','2'=>'未通过'])->default('0');
        $form->text('address', 'Address');
        $form->textarea('images', 'Images');
        $form->number('ctime', 'Ctime');
        $form->number('utime', 'Utime');

        return $form;
    }
}
