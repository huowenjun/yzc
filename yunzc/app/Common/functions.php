<?php
use Illuminate\Support\Facades\Storage;
use App\Aipimagessearch\AipImageSearch;
use App\Models\AipImageSearchLog;
/**
 * Created by PhpStorm.
 * User: liule
 * Date: 2018/10/8
 * Time: 9:27
 */

/**
 * 公用的方法  返回json数据，进行信息的提示
 * @param $status 状态
 * @param string $message 提示信息
 * @param array $data 返回数据
 */
function msg_err($status,$message = '',$data = ''){
    $data = new stdClass();
    $result = [
            'code' => $status,
            'message' =>$message,
            'data' =>$data,
    ];

    return response()->json($result,200);
}

/**
 * 公用的方法  返回json数据，进行信息的提示
 * @param $status 状态
 * @param string $message 提示信息
 * @param array $data 返回数据
 */
function msg_ok($status,$message = '',$data = array()){
    $result = [
          'code' => $status,
          'message' =>$message,
          'data' =>$data,
    ];
    return response()->json($result,200);
}

//是否是GET提交
function isGet(){
    return $_SERVER['REQUEST_METHOD'] == 'GET' ? true : false;
}

//是否是POST提交
function isPost(){
    return ($_SERVER['REQUEST_METHOD'] == 'POST') ? true : false;
}
/*添加用户token redis缓存*/
function SetAppToken($key,$name,$time =2592000){
    $redis = new \Illuminate\Support\Facades\Redis;
    return $redis::setex($key,$time,$name);
}
/*获取用户token redis缓存*/
function GetAppToken($key){
    $redis = new \Illuminate\Support\Facades\Redis;
    if($redis::exists($key)){
        $u_id = $redis::get($key);
        if($u_id){
            return ['code'=>200,'msg'=>'获取成功','u_id'=>$u_id];
        }else{
            return ['code'=>301,'msg'=>'token失效或不存在'];
        }
    }else{
        return ['code'=>416,'msg'=>'token已失效或不存在'];
    };
}
function err($messger,$data=false)
{
    return [
        'code'=>'401',
        'message'=>$messger,
        'data'=>$data,
    ];
}

/**
 * 单条数据转对象
 * @param $data
 * @return stdClass
 */
function arr($data)
{
    if(isset($data[0])) {
        return $data[0];
    }else{
        return new stdClass();
    }
}

/**
 * 处理数据，转义数据
 */
function handleData($data)
{
    foreach ($data as $k=>$v)
    {
        if($k !== 'photo'){
            $data[$k] = htmlentities($v);
        }
    }
    return $data;
}

/**
 * 过滤敏感词
 */
function Sensitive($txt)
{
    $sen = new \Yankewei\LaravelSensitive\Sensitive();
    $interference = ['&','*'];
    $filename = './words.txt';    //每个敏感词独占一行
    $sen->interference($interference);    //添加干扰因子
    $sen->addwords($filename);   //需要过滤的敏感词
    $words = $sen->filter($txt);
    return $words;
}

/**
 * 接口返回的分页数据拼接转换
*/
function obj_array($obj,$sta = 1){
    $list = $obj->toArray();
    $list2['current_page'] = $list['current_page'];
    $list2['last_page'] = $list['last_page'];

    if($sta == 2){
        $list2['total'] = $list['total'];
    }
    $list2['data'] = $list['data'];
    return $list2;
}
function mkdirs($dir, $mode = 0777)
{
    if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
    if (!mkdirs(dirname($dir), $mode)) return FALSE;
    return @mkdir($dir, $mode);
}

function res_time($res)
{
    if(isset($res->created_at)){
        $res->created_at = strtotime($res->created_at);
        $res->now_time = strtotime(date('Y-m-d H:i:s'));
    }
    return $res;
}

