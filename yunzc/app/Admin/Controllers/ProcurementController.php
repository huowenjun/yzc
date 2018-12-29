<?php

namespace App\Admin\Controllers;

use App\Models\Procurement;
use App\Models\Category;
use App\Models\Brand;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\User;
use App\Models\Cities;
class ProcurementController extends Controller
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
            ->header('采购快讯-列表')
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
        $grid = new Grid(new Procurement);
        /*禁用创建按钮*/
        $grid->disableCreateButton();
        /*禁用导出数据按钮*/
        $grid->disableExport();
//        $grid->disableRowSelector();
        /*禁用行选择checkbox*/
        $grid->actions(function ($actions) {
            //$actions->disableDelete();  /*关闭删除按钮*/
            $actions->disableEdit();    /*关闭编辑按钮*/
            //$actions->disableView();    /*关闭查看按钮*/
        });
        $grid->filter(function($filter){
            $uids = Procurement::distinct()->pluck('u_id');
            $user_list = \DB::table('users')->where('status',0)->whereIn('id',$uids)->pluck('name','id');
            $filter->equal('u_id','用户')->select($user_list);
            $filter->equal('brick_time','用砖时间')->select(['1'=>'1周','5'=>'1个月','10'=>'3个月']);
            $filter->equal('status','审核状态')->select([ 0 =>'待审核', 1 => '通过', 2 => '未通过',]);
        });
        $grid->id('Id')->sortable();
        $grid->u_id('姓名/昵称')->display(function($userId) {
            return User::where('id',$userId)->value('name');
        });
        $grid->brand('品牌要求')->display(function ($id) {
            return Brand::where('id',$id)->value('name');
        });
        $grid->status('审核')->select([
            0 =>'待审核',
            1 => '通过',
            2 => '未通过',
        ]);
        $grid->area('采购面积')->display(function ($num) {
            return $num.'平米';
        })->sortable();
        $grid->room_city('项目地址')->display(function ($id) {
            return Cities::where('area_code',$id)->value('area_name');
        });
        $grid->address('详细地址');
        $grid->brick_time('用砖时间')->display(function ($num) {
            switch($num){
                case '1':
                    return '1周';
                break;
                case '5':
                    return '1个月';
                    break;
                case '10':
                    return '3个月';
                    break;
            }
        });
        $grid->ctime('创建时间')->sortable();

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
        $show = new Show(Procurement::findOrFail($id));
        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                //$tools->disableDelete();
            });
        $show->id('Id');
        $show->u_id('姓名/昵称')->as(function($val){
            return User::where('id',$val)->value('name');
        });
        $show->brand('品牌要求')->as(function($id){
            return  Brand::find($id)->name;
        });
        $show->type('瓷砖类型')->as(function($id){
            return  Category::find($id)->name;
        });
        $show->models('型号要求')->as(function($id){
            return  Category::find($id)->name;
        });
        $show->material('材质要求')->as(function($id){
            return  Category::find($id)->name;
        });
        $show->area('采购面积')->as(function($val){
            return $val.'平米';
        });
        $show->room_city('所在地区')->as(function($val){
            return  Cities::where('area_code',$val)->value('area_name');
        });
        $show->address('详细地址');
        $show->brick_time('用砖时间')->as(function ($num) {
            switch($num){
                case '1':
                    return '1周';
                    break;
                case '5':
                    return '1个月';
                    break;
                case '10':
                    return '3个月';
                    break;
            }
        });
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
//        $show->utime('Utime');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Procurement);

        $form->number('u_id', 'U id');
        $form->number('brand', 'Brand');
        $form->number('type', 'Type');
        $form->number('models', 'Models');
        $form->number('material', 'Material');
        $form->decimal('area', 'Area');
        $form->radio('status', '审核')->options(['0' => '待审核', '1'=> '通过','2'=>'未通过'])->default('0');
        $form->number('room_city', 'Room city');
        $form->text('address', 'Address');
        $form->number('brick_time', 'Brick time')->default(1);
        $form->text('images', 'Images');
        $form->number('ctime', 'Ctime');
        $form->number('utime', 'Utime');

        return $form;
    }
}
