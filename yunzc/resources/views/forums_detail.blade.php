<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>首页</title>
    <link rel="stylesheet" href="/fenxiang/css/style.css">
    <link rel="stylesheet" type="text/css" href="/fenxiang/css/swiper.min.css"/>

    <script src="/fenxiang/js/rem.js"></script>
    <script src="/fenxiang/js/jquery-3.1.1.js"></script>
    <script src="/fenxiang/js/swiper2.min.js" type="text/javascript" charset="utf-8"></script>
</head>
<body>
<section>
    <h1 class="dow_tit"><img src="/uploads/{{$sys['server_img']}}" class="logo">{{$sys['server_name']}} <a href="#" class="downloads">下载</a></h1>
    <banner>
        <div class="swiper-container banner" style="height: 100%">
            <div class="swiper-wrapper">
                @foreach($info['photo'] as $v)
                    <div class="swiper-slide">
                        <img src="{{$v}}">
                    </div>
                @endforeach
            </div>
            <div class="swiper-pagination" style="bottom: 0.1rem;width: .69rem;background: rgba(0,0,0,0.7);right: 0.1rem;left: 85%;color: #fff;height: .35rem;line-height: .35rem;border-radius: .16rem;"></div>
        </div>
    </banner>
    <section class="address_xq">
        <h3 class="address_tit">{{$info['title']}}</h3>
        <div class="address_cont">
            <div class="as_info">
                <span>来源：{{$info['type_name']}}</span>
                <span>浏览量{{$info['browse_num']}}万</span>
                <span>{{$info['created_at']}}</span>
            </div>
        </div>
    </section>
    <section class="jianjie">
        <h2>简介</h2>
        <div class="jian_cont">
            {{$info['content']}}
        </div>
    </section>
    <h1 class="dow_tit dowtit_bot"><img src="/uploads/{{$sys['server_img']}}" class="logo">{{$sys['server_name']}}/下载APP查看更多详细信息 <a href="#" class="downloads">下载</a></h1>
</section>

<script>


    //暂时设计每个slide大小需要一致
    //热卖
    var bannerSwiper = new Swiper('.banner', {
        loop: true,
        pagination: {
            el: '.swiper-pagination',
            type: 'fraction',
            renderFraction: function(currentClass, totalClass) {
                return '<span class="' + currentClass + '"></span>' + '/' + '<span class="' + totalClass + '"></span>';
            }
        }
    });
    //判断访问终端
    var browser={
        versions:function(){
            var u = navigator.userAgent, app = navigator.appVersion;
            return {
                trident: u.indexOf('Trident') > -1, //IE内核
                presto: u.indexOf('Presto') > -1, //opera内核
                webKit: u.indexOf('AppleWebKit') > -1, //苹果、谷歌内核
                gecko: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1,//火狐内核
                mobile: !!u.match(/AppleWebKit.*Mobile.*/), //是否为移动终端
                ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), //ios终端
                android: u.indexOf('Android') > -1 || u.indexOf('Adr') > -1, //android终端
                iPhone: u.indexOf('iPhone') > -1 , //是否为iPhone或者QQHD浏览器
                iPad: u.indexOf('iPad') > -1, //是否iPad
                webApp: u.indexOf('Safari') == -1, //是否web应该程序，没有头部与底部
                weixin: u.indexOf('MicroMessenger') > -1, //是否微信 （2015-01-22新增）
                qq: u.match(/\sQQ/i) == " qq" //是否QQ
            };
        }(),
        language:(navigator.browserLanguage || navigator.language).toLowerCase()
    }
    var photo_type = 1;
    if( browser.versions.android == true ){
        photo_type = 1;
    }else if( browser.versions.ios == true){
        photo_type = 2;
    }
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: '/xiazai' ,
        data: {sta:photo_type} ,
        cache: false,
        success:function(data){
            console.log(data)
            $('.downloads').attr('href',data);
        },
        error:function(err){
            console.log(err);
        }
    });

</script>
</body>

</html>