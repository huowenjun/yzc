<?php

use Illuminate\Http\Request;
use App\Article;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
/**
 * 全接口请求方式Wie-----api/请求接口
 */
Route::group(['middleware' => 'auth:api'], function(){
    Route::post('get-details', 'Api\PassportController@getDetails');
});
Route::group(['middleware' => ['auth.api']], function () {
    Route::post('initiate_single','Api\CollageControlerodel@add_single' ); //拼单 - 发起拼单
    Route::post('get_my_signle_list','Api\CollageControlerodel@my_signle' ); //拼单 - 我的拼单列表
    /**
     * 我发布的
     */
    Route::any('set_clearing_houses','Api\ClearingHousesController@setClearingHouses');//十个入口--清仓特卖?
    Route::any('get_my_clearing_houses','Api\ClearingHousesController@getMyClearingHouses');//我发布的--获取我发的清仓特卖列表和详情？
    Route::any('del_my_clearing_houses','Api\ClearingHousesController@delMyClearingHouses');//del-我发的清仓特卖？
    Route::any('set_job_recruits','Api\JobRecruitsController@setJobRecruits');//十个入口--求职招聘?
    Route::any('get_my_job_recruits','Api\JobRecruitsController@getMyJobRecruits');//我发布的--获取我发布的求职招聘？
    Route::any('del_my_job_recruits','Api\JobRecruitsController@delMyJobRecruits');//我发布的--del我发布的求职招聘？
    Route::any('set_getrent_setrents','Api\GetrentSetrentsController@setGetrentSetrents');//十个入口--寻租转租?
    Route::any('get_my_getrent_setrents','Api\GetrentSetrentsController@getMyGetrentSetrents');//我发布的--获取我发布的寻租转租？
    Route::any('del_my_getrent_setrents','Api\GetrentSetrentsController@delMyGetrentSetrents');//del-我发布的寻租转租?
    Route::any('set_agents','Api\AgentsController@setAgents');//十个入口--招商代理?
    Route::any('get_my_agents','Api\AgentsController@getMyAgents');//我发布的--获取我发布的招商代理?
    Route::any('del_my_agents','Api\AgentsController@delMyAgents');//del-我发布的招商代理?
    Route::any('set_search_bricks','Api\SearchBricksController@setSearchBricks');//十个入口--广播找砖?
    Route::any('get_my_search_bricks','Api\SearchBricksController@getMySearchBricks');//我发布的--获取我发布的广播找砖?
    Route::any('del_my_search_bricks','Api\SearchBricksController@delMySearchBricks');//del-我发布的广播找砖?
    Route::post('addfeedback','Api\FeedbackControlerodel@add_feedback'); //添加反馈意见
    Route::post('procurement_insert','Api\ProcurementControlerodel@create'); //添加采购快讯
    Route::post('project_cooperation','Api\ProjectCooperationControlerodel@create'); //添加项目合作
    Route::post('drawing_part','Api\DrawingPartControlerodel@create'); //添加找作图员
    Route::post('help_see','Api\HelpSeeControlerodel@create'); //添加帮帮看
    Route::post('procurement_list','Api\ProcurementControlerodel@index'); //采购快讯-列表
    Route::post('my_procurement','Api\ProcurementControlerodel@my_list'); //采购快讯-我的发布-列表
    Route::post('my_procurement_del','Api\ProcurementControlerodel@destroy'); //采购快讯-我的发布-删除
    Route::post('procurement_detail','Api\ProcurementControlerodel@show'); //采购快讯-详情
    Route::post('project_cooperation_list','Api\ProjectCooperationControlerodel@index'); //项目合作-列表
    Route::post('my_project_cooperation_list','Api\ProjectCooperationControlerodel@my_list'); //我的发布-项目合作-列表
    Route::post('my_project_cooperation_del','Api\ProjectCooperationControlerodel@destroy'); //我的发布-项目合作-删除
    Route::post('project_cooperation_detail','Api\ProjectCooperationControlerodel@show'); //项目合作-详情
    Route::post('drawing_part_list','Api\DrawingPartControlerodel@index'); //找作图员-列表
    Route::post('my_drawing_part_list','Api\DrawingPartControlerodel@my_list'); //我的发布-找作图员-列表
    Route::post('my_drawing_part_del','Api\DrawingPartControlerodel@destroy'); //我的发布-找作图员-删除
    Route::post('drawing_part_detail','Api\DrawingPartControlerodel@show'); //找作图员-详情
    Route::post('help_see_list','Api\HelpSeeControlerodel@index'); //帮帮看-列表
    Route::post('my_help_see_list','Api\HelpSeeControlerodel@my_list'); //我的发布-帮帮看-列表
    Route::post('my_help_see_del','Api\HelpSeeControlerodel@destroy'); //我的发布-帮帮看-删除
    Route::post('help_see_detail','Api\HelpSeeControlerodel@show'); //帮帮看-详情
    Route::post('get_design_institute_list','Api\DesignInstituteController@index'); //设计院列表
    Route::post('get_design_institute_detail','Api\DesignInstituteController@show'); //设计院详情
    Route::post('users_info_edit','Api\PassportController@usersInfoEdit');//编辑用户资料？
    Route::post('get_user_info','Api\PassportController@getUserInfo');//获取用户资料？

    Route::post('dealers_add','Api\DealersControlerodel@create'); //经销商-添加
    Route::post('dealers_edit','Api\DealersControlerodel@edit'); //经销商-编辑
    Route::post('dealers_update','Api\DealersControlerodel@update'); //经销商-编辑保存
    Route::post('dealers_toggle','Api\DealersControlerodel@status_toggle'); //经销商-上架/下架
    Route::post('dealers_list','Api\DealersControlerodel@index'); //经销商-列表
    Route::post('dealers_del','Api\DealersControlerodel@destroy'); //经销商-删除
    Route::post('tile_list','Api\TileControlerodel@index'); //商户-商品-列表
    Route::post('tile_toggle','Api\TileControlerodel@status_toggle'); //商户-商品-上下架
    Route::post('merchants_index','Api\TileControlerodel@index_num'); //商户-首页-商品/经销商 数量
    Route::post('edit_logo','Api\BrandController@upd_logo'); //商户 修改品牌logo
    Route::post('get_merchant_info','Api\BrandController@get_merchant_info'); //商户 修改品牌logo

    Route::post('suggestions_add','Api\SuggestionsControlerodel@create'); //反馈/建议-添加
    Route::post('images_search','Api\ImageSearchControlerodel@index'); //相似图形检索
    Route::post('get_evaluates','Api\BrandEvaluatesControlerodel@index'); //商户端的评价列表-品牌评价
    Route::post('get_sys_message','Api\BrandEvaluatesControlerodel@message_list'); //系统消息列表
    Route::post('get_sys_message_detail','Api\BrandEvaluatesControlerodel@message_detail'); //系统消息详情
    Route::post('get_message_num','Api\BrandEvaluatesControlerodel@message_count'); //商户系统消息 未读数目



});
Route::post('login', 'Api\PassportController@login');//登录接口请求?
Route::post('bind_tel', 'Api\PassportController@bindTel');//绑定手机号?
Route::post('register', 'Api\PassportController@register');//添加6位推荐码到注册？
Route::post('verify','Api\VerifyController@get_code');//获取验证码?
Route::post('password_back','Api\PassportController@get_back_password');//修改密码？
Route::post('sowing_map','Api\SowingMapController@show');//轮播图?
Route::any('recommend_show','Api\RecommendsController@show');//大咖推荐?
Route::any('recommend_get_show','Api\RecommendsController@getShow');//大咖详情?
Route::post('cities','Api\AddressController@show');//获取城市列表+热门城市?
Route::post('cities_info','Api\AddressController@citiesInfo');//根据城市名获取城市信息?
Route::any('get_clearing_houses','Api\ClearingHousesController@getClearingHouses');//十个入口--获取清仓特卖?
Route::any('get_job_recruits','Api\JobRecruitsController@getJobRecruits');//十个入口--获取求职招聘?
Route::any('shop_type','Api\ShopTypeController@shopType');//店铺类型?
Route::any('get_getrent_setrents','Api\GetrentSetrentsController@getGetrentSetrents');//十个入口--寻租转租?
Route::any('get_agents','Api\AgentsController@getAgents');//十个入口--招商代理?
Route::any('get_search_bricks','Api\SearchBricksController@getSearchBricks');//十个入口--广播找砖?