function set_release_num($uid,$type='asc'){
    if(!$uid){
        return false;
    }
    $redis = new \Illuminate\Support\Facades\Redis;
    $uname = $uid.'release';
    if($type == 'asc'){
       $res = $redis::incr($uname);
    }elseif($type == 'desc'){
       $res =$redis::decr ($uname);
    }
    if(!$res){
        return false;
    }else{
        return true;
    }

}
function get_release_num($uid){
    if(!$uid){
        return false;
    }
    $redis = new \Illuminate\Support\Facades\Redis;
    $uname = $uid.'release';
    return $redis::get($uname)?:0;
}
//读取图片路径json_decode
function json_decode_photo($path)
{
    if(isset($path)){
        return json_decode($path);
    }
    return array();

}

// 推送
/**
 *@param $title 推送标题
 *@param $content 推送内容
 *@param $alias 推送人员数组
 */
function initPush_admin($title = '', $content = '', $alias = array())
{
    if(empty($content) || empty($alias)){
        return false;
    }
    $title = empty($title) ? '云砖城系统消息' : $title;
    $client = new \JPush\Client('ea1b33fd3f89f442e51e1a7b', 'aa90e5b5400fdaf95825147c');

    $pusher = $client->push();
    $pusher->setPlatform(array('ios', 'android'));

    $pusher->addAlias($alias);
    $pusher->iosNotification($content, array(
        'sound' => 'default',
    ));
    $pusher->androidNotification($content, array(
        'title' => $title,
    ));

    $pusher->options(array(
        "apns_production" => false  //true表示发送到生产环境(默认值)，false为开发环境
    ));
    try {
       $r =  $pusher->send();
        return $r;
    } catch (\JPush\Exceptions\JPushException $e) {
        // try something else here
            return ['code'=>$e->getCode(),'zh_msg'=>jpush_msg($e->getCode()),'en_msg'=>$e->getMessage()];

    }
}
/**
 *   用户登录  若在其他设备有登录，推送一条消息
 *@param $title 推送标题
 *@param $content 推送内容
 *@param $alias 推送人员数组
 */
function initPush_admin_login($title = '',$content ='',$alias = array(),$extras=array())
{
    if(empty($content) || empty($alias)){
        return false;
    }
    $title = empty($title) ? '云砖城系统消息' : $title;
    $client = new \JPush\Client('ea1b33fd3f89f442e51e1a7b', 'aa90e5b5400fdaf95825147c');

    $pusher = $client->push();
    $pusher->setPlatform(array('ios', 'android'));
    $pusher->addAlias($alias);
    $pusher->message($content, [
        'title' => $title,
        'content_type' => 'text',
        'extras' => $extras
    ]);
    try {
        $r =  $pusher->send();
        return $r;
    } catch (\JPush\Exceptions\JPushException $e) {
        // try something else here
        return ['code'=>$e->getCode(),'zh_msg'=>jpush_msg($e->getCode()),'en_msg'=>$e->getMessage()];

    }
}
function jpush_msg($code){
    switch($code){
        case 10:
            return '系统内部错误';
            break;
        case 1001:
            return '只支持 HTTP Post 方法，不支持 Get 方法';
            break;
        case 1002:
            return '缺少了必须的参数';
            break;
        case 1003:
            return '参数值不合法';
            break;
        case 1004:
            return 'verification_code 验证失败';
            break;
        case 1005:
            return '消息体太大';
            break;
        case 1007:
            return 'receiver_value 参数 非法';
            break;
        case 1008:
            return 'appkey参数非法';
            break;
        case 1010:
            return 'msg_content 不合法';
            break;
        case 1011:
            return '没有满足条件的推送目标';
            break;
        case 1012:
            return 'iOS 不支持推送自定义消息。只有 Android 支持推送自定义消息。';
            break;
        case 1013:
            return 'content-type 只支持 application/x-www-form-urlencoded';
            break;
        case 1014:
            return '消息内容包含敏感词汇。';
            break;
        case 1030:
            return '内部服务超时。稍后重试。';
            break;
    }
}
function initPush_admin_all($title = '', $content = ''){
    if(empty($content)){
        return false;
    }
    $title = empty($title) ? '云砖城系统消息' : $title;
    $client = new \JPush\Client('ea1b33fd3f89f442e51e1a7b', 'aa90e5b5400fdaf95825147c');
    $pusher = $client->push() // 调用push方法（返回一个PushPayload实例）
    ->setPlatform(array('ios', 'android')) // 设置平台
    ->addAllAudience() // 设置所有设备都推送
    ->setNotificationAlert($title,$content); // 设置推送通知内容
    try {
        $r = $pusher->send();
        return $r;
    } catch (\JPush\Exceptions\JPushException $e) {
        // try something else here
        return ['code'=>$e->getCode(),'zh_msg'=>jpush_msg($e->getCode()),'en_msg'=>$e->getMessage()];
    }
}

