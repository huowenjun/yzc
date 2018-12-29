<?php
/**
 * Created by PhpStorm.
 * User: hwj
 * Date: 2018/11/14
 * Time: 16:37
 */
namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\JobRecruits;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Controllers\HasResourceActions;

class JobRecruitsController extends Controller
{
    use HasResourceActions;

    /**
     * @var array $term
     * 术语
     */
    public $term = array(
        'header'=>'求职招聘',
        'indexDescription'=>'展示',
        'showDescription'=>'查看',
        'editDescription'=>'编辑',
        'createDescription'=>'添加',
        'id'=>'编号',
        'user_id'=>'用户名称',
        'type'=>'类型',
        'position'=>'职位',
        'education'=>'学历',
        'experience'=>'行业经验',
        'experience_info'=>'行业备注',
        'salary'=>'薪资待遇',
        'station_time'=>'到岗时间',
        'sketch'=>'简述',
        'contacts_name'=>'姓名',
        'contacts_tel'=>'电话',
        'photo'=>'照片',
        'examine'=>'审核',
        'created_at'=>'添加时间',
    );
    const TERM_NO = '暂无';

    /**
     * 求职招聘数据展示
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
     * 求职招聘数据查看
     */
    public function show($id, Content $content)
    {
        return $content
            ->header($this->term['header'])
            ->description($this->term['showDescription'])
            ->body($this->detail($id));
    }

    /**
     * 求职招聘数据的编辑
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header($this->term['header'])
            ->description($this->term['editDescription'])
            ->body($this->form()->edit($id));
    }

    protected function grid()
    {
        $grid = new Grid(new JobRecruits());

        $grid->filter(function($filter){
            $filter->disableIdFilter();
            $filter->like('contacts_tel',$this->term['contacts_tel']);
            $filter->like('contacts_name',$this->term['contacts_name']);
        });

        $grid->id($this->term['id']);
        $grid->users()->name($this->term['user_id']);
        $grid->type($this->term['type']);
        $grid->position($this->term['position']);
        $grid->education($this->term['education']);
        $grid->experience($this->term['experience']);
        $grid->experience_info($this->term['experience_info']);
        $grid->salary($this->term['salary']);
        $grid->station_time($this->term['station_time']);
        $grid->sketch($this->term['sketch']);
        $grid->contacts_name($this->term['contacts_name']);
        $grid->contacts_tel($this->term['contacts_tel']);
        $grid->examine($this->term['examine']);
        $grid->created_at($this->term['created_at']);

        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            // 隐藏删除按钮
            $actions->disableDelete();
            // 隐藏修改按钮
            $actions->disableEdit();
        });

        return $grid;
    }

    protected function detail($id){
        $show = new Show(JobRecruits::findOrFail($id));
        $show->users()->name($this->term['user_id']);
        $show->type($this->term['type']);
        $show->position($this->term['position']);
        $show->education($this->term['education']);
        $show->experience($this->term['experience']);
        $show->experience_info($this->term['experience_info']);
        $show->salary($this->term['salary']);
        $show->station_time($this->term['station_time']);
        $show->sketch($this->term['sketch']);
        $show->contacts_name($this->term['contacts_name']);
        $show->contacts_tel($this->term['contacts_tel']);
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

    protected function form()
    {
        $form = new Form(new JobRecruits());
        $form->display('type',$this->term['type']);
        $form->display('position',$this->term['position']);
        $form->display('education',$this->term['education']);
        $form->display('experience',$this->term['experience']);
        $form->display('experience_info',$this->term['experience_info']);
        $form->display('salary',$this->term['salary']);
        $form->display('station_time',$this->term['station_time']);
        $form->display('sketch',$this->term['sketch']);
        $form->display('contacts_name',$this->term['contacts_name']);
        $form->display('contacts_tel',$this->term['contacts_tel']);
        $form->display('created_at',$this->term['created_at']);
        $form->radio('examine',$this->term['examine'])->options(['1' => '通过', '2'=> '未通过']);
        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
            $tools->disableDelete();
        });
        return $form;
    }
}