Route::post('single_file_upload','Api\PassportController@images');   /*单文件上传*/
Route::post('multipleImage','Api\PassportController@multipleImage');   /*多文件上传*/
Route::post('base_url','Api\AddressController@get_base_url');
Route::post('upload','Api\PassportController@upload' );
Route::post('uploadAudio','Api\PassportController@uploadAudio' );

Route::post('get_collage_list','Api\CollageControlerodel@getlist'); //拼单 - 列表
Route::post('get_collage_detail','Api\CollageControlerodel@getdetail'); //拼单 - 详情
Route::get('get_cities_list','Api\CollageControlerodel@getcities' ); //拼单 - 市  //post 后台调用会出错
Route::get('get_shanghu','Api\CollageControlerodel@getuser' ); //拼单 - 后台管理用 商户
Route::post('get_single_user_list','Api\CollageControlerodel@single_list'); //拼单人 - 列表

Route::any('disclaimer','Api\DisclaimerController@index');//免责说明？
Route::any('about_us','Api\VersionsController@aboutUs');//关于我们?
Route::any('version','Api\VersionsController@version');//版本控制?
Route::any('advertising','Api\AdvertisingsController@index');//广告？
/**
 * 论坛接口位置
 */
//start
Route::any('random_forum','Api\Forum\ForumController@randomForum');//首页初始化论坛随机数据----论坛初始化随机数据?
Route::any('hot_forum','Api\Forum\HotController@hotForum');//热门论坛接口?
Route::any('brand_forum','Api\Forum\BrandsController@brandForum');//品牌论坛接口？
Route::any('theme','Api\Forum\ThemesController@themeForum');//主题论坛接口？
Route::any('forum_list','Api\Forum\ForumController@forumList');//论坛列表选择后的列表数据?
Route::any('get_offten_go','Api\Forum\ForumController@getOfftenGo');//用户常去的论坛接口（有token就传，无token就不传）?
Route::any('show_comment','Api\Forum\CommentController@showComment');//论坛评论展示?
Route::any('two_page_comment','Api\Forum\CommentController@twoPageComment');//二级评论加载更多--分页接口?
Route::any('one_comment_info','Api\Forum\CommentController@oneCommentInfo');//一级评论详情?

