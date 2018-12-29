<?php

namespace App\Admin\Controllers;

use App\Models\Collage;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CollageController extends Controller
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

        $grid = new Grid(new Collage);
        $grid->actions(function ($actions) {
//            $actions->disableDelete(); //禁用删除按钮
//            $actions->disableEdit(); // 禁用修改按钮
//            $actions->disableView(); //禁用查看详情按钮 （小眼睛）
//            $actions->disable();
        });
        $grid->id('ID');
        $grid->title('标题');
        $grid->num('拼单数量');
        $grid->status('状态')->display(function ($name) {
            return $name == 1?'上架':'下架';
        });
        $grid->stime('开始时间');
        $grid->etime('结束时间');
        $grid->ctime('创建时间');
        $grid->photo()->image('', 50, 50);
//        $grid->images()->image();
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
        $show = new Show(Collage::findOrFail($id));

        $show->title('标题');
        $show->photo('封面')->image();
//        $show->images('商品banner')->display(function ($name) {
//            $img = '';
////            for($i=1;$i<=count($name);$i++){
////                $img.='<img class=\'label\' src="'.$name[$i].'">';
////            }
//            return  $img;
//        });
        $show->keywords('搜索关键词');
        $show->description('简介');
        $show->content('商品详情');

        $show->status('上架')->as(function($sta){
            return $sta == 1?'上架':'下架';
        });
        $show->stime('开始时间');
        $show->etime('结束时间');
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
        $form = new Form(new Collage);

        $form->text('title','标题')->placeholder("请输入标题")->rules('required');
        $form->textarea('keywords','搜索关键词')->placeholder("请输入搜索关键词,搜索条件（标题+搜索关键词）")->rules('max:255');
        $form->text('description','简介')->setWidth(8,2)->placeholder("商品的简介")->rules('required');
        $form->select('s_uid','项目发起人')->setWidth(3,2)->options('/api/get_shanghu')->rules('required');
        $form->radio('symbol','符号')->options(['1' => '   >=', '0'=> '   ='])->default(0);
        $form->number('num','拼单数量')->placeholder("拼单数量，最小为1")->rules('required')->default(1)->min(1);
        $form->text('unit','单位')->placeholder("商品的计量单位（例：个/辆/只/块）")->rules('required')->setWidth(3,2);
        $form->datetime('stime','开始时间');
        $form->datetime('etime','结束时间');
        $form->radio('status','状态')->options(['1' => '上架', '0'=> '下架'])->default(0);
        $form->text('discounts','折扣优惠')->setWidth(3,2);
        $form->select('room_city','城市')->setWidth(3,2)->options('/api/get_cities_list')->rules('required');
        $form->image('photo','封面图')->rules('required')->setWidth(4,2)->placeholder('（必填）拼单列表封面图')->uniqueName();
        $form->multipleImage('images','商品banner')->removable()->placeholder("商品轮播图片（多张）")->uniqueName();
        $form->textarea('content','商品详情')->placeholder("商品的详情描述")->rules('required');
        $form->multipleImage('goods_images','商品详情图片')->removable()->placeholder("商品详情图片（多张）");


//        $form->saving(function (Form $form) {
//            echo "<pre>";
//            print_r($form);die;
//        });

        return $form;
    }
}
