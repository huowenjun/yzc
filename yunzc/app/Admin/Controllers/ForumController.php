<?php
/**
 * 热门论坛设置
 * User: hwj
 * Date: 2018/11/30
 * Time: 9:14
 */

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Forum;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;


class ForumController extends Controller
{
    use HasResourceActions;
    /**
     * @var array $term
     * 术语
     */
    public $term = array(
        'header'=>'热门论坛',
        'indexDescription'=>'展示',
        'showDescription'=>'查看',
        'editDescription'=>'修改',
    );
    /**
     * 热门管理数据展示
     * @param Content $content obj
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header($this->term['header'])
            ->description($this->term['indexDescription'])
            ->body($this->grid());
    }

    public function show($id, Content $content)
    {
        return $content
            ->header($this->term['header'])
            ->description($this->term['showDescription'])
            ->body($this->detail($id));
    }
    protected function detail($id)
    {
        $show = new Show(Forum::findOrFail($id));
        $show->id('编号');
        $show->title('标题');
        $show->browse_num('浏览量');
        $show->fabulous_num('点赞量');
        $show->collection_num('收藏量');
        $show->created_at('发布时间');
        $show->examine('审核状态')->as(function($sta){
            switch($sta){
                case '0':
                    return "待审核";
                    break;
                case '1':
                    return "通过";
                    break;
                case '2':
                    return "未通过";
                    break;
            }
        });
        $show->status('是否热门')->as(function($sta){
            switch($sta){
                case '0':
                    return "否";
                    break;
                case '1':
                    return "是";
                    break;
            }
        });
        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();//修改but
//                $tools->disableList();//列表but
                $tools->disableDelete();//删除but
            });
        $show->panel()->title('用户详情');
        return $show;
    }

    /**
     * @return Grid 数据
     */
    protected function grid()
    {
        $grid = new Grid(new Forum());
        $grid->filter(function($filter){
            $filter->disableIdFilter();
            $filter->like('title','标题');

        });

        $grid->id('编号');
        $grid->title('标题');
        $grid->browse_num('浏览量');
        $grid->fabulous_num('点赞量');
        $grid->collection_num('收藏量');
        $grid->created_at('发布时间');
        $grid->examine('审核状态')->select([
            0 => '待审核',
            1 => '通过',
            2 => '未通过',
        ]);
        $grid->status('是否热门')->select([
            0 => '否',
            1 =>'是',
        ]);
        $grid->disableCreateButton();//去掉添加按钮
        $grid->disableRowSelector();//去掉操作按钮
        $grid->actions(function ($actions) {
            // 隐藏删除按钮
            $actions->disableDelete();
            // 隐藏修改按钮
            $actions->disableEdit();
        });
        return $grid;
    }

    /**
     * 添加与修改页面的数据处理
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Forum());
        $form->select('examine','审核状态')->options([
            0 => '待审核',
            1 => '通过',
            2 => '未通过',
        ]);
        $form->select('status','是否热门')->options([
            0 => '否',
            1 => '是',
        ]);
//        $form->tools(function (Form\Tools $tools) {
//            $tools->disableView();
//            $tools->disableDelete();
//        });
//添加编辑页按钮
        return $form;
    }

}