<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('/users', UserController::class);
    $router->resource('/web/categories',CategoryController::class);
    //app首页
    $router->resource('/first/carousel',CarouselController::class);
    $router->resource('/first/recommend',RecommendController::class);
    $router->resource('/first/advertising',AdvertisingController::class);
    $router->resource('/first/designing_institute',DesignInstituteController::class);
    $router->resource('/first/clearing_houses',ClearingHousesController::class);//清仓特卖
    $router->resource('/first/job_recruits',JobRecruitsController::class);//求职招聘
    $router->resource('/first/shops',ShopsController::class);//店铺类型
    $router->resource('/first/getrent_setrents',GetrentSetrentsController::class);//寻租转租
    $router->resource('/first/agents',AgentsController::class);//招商/代理
    $router->resource('/first/search_bricks',SearchBricksController::class);//广播找砖
    $router->resource('/brand',BrandController::class);
    $router->resource('/tile',TileController::class);
    $router->resource('/theme',ThemeController::class);
    $router->get('/api/cities','ApiController@cities');
    $router->resource('/user',UsersController::class);//用户管理
    $router->resource('/collage',CollageController::class);// 拼单管理
    $router->resource('/collages_user_list',CollagesUserListController::class);// 拼单人管理
    $router->resource('/other/hot_city',CityController::class);//热门城市
    $router->resource('/other/disclaimer',DisclaimerController::class);//免责说明
    $router->resource('/other/my',VersionsController::class);//关于我们
    $router->resource('/forum',ForumController::class);//热门论坛





    $router->resource('/system_message',SystemMessageController::class);//系统消息





    $router->resource('/feedback',FeedbackController::class);// 反馈意见
    $router->resource('/procurement',ProcurementController::class);// 采购快讯
    $router->resource('/project_cooperation',ProjectCooperationController::class);// 项目合作
    $router->resource('/drawing_part',DrawingPartController::class);// 找作图员
    $router->resource('/help_see',HelpSeeController::class);// 帮帮看

    $router->resource('/feedback',FeedbackController::class);// 反馈意见
    $router->resource('/suggestions',SuggestionsController::class);// 投诉/建议

    $router->resource('/dealers',DealersController::class);// 经销商
});