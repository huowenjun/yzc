<?php
namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Users;
use App\User;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;


class UsersController extends Controller
{
    use HasResourceActions;
    /**
     * @var array $term
     * 术语
     */
    public $term = array(
        'header'=>'用户',
        'indexDescription'=>'用户展示',
        'showDescription'=>'用户查看',
        'createDescription'=>'用户添加',
        'editDescription'=>'用户修改',
        'status'=>'商家与客户',
        'createdAt'=>'创建时间',
        'id'=>'编号',
        'email'=>'邮箱',
        'name'=>'用户名',
        'tel'=>'手机号码',
        'img'=>'用户头像',
        'password'=>'密码',
    );
    public $statu = array(
        0=>'客户',
        1=>'商家',
    );
    const TERM_NO = '暂无';

    /**
     * 用户管理数据展示
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

    /**
     * 展示单条数据
     * @param $id 用户id
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
     * 用户数据添加
     * @param Content $content
     * @return Content
     */
    public function create(Content $content){
        return $content
            ->header($this->term['header'])
            ->description($this->term['createDescription'])
            ->body($this->form());
    }

    public function edit($id, Content $content)
    {
        return $content
            ->header($this->term['header'])
            ->description($this->term['editDescription'])
            ->body($this->form()->edit($id));
    }

    /**
     * 用户展示数据处理
     * @return Grid 数据
     */
    protected function grid()
    {
        $grid = new Grid(new Users());
        $grid->filter(function($filter){
            $filter->disableIdFilter();
            $filter->like('tel',$this->term['tel']);
            $filter->equal('status',$this->term['status'])->select($this->statu);
        });

        $grid->id($this->term['id']);
        $grid->name($this->term['name']);
        $grid->status($this->term['status']);
        $grid->tel($this->term['tel'])->display(function($tel){
            $tele = self::TERM_NO;
            if (!empty($tel)){
                $h = substr($tel,0,3);
                $e = substr($tel,-4,4);
                $tele = $h.'****'.$e;
            }
            return $tele;
        });
        $grid->img($this->term['img'])->image()->display(function($img){
            if (empty($img)){
                $img = self::TERM_NO;;
            }
            return $img;
        });
        $grid->created_at($this->term['createdAt']);
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
     * 单条用户数据处理
     * @param $id 编号
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Users::findOrFail($id));
        $show->name($this->term['name']);
        $show->tel($this->term['tel']);
        $show->img($this->term['img'])->image();
        $show->status('status',$this->term['status']);
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
     * 添加与修改页面的数据处理
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User());
        $form->text('name',$this->term['name']);
        $form->image('img',$this->term['img']);
        $form->text('tel',$this->term['tel']);
        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = bcrypt($form->password);
            }
        });
        $form->password('password',$this->term['password'])->rules('required');
        $form->switch('status', $this->term['status']);
        $form->display('created_at', '添加时间');
        $form->display('updated_at', '修改时间');
//        $form->tools(function (Form\Tools $tools) {
//            $tools->disableView();
//            $tools->disableDelete();
//        });
//添加编辑页按钮
        return $form;
    }

}