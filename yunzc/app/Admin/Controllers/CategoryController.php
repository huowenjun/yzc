<?php

namespace App\Admin\Controllers;

use App\Models\Category;

use Encore\Admin\Form;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Layout\Row;
use Encore\Admin\Tree;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManagerStatic as Image;


class CategoryController extends Controller
{
    use ModelForm;

    protected $header = '类型管理';

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header($this->header);
            $content->description('类型列表');

            $content->row(function (Row $row) {

                $row->column(6, $this->treeView()->render());

                $row->column(6, function (Column $column) {
                    $form = new \Encore\Admin\Widgets\Form();
                    $form->action(admin_base_path('/web/categories'));

                    $watermark = '云砖城';
                    $form->text('name','类型名称');
                    $form->textarea('description','类型描述信息');
                    $form->number('order','排序序号');
                    $form->image('img','图片')->text('foo', 0, 0, function($font) {
                        $font->size(24);
                        $font->color('#fdf6e3');
                        $font->align('center');
                        $font->valign('top');
                        $font->angle(45);
                    });

                    $form->select('parent_id','父类名称')->options(Category::selectOptions());

                    $form->hidden('_token')->default(csrf_token());

                    $column->append((new Box(trans('admin.new'), $form))->style('success'));
                });
            });
        });
    }


    protected function treeView()
    {
        return Category::tree(function (Tree $tree) {
            $tree->disableCreate();
        });

    }


    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {

        return Admin::content(function (Content $content) use ($id) {

            $content->header($this->header);
            $content->description('编辑类型');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {

        return Admin::content(function (Content $content) {

            $content->header($this->header);
            $content->description('添加类型');

            $content->body($this->form());
        });
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Category::class, function (Form $form) {

            $watermark = '云砖城';
            $form->display('id', 'ID');
            $form->image('img','图片')->text('数据库东方会计师的', 10, 25);
            $form->text('name','类型名称');

//            $form->map('lat','lon','地图');

            $form->textarea('description','类型描述信息');
            $form->number('order','排序序号');
            $form->select('parent_id','父类名称')->options(Category::selectOptions());


        });
    }


    public function getCategoryOptions()
    {
        return DB::table('categories')->select('id','name as text')->get();
    }
}