function tel_preg($tel)
{
    if(preg_match("/^1[34578]\d{9}$/", $tel)){
        return 1;
    }
    return 0;
}
const APP_ID = '14704969';
const API_KEY = 'GtSrqiSOlLXGbyanWtCx2rBM';
const SECRET_KEY = 'fjZ3CqPyGmIvW74rGSWIiEOlBxSRDWvD';
/**
 * 相同图入库
 * $imagespath 本地的图片的路径 ，单个或多个组成的数组
 * $type  类型  1：本地图片路径  2：网络图片路径 带域名 的
 * $filepath  './uploads/' 图片的路基
 * $brief  入库时需要同步提交图片及可关联至本地图库的摘要信息（具体变量为brief，具体可传入图片在本地标记id、图片url、图片名称等） 检索时原样带回,最长256B
 * tags   1 - 65535范围内的整数，tag间以逗号分隔，最多2个tag。样例："100,11" ；检索时可圈定分类维度进行检索（分类维度信息，最多可传入2个tag）
 */
function sameHqAdds($imagespath,$brief = null,$tags='',$type=1,$filepath='./uploads/'){
    $brief = is_array($brief)?json_encode($brief):$brief;   /*是数组的话转为json*/
    $files = array();
    if(!is_array($imagespath)){
        $files[0] = $imagespath;
    }else{
        $files = $imagespath;
    }
    $client = new AipImageSearch(APP_ID, API_KEY, SECRET_KEY);
    $info = [];
    foreach($files as $k=>$path){
        if($type == 1){
            $isexists = \Storage::exists($path);
            if($isexists){
                $image = file_get_contents($filepath.$path);
                // 如果有可选参数
                $options = array();
                $options["brief"] = $brief;
                $options["tags"] = $tags;
                // 带参数调用相同图检索—入库, 图片参数为本地图片
                $info[$k] = $client->sameHqAdd($image, $options);
            }else{
                $info[$k] = false;
            }
        }elseif($type == 2){
            // 如果有可选参数
            $options = array();
            $options["brief"] = $brief;
            $options["tags"] = $tags;
            // 带参数调用相同图检索—入库, 图片参数为远程url图片
            $info[$k] = $client->sameHqAddUrl($path, $options);
        }
    }
    return $info;
}

/**
 * 相似图入库
 * $imagespath 本地的图片的路径 ，单个或多个组成的数组
 * $type  类型  1：本地图片路径  2：网络图片路径 带域名 的
 * $filepath  './uploads/' 图片的路基
 * $brief  入库时需要同步提交图片及可关联至本地图库的摘要信息（具体变量为brief，具体可传入图片在本地标记id、图片url、图片名称等） 检索时原样带回,最长256B
 * tags   1 - 65535范围内的整数，tag间以逗号分隔，最多2个tag。样例："100,11" ；检索时可圈定分类维度进行检索（分类维度信息，最多可传入2个tag）
 */
