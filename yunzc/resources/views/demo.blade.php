<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,height=device-height, user-scalable=no,initial-scale=1, minimum-scale=1, maximum-scale=1,target-densitydpi=device-dpi">
    <title>demo</title>
    <link rel="stylesheet" href="./css/style3.css">
</head>
<body>
<div class="zhao_con one">
    <div id="demo">

    </div>
    <button type="button" onclick="aa()" style="position: fixed;bottom: 50px;left: 60px">追加</button>
</div>
<script type="text/javascript" src="./js/common.js"></script>
<script type="text/javascript" src="./js/app.js"></script>
<script>
    aa();
    num =1;
    function aa(){
//        $.get("http://yzc.com/api/random_forum",{},function(data){
//            console.log(data);
//            html='<hr>'+num+'<br/>';
//            $.each(data.data,function(i,v){
//                html+="id:"+ v.id+'标题'+ v.title+'<br/><hr>';
//            })
//            $("#demo").append(html);
//            num++;
//        })
        $.post("http://yzc.com/api/login",{openid:1231,tel:1515,password:21235,type:1},function(data){
            console.log(data);
            html='<hr>'+num+'<br/>';
            html+=data.message;
//            $.each(data.data,function(i,v){
//                html+="id:"+ v.id+'标题'+ v.title+'<br/><hr>';
//            })
            $("#demo").append(html);
            num++;
        })
    }

</script>
</body>
          