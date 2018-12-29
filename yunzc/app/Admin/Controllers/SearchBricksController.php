<?php
/**
 * Created by PhpStorm.
 * User: HWJ
 * Date: 2018/11/15
 * Time: 14:54
 */
namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SearchBricks;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;


class SearchBricksController extends Controller
{
    use HasResourceActions;

    /**
    * @var array $term
    * 术语
    */
    public $term = array(
        'header'=>'广播找砖',
        'indexDescription'=>'展示',
        'showDescription'=>'查看',
        'createDescription'=>'添加',
        'editDescription'=>'修改',
        'id'=>'编号',
        'user_id'=>'用户名称',
        'specifications'=>'规格',
        'category'=>'品类',
        'style'=>'材质',
        'brands_id'=>'品牌要求',
        'sketch'=>'简述',
        'photo'=>'照片',
        'examine'=>'审核',
        'created_at'=>'添加时间',
    );
    const TERM_NO = '暂无';

    /**
     * 展示
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header($this->term['header'])
            ->description($this->term['indexDescription'])
            ->body($this->grid());
    }

    /**
     * 查看
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header($this->term['header'])
            ->description($this->term['showDescription'])
            ->body($this->detail($id));
    }
    /**
     * 编辑
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header($this->term['header'])
            ->description($this->term['editDescription'])
            ->body($this->form()->edit($id));
    }

    protected function form()
    {
        $form = new Form(new SearchBricks());
        $form->display('specifications',$this->term['specifications']);
        $form->display('category',$this->term['category']);
        $form->display('style',$this->term['style']);
        $form->display('brands_id',$this->term['brands_id']);
        $form->display('sketch',$this->term['sketch']);
        //$form->image('photo',$this->term['photo']);
        $form->display('created_at',$this->term['created_at']);
        $form->radio('examine',$this->term['examine'])->options(['1' => '通过', '2'=> '未通过']);
        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
            $tools->disableDelete();
        });
        return $form;
    }
    protected function detail($id)
    {
        $show = new Show(SearchBricks::findOrFail($id));
        $show->users()->name($this->term['user_id']);
        $show->specifications($this->term['specifications']);
        $show->category($this->term['category']);
        $show->style($this->term['style']);
        $show->brands_id($this->term['brands_id']);
        $show->sketch($this->term['sketch']);
        $show->photo('图片')->unescape()->as(function($val){
            $imgs = '';
            $path = getenv('APP_URL').'/uploads/';
            foreach($val as $k=>$v){
                $imgs .="<a href='$path$v'target='_blank'><img style='max-width:200px;max-height:200px;' src='$path$v'></a>";
            }
            return $imgs;
        });
        $show->created_at($this->term['created_at']);
        $show->examine($this->term['examine']);
        $show->panel()
            ->tools(function ($tools) {
                //$tools->disableEdit();//修改but
                $tools->disableList();//列表but
                $tools->disableDelete();//删除but
            });
        return $show;
    }

    protected function grid()
    {
        $grid = new Grid(new SearchBricks());
        $grid->id($this->term['id']);
        $grid->users()->name($this->term['user_id']);
        $grid->specifications($this->term['specifications']);
        $grid->category($this->term['category']);
        $grid->style($this->term['style']);
        $grid->brands_id($this->term['brands_id']);
        $grid->sketch($this->term['sketch']);

        $grid->created_at($this->term['created_at']);
        $grid->examine($this->term['examine']);
        $grid->actions(function ($actions) {
            // 隐藏删除按钮
            $actions->disableEdit();
            $actions->disableDelete();
        });
        $grid->disableCreateButton();
        return $grid;
    }
}