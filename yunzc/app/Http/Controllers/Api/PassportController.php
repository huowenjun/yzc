<?php

namespace App\Http\Controllers\Api;

use App\Models\FilesStorage;
use App\Models\Users;
use App\Models\Verification;
use Carbon\Carbon;
use Encore\Admin\Form\Field\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use phpseclib\Crypt\Hash;
use Validator;
class PassportController extends Controller
{
    const YSE_STATUS  = 200;//状态成功
    const NO_STATUS   = 401;//请求失败
    public $status = array(
      '0'=>'客户端',
      '1'=>'商家端',
    );
    public $term = array(//术语
        self::YSE_STATUS =>'登录成功',
        self::NO_STATUS=>'登录失败',
    );
    const STATUS_CODE_YES = 1;
    const STATUS_CODE_NO = 0;
    const PARAM = '缺少参数';
    const BIND_TEL = '请绑定手机号';
    const BIND_CODE   = 201;
    const BIND_STATUS_YES = 1;
    const BIND_STATUS_NO  = 0;
    const CODE_NO = 0;
    public $bind_term = array(
        self::BIND_STATUS_NO=>'绑定失败',
        self::BIND_STATUS_YES=>'绑定成功',
        self::NO_STATUS=>'验证码错误',
    );
    const PASSWORD_TREM = '未注册，不可修改密码';
    const PASSWORD_STATUS = 0;
    const PASSWORD_YES = 1;
    const PASSWORD_NO = 0;
    public $term_password = array(
        self::PASSWORD_YES =>'密码修改成功',
        self::PASSWORD_NO =>'密码修改失败',
    );
    const LOGIN_TREM = '微信授权失败';
    const LOGIN_WX_STATUS = 0;
    const EDIT_STATUS_YES = 1;
    const EDIT_STATUS_NO = 0;
    public $edit_trem = array(
        self::EDIT_STATUS_NO=>'修改失败',
        self::EDIT_STATUS_YES=>'修改成功',
    );
    const EDIT_USER_NO = '用户不存在';


