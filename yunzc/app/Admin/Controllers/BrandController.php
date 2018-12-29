<?php

namespace App\Admin\Controllers;

use App\Models\Brand;
use App\Http\Controllers\Controller;
use App\User;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use function foo\func;

class BrandController extends Controller
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
//        $alias = ['18','19'];
//        dd(initPush_admin(1,'哈喽！我又回来啦！！！',$alias));
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
            ->header('创建品牌')
            ->description('创建品牌并且创建品牌用户')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Brand);
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->between('created_at', '创建时间')->datetime();
            $filter->like('name', '品牌名称');
//            $filter->equal('status','状态')->select(['1' => '上架','0'=>'下架']);
//            $filter->expand();
        });

        $grid->id('Id');
        $grid->name('品牌');
        $grid->user('品牌服务商')->display(function($data){
            return $data ? $data['name'] : '暂无';
        });
//        $grid->images('图片');
        $grid->trade_mark('商标')->image();
//        $grid->description('描述');
        $grid->link('企业网址');
//        $grid->honors('荣誉图片');
        $grid->tiles('瓷砖数量')->display(function($dds){
            return count($dds);
        });
       /* $grid->status('状态')->display(function ($status){
            return $status ? '显示':'隐藏';
        });*/
        $grid->service_tel('客服电话');
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
        $show = new Show(Brand::findOrFail($id));

        $show->id('Id');
        $show->name('品牌');
//        $show->images('图片')->json();
        $show->trade_mark('商标')->image();
        $show->description('简介');
        $show->link('企业网址');
//        $show->honors('荣誉图片')->json();
        $show->service_tel('客服电话');
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

        $form = new Form(new Brand);
        $form->tab('基本信息', function ($form) {
            $watermark = public_path().'/cloudbrickcity.png';
            $form->text('name', '品牌名称')->rules('required|unique:brands,name');
            $form->multipleImage('images', '图片')->removable()->insert($watermark, 'bottom-left');
            $form->image('trade_mark', '商标')->rules('required');
            $form->textarea('description', '简介')->rules('required');
            $form->url('link', '公司官网')->rules('required');
            $form->multipleImage('honors', '荣誉图片')->removable()->insert($watermark, 'bottom-left');
            $form->mobile('service_tel', '客服电话')->options(['mask'=>'999-9999-999'])->rules('required');

        })->tab('品牌服务商', function ($form) {
                $form->text('user.name','昵称')->rules('required');
                $form->email('user.email','邮箱')->rules('email');
                $form->image('user.img','头像')->rules('required');
                $form->radio('user.security','保障金')->options(['1' => '已交', '0'=> '未交'])->default('0');
                $form->text('user.memo','简述')->rules('max:200');
                $form->password('user.pass','密码');
                $form->text('user.tel','手机/登录账号')->rules('required|regex:/^1[3456789][0-9]{9}$/|unique:users,tel');
                $form->hidden('user.status')->default('1');
                $form->hidden('user.referral_code')->default(function(){
                    return $this->get_rand();
                });
        });
        return $form;
    }

    /**
     * @return string
     * 推荐码生成
     */
    public function get_rand(){
        $code = strtoupper(str_random(6));
        if (User::where(['referral_code'=>$code])->count()){
            return $this->get_rand();
        }else{
            return $code;
        }
    }

}
