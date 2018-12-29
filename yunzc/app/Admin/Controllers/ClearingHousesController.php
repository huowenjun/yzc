<?php
/**
 * Created by PhpStorm.
 * User: HWJ
 * Date: 2018/11/14
 * Time: 13:29
 */
namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClearingHouses;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Controllers\HasResourceActions;

class ClearingHousesController extends Controller
{
    use HasResourceActions;
    /**
     * @var array $term
     * 术语
     */
    public $term = array(
        'header'=>'清仓特卖',
        'indexDescription'=>'展示',
        'showDescription'=>'查看',
        'editDescription'=>'编辑',
        'createDescription'=>'添加',
        'id'=>'编号',
        'user_id'=>'用户名称',
        'univalent'=>'出售单价',
        'unit'=>'单位',
        'number'=>'出售数量',
        'company'=>'单位',
        'mode'=>'交货方式',
        'invoice'=>'是否含发票',
        'room_city'=>'',
        'address'=>'详细地址',
        'remarks'=>'备注',
        'contacts_name'=>'联系人姓名',
        'contacts_tel'=>'联系人电话',
        'photo'=>'图片',
        'examine'=>'审核',
        'created_at'=>'添加时间',

    );
    const TERM_NO = '暂无';

    /**
     * 清仓特卖数据展示
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
     * 清仓特卖数据查看
     * @param $id
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
     * 清仓特卖数据编辑
     * @param $id
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


    protected function grid()
    {
        $grid = new Grid(new ClearingHouses());
        $grid->filter(function($filter){
            $filter->disableIdFilter();
            $filter->like('contacts_tel',$this->term['contacts_tel']);
            $filter->like('contacts_name',$this->term['contacts_name']);
        });

        $grid->id($this->term['id'])->sortable();
        $grid->users()->name($this->term['user_id']);
        $grid->univalent($this->term['univalent']);
        $grid->unit($this->term['unit']);
        $grid->number($this->term['number']);
        $grid->mode($this->term['mode']);
        $grid->invoice($this->term['invoice']);
        $grid->address($this->term['address']);
        $grid->contacts_name($this->term['contacts_name']);
        $grid->contacts_tel($this->term['contacts_tel']);
        $grid->remarks($this->term['remarks']);
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

    protected function detail($id){
        $show = new Show(ClearingHouses::findOrFail($id));
        $show->users()->name($this->term['user_id']);
        $show->univalent($this->term['univalent']);
        $show->unit($this->term['unit']);
        $show->number($this->term['number']);
        $show->mode($this->term['mode']);
        $show->invoice($this->term['invoice']);
        $show->address($this->term['address']);
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
        $show->remarks($this->term['remarks']);
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
        $form = new Form(new ClearingHouses());
        $form->display('univalent',$this->term['univalent']);
        $form->display('unit',$this->term['unit']);
        $form->display('number',$this->term['number']);
        $form->display('mode',$this->term['mode']);
        $form->display('invoice',$this->term['invoice']);
        $form->display('address',$this->term['address']);
        $form->display('contacts_name',$this->term['contacts_name']);
        $form->display('contacts_tel',$this->term['contacts_tel']);
        //$form->image('photo',$this->term['photo']);
        $form->display('remarks',$this->term['remarks']);
        $form->display('created_at',$this->term['created_at']);
        $form->radio('examine',$this->term['examine'])->options(['1' => '通过', '2'=> '未通过']);
        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
            $tools->disableDelete();
        });
        return $form;
    }
}