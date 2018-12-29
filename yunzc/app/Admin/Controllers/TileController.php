<?php

namespace App\Admin\Controllers;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Tile;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\Aipimagessearch\AipImageSearch;
use Illuminate\Support\Facades\Log;
use App\Models\AipImageSearchLog;
class TileController extends Controller
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
        $grid = new Grid(new Tile);
        $grid->actions(function ($actions) {

            $actions->disableView();
        });

        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->between('created_at', '创建时间')->datetime();
            $filter->like('name', '瓷砖名称');
            $filter->equal('status','状态')->select(['1' => '上架','0'=>'下架']);
//            $filter->expand();
        });

        $grid->id('Id')->sortable();
        $grid->brand('品牌')->display(function($cc){

            return $cc['name'];
        });
        $grid->img('展示图片')->image();
//        $grid->images('Images');
        $grid->name('瓷砖名称');
        $grid->status('状态')->display(function ($status){
            return $status == 1 ? '上架' : '下架';
        });
        $grid->sort('排序')->sortable();
//        $grid->cate('Cate');
//        $grid->photos('Photos');
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');

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
        $show = new Show(Tile::findOrFail($id));

        $show->id('Id');
        $show->brand_id('品牌');
        $show->img('Img');
        $show->images('Images');
        $show->name('瓷砖名称');
        $show->description('简述');
//        $show->cate('Cate');
        $show->photos('Photos');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Tile);

        $watermark = public_path().'/cloudbrickcity.png';
        $form->select('brand_id', '品牌')->options(Brand::all()->pluck('name','id'))->rules('required');
        $form->radio('type','瓷砖分类')->options(['1'=>'新品上市','2'=>'热门品牌','3'=>'产品定制','4'=>'降价优惠','5'=>'潮流风格'])->default('1');
        $form->image('img', 'Img')->uniqueName();
        $dir = '/tile/images/'.date('YmdHis');
        $form->multipleImage('images', '上边的图集')->removable()->insert($watermark, 'bottom-left')->move($dir);
        $form->text('name', '瓷砖名称');
        $form->textarea('description', '简述');
        $dir2 = '/tile/photos/'.date('YmdHis');
        $form->multipleImage('photos', '下面的图集')->removable()->insert($watermark, 'bottom-left')->move($dir2);
        $list = Category::where('parent_id','=',0)->pluck('name', 'id')->toArray();
        foreach ($list as $k=>$v){
            $form->checkbox('categories',$v)->options(Category::where('parent_id','=',$k)->pluck('name', 'id'));
        }
    //        $form->checkbox('categories','测试')->options(Category::where('parent_id','<>',0)->pluck('name', 'id'));
        $form->number('sort','排序')->placeholder('越小越靠前');
        $form->radio('status','商品状态')->options(['1'=>'上架','0'=>'下架'])->default('1');
        // 在表单提交前调用
        $form->submitted(function (Form $form) {
            //...
//            dd($form);



        });
        $form->saved(function (Form $form) {
            $images = $form->model()->images;
            $brief['id'] = $form->model()->id;
            $brief['name'] = $form->model()->name;
            $brief['img'] = $form->model()->img;
            $tags = '18,12';
            $filepath = './uploads/';
            /*相同图入库*/
            sameHqAdds($images,$brief,$tags,$type=1,$filepath);
            /*相似图入库*/
            similarAdd($images,$brief,$tags,$type=1,$filepath);
            /*商品检索入库*/
            productAdd($images,$brief,$type=1,$filepath='./uploads/',$class_id1=1,$class_id2=1);
        });
        return $form;
    }
}