    /**
     * 登录接口
     * post
     * api/login
     * openid,tel,password,type
     * 商家端与客户端分开登录 禁止同时登录
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(){

        if (empty(request('openid'))) {//app端正常登录
            $userInfo = request()->all();
            if (empty($userInfo) ||
                !array_key_exists('tel',$userInfo) ||
                !array_key_exists('password',$userInfo) ||
                !array_key_exists('mobile_id',$userInfo) ||
                !array_key_exists('type',$userInfo)) {
                return msg_err(self::NO_STATUS,self::PARAM,'');
            }
            return $this->baseLogin($userInfo);
        } else{//app端微信登录
            $usersObj = new Users();
            $usersInfo = $usersObj->sel_login(request('openid'));

            if(isset($usersInfo[0])){//查到数据
                if (empty($usersInfo[0]->tel)) {
                   return msg_err(self::BIND_CODE,self::BIND_TEL,request('openid'));//绑定手机号
                }else {
                    if($usersInfo[0]->api_token){
                        $redis = GetAppToken($usersInfo[0]->api_token);
                        if($redis['code'] == 200){
                            $ids = [];
                            if($usersInfo[0]->mobile_id && $usersInfo[0]->mobile_id != request('mobile_id')){
                                array_push($ids,(string)$usersInfo[0]->mobile_id);
                                $extras = ['online'=>true];
                                $tit = '登录通知';
                                $content = '您的账号已在其他设备进行登录';
                                initPush_admin_login($tit,$content,$ids,$extras);  //当前登录账号已在其他设备进行登录，且token未失效
                            }
                        }
                    }
                    $user = User::find($usersInfo[0]->id);
                    $user->api_token = $token = $user->generateToken();
                    $user->mobile_id = request('mobile_id');
                    $user->save();
                    SetAppToken($token,$usersInfo[0]->id);
                    return msg_ok(self::YSE_STATUS,$this->term[self::YSE_STATUS],[
                        'user_id'=>$usersInfo[0]->id,
                        'name'=>$usersInfo[0]->name,
                        'status'=>$usersInfo[0]->status,
                        'token' => $token,
                        'img'=>$usersInfo[0]->img,
                    ]);
                }
            }else{//无openid数据;创建该数据
                  return msg_ok(self::BIND_CODE,self::BIND_TEL,['openid'=>request('openid')]);
            }


        }

    }

    /**
     *
     * @param $data 用户登录数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function baseLogin($data){
        $userInfo = Users::where(['status'=>intval($data['type']),'tel'=>$data['tel']])->first();
        if(!empty($userInfo))
        {
            if(\Illuminate\Support\Facades\Hash::check($data['password'], $userInfo->password)){

                if($userInfo->api_token){
                    $redis = GetAppToken($userInfo->api_token);
                    if($redis['code'] == 200){
                        $ids = [];
                        if($userInfo->mobile_id && $userInfo->mobile_id != $data['mobile_id']){
                            array_push($ids,(string)$userInfo->mobile_id);
                            $extras = ['online'=>true];
                            $tit = '登录通知';
                            $content = '您的账号已在其他设备进行登录';
                            initPush_admin_login($tit,$content,$ids,$extras);  //当前登录账号已在其他设备进行登录，且token未失效
                        }

                    }
                }

                if(!isset($data['type'])){
                    return msg_err(self::NO_STATUS,'请标明来源');
                }
                $success['token'] =  $userInfo->generateToken();
                $success['name'] =  $userInfo->name;
                $success['user_id'] =  $userInfo->id;
                $success['img'] = $userInfo->img;
                $userInfo->last_login_at = Carbon::now()->toDateTimeString();
                $userInfo->mobile_id = $data['mobile_id'];
                $userInfo->save();
                SetAppToken($success['token'],$success['user_id']);
                return msg_ok(self::YSE_STATUS,$this->term[self::YSE_STATUS],$success);
            } else{
                return msg_err(self::NO_STATUS,$this->term[self::NO_STATUS]);
            }
        }else{
            return msg_err(self::NO_STATUS,$this->term[self::NO_STATUS]);
        }

//        if($bool = Auth::attempt([
//            'tel' => $data['tel'],
//            'password' => $data['password'],
//        ])){
//            $user = Auth::user();
//            if($user['api_token']){
//                $redis = GetAppToken($user['api_token']);
//                if($redis['code'] == 200){
//                    $ids = [];
//                    if($user['mobile_id'] && $user['mobile_id'] != $data['mobile_id']){
//                        array_push($ids,(string)$user['mobile_id']);
//                        $extras = ['online'=>true];
//                        $tit = '登录通知';
//                        $content = '您的账号已在其他设备进行登录';
//                        initPush_admin_login($tit,$content,$ids,$extras);  //当前登录账号已在其他设备进行登录，且token未失效
//                    }
//
//                }
//            }
//
//            if(!isset($data['type'])){
//                return msg_err(self::NO_STATUS,'请标明来源');
//            }
//            if(intval($data['type']) !== $user->status){
//                $str .= $this->status[$user->status];
//                return msg_err(self::NO_STATUS,$str);
//            }
//            $success['token'] =  $user->generateToken();
//            $success['name'] =  $user->name;
//            $success['user_id'] =  $user->id;
//            $success['img'] = $user->img;
//            $user->last_login_at = Carbon::now()->toDateTimeString();
//            $user->mobile_id = $data['mobile_id'];
//            $user->save();
//            SetAppToken($success['token'],$success['user_id']);
//            return msg_ok(self::YSE_STATUS,$this->term[self::YSE_STATUS],$success);
//        }
//        else{
//            return msg_err(self::NO_STATUS,$this->term[self::NO_STATUS]);
//        }
    }

    /**
     * 绑定手机号
     * post
     * tel,code,openid
     * api/band_tel
     */
    public function bindTel()
    {
        $info = request()->all();
        if (empty($info) ||
            !array_key_exists('tel',$info) ||
            !array_key_exists('code',$info) ||
            !array_key_exists('openid',$info)||
            !array_key_exists('mobile_id',$info)
        ) {//验证数据
            return msg_err(self::NO_STATUS,self::PARAM);
        }
        //验证码是否正确
        $verifyCode = new Verification();
        $isCode = $verifyCode->where('code',$info['code'])->where('type',4)->where('tel',$info['tel'])->first();
        if(!$isCode){
            return $this->fail(70002);
        }
        if(strtotime($isCode->expiration) < time()){
            return $this->fail(70001);
        }
        $usersObj = new Users();
        //推荐码，是推荐过来的使用上家推荐码，生成自己新的推荐码。不是推荐过来的，使用新推荐码
        $info['parent_code'] = '';
        if (!isset($info['referral_code'])) {
            $info['referral_code'] = $this->get_rand();
        } else {
            $info['parent_code'] = $info['referral_code'];
            $info['referral_code'] = $this->get_rand();
        }
        $boolInfo = $usersObj->where(['tel'=>$info['tel']])->first();

        if($boolInfo) {//修改
            unset($info['code']);
            $bool = $usersObj->where(['tel'=>$info['tel']])->update($info);
        }else{
            unset($info['code']);
            $bool = User::create($info);
        }
        if ($bool) {//成功
            $userInfo = $usersObj->sel_bind($info['openid']);
            $user = User::find($userInfo[0]->id);
            $user->api_token = $token = $user->generateToken();
            $user->save();
            SetAppToken($token,$userInfo[0]->id);
            return msg_ok(self::YSE_STATUS,$this->bind_term[self::BIND_STATUS_YES],[
                'user_id'=>$userInfo[0]->id,
                'name'=>$userInfo[0]->name,
                'status'=>$userInfo[0]->status,
                'token' => $token,
                'img'=>$userInfo[0]->img,
            ]);
        } else {//失败
            return msg_err(self::NO_STATUS,$this->bind_term[self::BIND_STATUS_NO]);
        }
    }