function similarAdd($imagespath,$brief = null,$tags='',$type=1,$filepath='./uploads/'){
    $brief = is_array($brief)?json_encode($brief):$brief;   /*是数组的话转为json*/
    $files = array();
    if(!is_array($imagespath)){
        $files[0] = $imagespath;
    }else{
        $files = $imagespath;
    }
    $client = new AipImageSearch(APP_ID, API_KEY, SECRET_KEY);
    $info = [];
    foreach($files as $k=>$path){
        if($type == 1){
            $isexists = \Storage::exists($path);
            if($isexists){
                $image = file_get_contents($filepath.$path);
                // 如果有可选参数
                $options = array();
                $options["brief"] = $brief;
                $options["tags"] = $tags;
                // 带参数调用相同图检索—入库, 图片参数为本地图片
                $info[$k] =  $client->similarAdd($image, $options);
            }else{
                $info[$k] =  false;
            }
        }elseif($type == 2){
            // 如果有可选参数
            $options = array();
            $options["brief"] = $brief;
            $options["tags"] = $tags;
            // 带参数调用相同图检索—入库, 图片参数为远程url图片
            $info[$k] =  $client->similarAddUrl($path, $options);
        }
    }
    return $info;
}
/**
 * 商品检索入库
 * $imagespath 本地的图片的路径 ，单个或多个组成的数组
 * $type  类型  1：本地图片路径  2：网络图片路径 带域名 的
 * $filepath  './uploads/' 图片的路基
 * $brief  否 入库时需要同步提交图片及可关联至本地图库的摘要信息（具体变量为brief，具体可传入图片在本地标记id、图片url、图片名称等） 检索时原样带回,最长256B  （但是不传会报错）
 * $class_id1 否  商品分类维度1，支持1-60范围内的整数。检索时可圈定该分类维度进行检索
 * $class_id2 否  商品分类维度1，支持1-60范围内的整数。检索时可圈定该分类维度进行检索
 */
function productAdd($imagespath,$brief = null,$type=1,$filepath='./uploads/',$class_id1=1,$class_id2=1){
    $brief = is_array($brief)?json_encode($brief):$brief;  /*是数组的话转为json*/
    $files = array();
    if(!is_array($imagespath)){
        $files[0] = $imagespath;
    }else{
        $files = $imagespath;
    }
    $client = new AipImageSearch(APP_ID, API_KEY, SECRET_KEY);
    $info = [];
    foreach($files as $k=>$path){
        if($type == 1){
            $isexists = \Storage::exists($path);
            if($isexists){
                $image = file_get_contents($filepath.$path);
                // 如果有可选参数
                $options = array();
                $options["brief"] = $brief;
                $options["class_id1"] = $class_id1;
                $options["class_id2"] = $class_id2;
                // 带参数调用相同图检索—入库, 图片参数为本地图片
                $info[$k] =  $client->productAdd($image, $options);
            }else{
                $info[$k] =  false;
            }
        }elseif($type == 2){
            // 如果有可选参数
            $options = array();
            $options["brief"] = $brief;
            $options["class_id1"] = $class_id1;
            $options["class_id2"] = $class_id2;
            // 带参数调用相同图检索—入库, 图片参数为远程url图片
            $info[$k] =  $client->productAddUrl($path, $options);
        }
    }
    return $info;
}

/**
 * 超过9999显示万
 */
function num2tring($num) {
    if ($num >= 10000) {
        $num = round($num / 10000 * 100) / 100 .' 万';
    } else {
        $num = "$num";
    }
    return $num;
}

/*截取两个不同字符之间的内容*/
function get_between($input, $start, $end) {
    $substr = substr($input, strlen($start)+strpos($input, $start),(strlen($input) - strpos($input, $end))*(-1));
    return $substr;
}

function objectA($objectD){
    if(!isset($objectD[0])){
       return ;
    }
    foreach ($objectD as $k=>$v){
        $objectA[$k]=$v;
    }
    return $objectA;
}

function dataPage($data)
{
    $page = ($data['page']-1)*10;
    $d['data'] = array_slice($data['data'],$page,10);
    $d['last_page'] = ceil(count($data['data'])/10);
    $d['current_page'] = intval($data['page']);
    return $d;
}
//获取论坛来源
function forums_com($sta,$type){
    switch($sta)
    {
        case "2":   //品牌
            $name = \App\Models\Brand::where('id',$type)->value('name');
            break;
        case "3":   //地区
            $name = \App\Models\Cities::where('area_code',$type)->value('area_name');
            break;
        case "4":    //主题
            $name = \App\Models\Theme::where('id',$type)->value('name');
            break;
        default:
            $name = "未知";
            break;
    }
    return $name?$name:'未知';
}
// 展示图片压缩
function resize_img_src($img,$w =50,$h=50){
    return getenv('APP_URL').'/Img/'.base64_encode($img)."/$w/$h";
}