Route::group(['middleware' => ['auth.api']], function () {
    Route::any('release','Api\Forum\ReleaseController@releaseForum');//用户论坛发布+token？
    Route::any('manage','Api\Forum\ReleaseController@manageForum');//用户管理自己的论坛/用户点击论坛列表后数据详情?
    Route::any('del_forum','Api\Forum\ReleaseController@delForum');//用户删除自己的某条论坛?
    Route::any('collection_content','Api\Forum\ForumController@collection');//收藏（关注）/取消论坛内容接口？
    Route::any('collection_sum','Api\Forum\ForumController@collectionSum');//当前用户收藏论坛内容的总数?
    Route::any('my_collection','Api\Forum\ForumController@myCollection');//我的收藏?
    Route::any('fabulous','Api\Forum\ForumController@fabulous');//点赞/取消点赞（0取消，1点赞）?
    Route::any('no_read','Api\Forum\FabulousController@noRead');//未读消息--点赞未读次数?
    Route::any('no_read_comment','Api\Forum\CommentController@noReadComment');//未读消息--评论未读次数?
    Route::any('see_no_read','Api\Forum\FabulousController@seeNoRead');//查看未读消息--点赞未读消息?
    Route::any('one_comment','Api\Forum\CommentController@oneComment');//一级评论提交?
    Route::any('two_comment','Api\Forum\CommentController@twoComment');//二级评论回复?
    Route::any('set_fabulou','Api\Forum\CommentController@setFabulou');//一级评论点赞/取消点赞（0取消，1点赞）?
    Route::any('see_no_read_comment','Api\Forum\CommentController@seeNoReadComment');//查看未读消息--评论?
});
//end


