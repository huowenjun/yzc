<?php

namespace App\Admin\Controllers;

use App\Models\Recommend;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class RecommendController extends Controller
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
        $grid = new Grid(new Recommend);

        $grid->filter(function($filter){
            $filter->disableIdFilter();
            $filter->between('created_at', '创建时间')->datetime();
            $filter->equal('status','显示')->select(['1'=>'是','0'=>'否']);
            $filter->like('title', '标题');
        });

        $grid->id('Id');
        $grid->images('推荐作品')->image();
        $grid->title('标题');
        $grid->img('大咖头像')->image();
        $grid->introduction('大咖简介');
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');
        $grid->status('显示')->display(function($status){
            return $status ? '是':'否';
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
        $show = new Show(Recommend::findOrFail($id));

        $show->id('Id');
        $show->reason('推荐理由');
        $show->title('标题');
        $show->description('推荐简介');
        $show->img('大咖头像')->image();
        $show->introduction('大咖简介');
        $show->created_at('Created at');
        $show->updated_at('Updated at');
        $show->status('显示');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Recommend);
        $watermark = public_path().'/cloudbrickcity.png';
        $form->multipleImage('images', '推荐作品')->removable()->insert($watermark, 'bottom-left');
        $form->textarea('reason', '推荐理由');
        $form->text('title', '标题');
        $form->textarea('description', '推荐描述');
        $form->image('img', '大咖头像');
        $form->textarea('introduction', '大咖简介');
        $form->switch('status', '显示');

        return $form;
    }
}
