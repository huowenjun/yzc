<?php
/**
 * Created by PhpStorm.
 * User: huowenjun(hwj)
 * Date: 2018/11/15
 * Time: 10:56
 */
namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GetrentSetrents;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;


class GetrentSetrentsController extends Controller
{
    use HasResourceActions;

    /**
     * @var array $term
     * 术语
     */
    public $term = array(
        'header'=>'寻租/转租',
        'indexDescription'=>'展示',
        'showDescription'=>'查看',
        'createDescription'=>'添加',
        'editDescription'=>'修改',
        'id'=>'编号',
        'user_id'=>'用户名称',
        'type'=>'类型',
        'acreage'=>'面积/平方',
        'money'=>'日租金/元',
        'address'=>'详细地址',
        'start_time'=>'转让起始日期',
        'set_money'=>'转让费',
        'shops_id'=>'店铺类型',
        'matching'=>'商业配套',
        'contacts_name'=>'联系人姓名',
        'contacts_tel'=>'联系人电话',
        'photo'=>'图片',
        'examine'=>'审核',
        'created_at'=>'添加时间',
        'updated_at'=>'修改时间',
    );
    const TERM_NO = '暂无';

    /**
     * 寻租、转租展示
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
     * 寻租、转租查看
     */
    public function show($id, Content $content)
    {
        return $content
            ->header($this->term['header'])
            ->description($this->term['showDescription'])
            ->body($this->detail($id));
    }

    /**
     * 寻租、转租修改
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
        $form = new Form(new GetrentSetrents());
        $form->display('type',$this->term['type']);
        $form->display('acreage',$this->term['acreage']);
        $form->display('money',$this->term['money']);
        $form->display('address',$this->term['address']);
        $form->display('start_time',$this->term['start_time']);
        $form->display('set_money',$this->term['set_money']);
        $form->display('matching',$this->term['matching']);
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

    protected function detail($id)
    {
        $show = new Show(GetrentSetrents::findOrFail($id));
        $show->users()->name($this->term['user_id']);
        $show->type($this->term['type']);
        $show->acreage($this->term['acreage']);
        $show->money($this->term['money']);
        $show->address($this->term['address']);
        $show->start_time($this->term['start_time']);
        $show->set_money($this->term['set_money']);
        $show->shops()->shop_type($this->term['shops_id']);
        $show->matching($this->term['matching']);
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
        $show->examine($this->term['examine']);
        $show->created_at($this->term['created_at']);
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
        $grid = new Grid(new GetrentSetrents());

        $grid->filter(function($filter){
            $filter->disableIdFilter();
            $filter->like('contacts_name',$this->term['contacts_name']);
            $filter->like('contacts_tel',$this->term['contacts_tel']);
        });

        $grid->id($this->term['id']);
        $grid->users()->name($this->term['user_id']);
        $grid->type($this->term['type']);
        $grid->acreage($this->term['acreage']);
        $grid->money($this->term['money']);
        $grid->address($this->term['address']);
        $grid->start_time($this->term['start_time']);
        $grid->set_money($this->term['set_money']);
        $grid->shops()->shop_type($this->term['shops_id']);
        $grid->matching($this->term['matching']);
        $grid->contacts_name($this->term['contacts_name']);
        $grid->contacts_tel($this->term['contacts_tel']);
        $grid->examine($this->term['examine']);
        $grid->created_at($this->term['created_at']);
        $grid->actions(function ($actions) {
            // 隐藏删除按钮
            $actions->disableEdit();
            $actions->disableDelete();
        });
        $grid->disableCreateButton();//去掉添加按钮

        return $grid;
    }


}