    /**
     * 推荐码生成
     * @return string
     *
     */
    public function get_rand(){
        $code = strtoupper(str_random(6));
        if (User::where(['referral_code'=>$code])->count()){
            return $this->get_rand();
        }else{
            return $code;
        }
    }


    /**
     * 添加6位推荐码到注册
     * Register api
     * @return \Illuminate\Http\Response
     * 2018-10-22 liulei
     */
    public function register(Request $request)  {
            $validator = Validator::make($request->all(),
                [
                    'tel' => 'required|regex:/^1[3456789][0-9]{9}$/|unique:users',
                    'password' => 'required|min:6',
                    'code' => 'required|numeric',
                    ],[],[
                    'tel' => '手机号',
                    'password' => '密码',
                    'code' => '验证码'
                ]);
            if ($validator->fails()) {
                return $this->fail(7201,$validator->errors()->first());
            }
            $input = $request->all();
            $verifyCode = new Verification();
            $isCode = $verifyCode->where('code',$input['code'])->where('type',1)->where('tel',$input['tel'])->first();
            if(!$isCode){
                return $this->fail(70002);
            }
            if(strtotime($isCode->expiration) < time()){
                return $this->fail(70001);
            }
            $input['name'] = $input['tel'];
            $input['password'] = bcrypt($input['password']);
            $input['first_login_at'] = $input['last_login_at'] = Carbon::now()->toDateTimeString();
            //推荐码，是推荐过来的使用上家推荐码，生成自己新的推荐码。不是推荐过来的，使用新推荐码
            if (!isset($input['referral_code'])) {
                $input['referral_code'] = $this->get_rand();
            } else {
                $input['parent_code'] = $input['referral_code'];
                $input['referral_code'] = $this->get_rand();
            }
            $user = User::create($input);
            $success['token'] =  $user->generateToken();
            $success['name'] =  $user->name;
            $success['user_id'] =  $user->id;
            SetAppToken($success['token'],$success['user_id']);

        return $this->success($success);
        }