//start找砖路由
Route::any('brand','Api\BrandController@index');//品牌？
Route::any('category','Api\SearchBrick\CategoryController@index');//品类接口？
Route::any('material','Api\SearchBrick\CategoryController@materia');//属性材质接口？
Route::any('technology','Api\SearchBrick\CategoryController@technology');//属性工艺列表接口?
Route::any('surface','Api\SearchBrick\CategoryController@surface');//属性表面接口?
Route::any('appropriate','Api\SearchBrick\CategoryController@appropriate');//属性适用范围?
Route::any('specifications','Api\SpecificationsController@index');//规格？
Route::any('production_manu','Api\SearchBrick\CategoryController@productionManu');//生产厂商属性接口?
Route::any('parts','Api\SearchBrick\CategoryController@parts');//配件接口?
/*
 * 点击品牌列表，进入产品中心的接口
 */
Route::any('brand_info','Api\SearchBrick\BrandsController@brandInfo');//展示品牌详情---基本信息？
Route::any('brand_introduce','Api\SearchBrick\BrandsController@brandIntroduce');//品牌介绍？
Route::any('brand_evaluate','Api\SearchBrick\BrandsController@brandEvaluate');//品牌用户评价?
Route::any('get_product_data','Api\SearchBrick\BrandsController@getProductData');//产品中心数据?
Route::group(['middleware' => ['auth.api']], function () {
    Route::any('set_brand_evaluate','Api\SearchBrick\BrandsController@setBrandEvaluate');//品牌用户评价提交?
    Route::any('set_product_evaluate','Api\SearchBrick\BrandsController@setProductEvaluate');//产品用户评价提交？
    Route::any('sum_p','Api\SearchBrick\BrandsController@sumP');//用户评价总数
    Route::any('my_p','Api\SearchBrick\BrandsController@myP');//我的评价列表
    Route::any('my_b','Api\SearchBrick\BrandsController@myB');//我的评价品牌列表
});
//end
/*
 * 1论坛2产品3拼单
 */
Route::any('history_search','Api\Search\HistorySearchController@historyWord');//搜索历史词
Route::any('search_forum','Api\Forum\ForumController@searchForum');//论坛搜索?
Route::any('search_evaluate','Api\SearchBrick\BrandsController@searchEvaluate');//品牌--产品搜索
Route::any('search_collage','Api\CollageControlerodel@searchCollage');//拼单
//Route::any('data','Api\Search\HistorySearchController@data');

Route::post('file_upload','Api\PassportController@file_upload');   /*文件上传 测试*/
Route::post('uploadAudio','Api\PassportController@uploadAudio');   /*异步文件上传 测试*/
Route::post('get_main_list','Api\TileControlerodel@main_list');   /*瓷砖主打产品列表*/
Route::post('get_tile_detail','Api\TileControlerodel@tile_detail');   /*主打产品详情*/

Route::post('get_tile_search','Api\TileControlerodel@get_tile_search');   /*瓷砖搜索*/
Route::post('get_tile_type_list','Api\TileControlerodel@tile_type_list');   /*瓷砖分类列表*/

Route::any('code','Api\CodeController@code');   /*生产验证码*/
Route::post('tiles_share','Api\TileControlerodel@tiles_share');   /*瓷砖分享*/
Route::post('forums_share','Api\Forum\ForumController@forums_share'); // 论坛分享

