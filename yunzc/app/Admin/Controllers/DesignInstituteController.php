<?php

namespace App\Admin\Controllers;

use App\Models\City;
use App\Models\DesignInstitute;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\Models\Cities;
class DesignInstituteController extends Controller
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
            ->header('设计院-列表')
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
            ->description('设计院')
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
            ->header('编辑')
            ->description('设计院')
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
            ->header('添加')
            ->description('设计院')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new DesignInstitute);
        /*禁用导出数据按钮*/
        $grid->disableExport();
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->between('created_at', '创建时间')->datetime();
            $filter->like('title', '标题');
            $filter->gt('scale', '人员规模');
            $filter->equal('status','显示')->select(['1' => '是','0'=>'否']);
//            $filter->expand();
        });
        $grid->id('Id');
        $grid->title('标题');
        $grid->money('单价/天');
        $grid->scale('人员规模');
        $grid->city_id('城市')->display(function($id){
            return City::where('id',$id)->value('area_name');
    });
        $grid->address('详细地址');
        $grid->status('显示')->display(function($status){
            return $status ? '是':'否';
        });
        $grid->created_at('创建时间');
        $grid->updated_at('修改时间');

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
        $show = new Show(DesignInstitute::findOrFail($id));

        $show->id('Id');
        $show->title('标题');
        $show->money('单价/天');
        $show->u_name('联系人');
        $show->u_mobile('联系方式');
        $show->photo('图片')->image();
        $show->images('图片')->unescape()->as(function($val){
            $imgs = '';
            $path = getenv('APP_URL').'/uploads/';
            foreach($val as $k=>$v){
                $imgs .="<a href='$path$v'target='_blank'><img style='max-width:200px;max-height:200px;' src='$path$v'></a>";
            }
            return $imgs;
        });
        $show->description('简介');
        $show->scale('人员规模');
        $show->city_id('城市')->as(function($val){
            return  Cities::where('area_code',$val)->value('area_name');
        });
        $show->address('详细地址');
        $show->status('显示')->as(function($sta){
          return  $sta == 1?'上架':'下架';
        });
        $show->created_at('创建时间');
        $show->updated_at('更新时间');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new DesignInstitute);

        $form->text('title', '标题')->rules('required');
        $form->currency('money','单价/天')->symbol('￥');
        $form->text('u_name','联系人')->rules('required');
        $form->mobile('u_mobile','联系电话')->options(['mask' => '199 9999 9999'])->rules('required');
        $form->image('photo','列表封面')->rules('required');
        $form->multipleImage('images','图片')->removable();

//        $form->text('images', 'Images');
        $form->textarea('description', '简介')->rules('required');
        $form->number('scale', '人员规模')->rules('required');
        $form->select('city_id','城市')->options('/api/get_cities_list')->rules('required');
        $form->text('address', '详细地址')->rules('required');
        $form->switch('status', '显示')->rules('required');

        return $form;
    }

}
