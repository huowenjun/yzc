<?php
/**
 * Created by PhpStorm.
 * User: hwj
 * Date: 2018/11/15
 * Time: 13:19
 */
namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Agents;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;



class AgentsController extends Controller
{
    use HasResourceActions;

    /**
     * @var array $term
     * 术语
     */
    public $term = array(
        'header'=>'招商/代理',
        'indexDescription'=>'展示',
        'showDescription'=>'查看',
        'createDescription'=>'添加',
        'editDescription'=>'修改',
        'id'=>'编号',
        'users_id'=>'用户名称',
        'type'=>'类型',
        'company'=>'招商单位',
        'brand'=>'经营品牌',
        'acreage'=>'面积要求',
        'policy'=>'政策要求',
        'photo'=>'图片',
        'examine'=>'审核',
        'created_at'=>'添加时间',
    );
    const TERM_NO = '暂无';
    /**
     * 招商/代理展示
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
     * 招商/代理查看
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
     * 招商/代理修改
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
        $form = new Form(new Agents());
        $form->display('type',$this->term['type']);
        $form->display('company',$this->term['company']);
        $form->display('brand',$this->term['brand']);
        $form->display('acreage',$this->term['acreage']);
        $form->display('policy',$this->term['policy']);
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
        $show = new Show(Agents::findOrFail($id));
        $show->users()->name($this->term['users_id']);
        $show->type($this->term['type']);
        $show->company($this->term['company']);
        $show->brand($this->term['brand']);
        $show->acreage($this->term['acreage']);
        $show->policy($this->term['policy']);
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
        $grid = new Grid(new Agents());

        $grid->filter(function($filter){
            $filter->disableIdFilter();
            $filter->like('type',$this->term['type']);
            $filter->like('company',$this->term['company']);
        });

        $grid->id($this->term['id']);
        $grid->users()->name($this->term['users_id']);
        $grid->type($this->term['type']);
        $grid->company($this->term['company']);
        $grid->brand($this->term['brand']);
        $grid->acreage($this->term['acreage']);
        $grid->policy($this->term['policy']);
        $grid->created_at($this->term['created_at']);
        $grid->examine($this->term['examine']);
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            // 隐藏删除按钮
            $actions->disableEdit();
            $actions->disableDelete();
        });
        return $grid;
    }
}