    /**
     * 修改密码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_back_password(Request $request){
            $validator = Validator::make($request->all(),
                [
                    'tel' => 'required|regex:/^1[3456789][0-9]{9}$/',
                    'password' => 'required|min:6',
                    'code' => 'required|numeric',
                    'type' => 'required|in:1,2,3,4,5',
                    'status' => 'required|in:0,1',
//                    'c_password' => 'required|same:password',
                ],[],[
                    'tel' => '手机号',
                    'password' => '密码',
                    'code' => '验证码',
                    'type'=>'验证码类型',
                    'status'=>'标明来源'
                ]);
            if ($validator->fails()) {
                return msg_err(401,$validator->errors()->first());
            }
            $input = $request->all();
            $user = User::where(['tel'=>$input['tel'],'status'=>$input['status']])->first();
            if(!$user){
                return msg_err(401,'用户不存在');
            }
            $verifyCode = new Verification();
            $isCode = $verifyCode->where('code',$input['code'])->where('type',$input['type'])->where('tel',$input['tel'])->first();
            if(!$isCode){
                return msg_err(401,'验证码不存在');
            }
            if(strtotime($isCode->expiration) < time()){
                return msg_err(401,'验证码超时');
            }

            $user->password = bcrypt($input['password']);
            $user->save();
            $success['token'] =  $user->generateToken();
            $success['name'] =  $user->name;
            $success['user_id'] =  $user->id;

            SetAppToken($success['token'],$success['user_id']);

            return msg_ok(self::YSE_STATUS,'修改成功',$success);




        }

    /**
     * 个人资料编辑
     * post
     * api/users_info_edit
     * id,img,name
     */
    public function usersInfoEdit()
    {
        $info = request()->all();
        if (empty($info) ||
            !array_key_exists('api_token',$info) ||
            !array_key_exists('img',$info) ||
            !array_key_exists('name',$info)) {//验证数据
            return msg_err(self::NO_STATUS,self::PARAM);
        }
        $redis = GetAppToken($info['api_token']);
        if($redis['code'] == 200){
            $info['id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $userObj = new Users();
        $usersInfo = $userObj->sel_usersinfo($info['id']);
        if(isset($usersInfo[0])){
            $bool = $userObj->save_userinfo($info);
            $success['name'] = $info['name'];
            $success['img'] = $info['img'];
            if($bool == self::EDIT_STATUS_YES){
                return msg_ok(self::YSE_STATUS,$this->edit_trem[self::EDIT_STATUS_YES],$success);
            }else{
                return msg_err(self::NO_STATUS,$this->edit_trem[self::EDIT_STATUS_NO]);
            }
        }else{
            return msg_err(self::NO_STATUS,self::EDIT_USER_NO);
        }
    }

    /**
     * 获取个人资料
     *
     */
    public function getUserInfo()
    {
        $info = request()->all();
        if (empty($info) ||
            !array_key_exists('api_token',$info)) {//验证数据
            return msg_err(self::NO_STATUS,self::PARAM);
        }
        $redis = GetAppToken($info['api_token']);
        if($redis['code'] == 200){
            $info['id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $userObj = new Users();
        $usersInfo = $userObj->sel_user($info['id']);
        $now_time = date('Y-m-d H:i:s');
        $usersInfo->brick_age = intval((strtotime($now_time) - strtotime($usersInfo->created_at))/(24 * 3600));
        return msg_ok(self::YSE_STATUS,'个人资料',$usersInfo);
    }


    /**
     * details api
     * *
     * * @return \Illuminate\Http\Response
     */
    public function getDetails()    {
        $user = Auth::user();
        return $this->success($user);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 测试单张图片上传
     * 2018-10-22 多占图片上传添加
     */
    public function upload(Request $request)
    {

            $files = $request->allFiles();

            if(!$files){
                return msg_err(401, '文件不存在');
            }
            $return = [];
            //文件是否上传成功
        if(is_array($files['images'])){
            $file_a = $files['images'];
        }else{
            $file_a = $files;
        }
        foreach ($file_a as $files) {
            if ($files && $files->isValid()) {
                //取原文件名
                $originalName = $files->getClientOriginalName();
                //取扩展名
                $ext = $files->getClientOriginalExtension();
                //取文件类型
                $type = $files->getClientMimeType();
                //临时文件的绝对路径
                $realPath = $files->getRealPath();
                //定义文件名
                $fileName = 'api/' . date("YmdHis") . '-' . uniqid() . '.' . $ext;
//                $img = Image::make('foo.jpg')->resize(300, 200);
                //uploads为config/filesystems里自定义
                $bool = Storage::disk('admin')->put($fileName, file_get_contents($realPath));
                $dd['originalName'] = $originalName;
                $dd['ext'] = $ext;
                $dd['type'] = $type;
                $dd['realPath'] = $realPath;
                $dd['fileName'] = $fileName;
                FilesStorage::create($dd);
                $return[] = $dd;
            } else {
//                return msg_err(401, '文件不存在', null);
//                $return[] = '文件不存在';
                continue;
            }
        }

        return msg_ok(1, 'success', $return);

    }
    /*多文件上传*/
    public function multipleImage(Request $request){
        $image = [];
        $file = $request->file('images');

        $filename = $request->get('filename')?:'images';
        if(!empty($file)){
            foreach($file as $k=>$v ){
                $info = $v->store($filename.'/'.date('Ymd'));
                $image[$k]=$info;
            }
        }
        if($image){
            return msg_ok(200,'上传成功',$image);
        }else{
            return msg_ok(401,'上传失败');
        }

    }
    public function file_upload(Request $request){
        $files = $request->allFiles();

        if(!$files){
            return msg_err(401, '文件不存在');
        }
        $return = [];
        //文件是否上传成功
        if(is_array($files['images'])){
            $file_a = $files['images'];
        }else{
            $file_a = $files;
        }
        foreach ($file_a as $files) {
            if ($files && $files->isValid()) {
                //取原文件名
                $originalName = $files->getClientOriginalName();
                //取扩展名
                $ext = $files->getClientOriginalExtension();
                //取文件类型
                $type = $files->getClientMimeType();
                //临时文件的绝对路径
                $realPath = $files->getRealPath();
                //定义文件名
                $fileName = 'api/' . date("YmdHis") . '-' . uniqid() . '.' . $ext;
//                $img = Image::make('foo.jpg')->resize(300, 200);
                //uploads为config/filesystems里自定义
                $bool = Storage::disk('admin')->put($fileName, file_get_contents($realPath));
                $dd['originalName'] = $originalName;
                $dd['ext'] = $ext;
                $dd['type'] = $type;
                $dd['realPath'] = $realPath;
                $dd['fileName'] = $fileName;
                FilesStorage::create($dd);
                $return[] = $dd;
            } else {
//                return msg_err(401, '文件不存在', null);
//                $return[] = '文件不存在';
                continue;
            }
        }
    }
    /*单文件上传*/
    public function images(Request $request){
        $type = $request->get('type') ? :2;
        $filename = $request->get('filename')?:'images';
        if($type == 2){
            $filepath = $this->saveBase64File($request->get('images'),'',$filename);
            if($filepath){
                return msg_ok(200,'上传成功',$filepath);
            }else{
                return msg_ok(401,'上传失败');
            }
        }else{
            $files = $request->file('images');
            if(!$files){
                return msg_err(401, '文件不存在');
            }
            if ($files && $files->isValid()) {
                //取扩展名
                $ext = $files->getClientOriginalExtension();
                //临时文件的绝对路径
                $realPath = $files->getRealPath();
                $fileName = $filename.'/' . date("Ymd").'/'.date("YmdHis") . '-' . uniqid() . '.' . $ext;
                $bool = Storage::disk('admin')->put($fileName, file_get_contents($realPath));
                if($bool){
                    return msg_ok(200,'上传成功',$fileName);
                }else{
                    return msg_err(401,'上传失败');
                }
            }

        }

    }

    public function saveBase64File($img,$name='',$filename='imagse'){
        $base_img = str_replace('data:image/jpeg;base64,','', $img);
        //  设置文件路径和文件前缀名称
        $upload = "{$filename}/".date('Ymd').'/';
        $path = "./uploads/".$upload;
        $prefix='';
        if($name ==''){
            $output_file = md5($prefix.time().rand(100,999)).'.jpg';
        }else{
            $output_file = $name;
        }
        mkdirs($path);
        $path = $path.$output_file;
        //  创建将数据流文件写入我们创建的文件内容中
        $ifp = fopen( $path, "wb" );
        fwrite( $ifp, base64_decode( $base_img) );
        fclose( $ifp );
        // 第二种方式
        // file_put_contents($path, base64_decode($base_img));
        // 输出文件
        // print_r($output_file);
        $path=$upload.$output_file;
        return $path;
    }
    /**/
    public function uploadAudio(Request $request){


//        dd($request->hasFile('file_upload'));
//
//        die;

        if ($request->hasFile('file_upload'))
        {
            $file = $request->file('file_upload');
        $allowed_extensions = ['jpg','png','bmp','jpeg'];

        if($file->getClientOriginalExtension() && !in_array($file->getClientOriginalExtension(), $allowed_extensions))
        {
            return ['error' => 'You may only upload png, jpg or gif.'];
        }
        $baseName =md5(time().str_random(10).uniqid());
        $newName = $baseName.'.'.$file->getClientOriginalExtension();
            $saveDir = 'upload/'.date('Y').'/'.date('m').'/'.date('d').'/';
            $savePath = $saveDir.$newName;
        $bytes = Storage::put($savePath, file_get_contents($file->getRealPath()));
        if (Storage::exists($savePath))
            return ['data'=>$savePath];
//            return response()->json(['code'=>200,'msg'=>'上传成功','data'=>['uri'=>$savePath]]);
        }
//            return response()->json(['code'=>500,'msg'=>'上传失败']);
    }





















}
