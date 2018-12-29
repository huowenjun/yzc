<?php

namespace App\Http\Controllers\Api;
use Validator;
use App\Models\Feedback;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FeedbackControlerodel extends Controller
{
    /*保存反馈意见*/
    public function add_feedback(Request $request){
        $validator = Validator::make($request->all(),
            [
                'content' => 'required|max:10000',
                'mobile' => 'required|regex:/^1[3456789][0-9]{9}$/',
                'type' => 'required|numeric',
            ],[],[
                'content' => '反馈内容',
                'mobile' => '手机号',
                'type' => '用户类型',
            ]);
        if ($validator->fails()) {
            return $this->fail(401,$validator->errors()->first());
        }
        $data = $request->all();
        $redis = GetAppToken($request->get('api_token'));
        if($redis['code'] == 200){
            $data['u_id'] = $redis['u_id'];
        }else{
            return msg_err($redis['code'],$redis['msg']);
        }
        $where ['u_id'] = $data['u_id'];
        $where ['mobile'] = $data['mobile'];
        $where ['content'] = $data['content'];
        $where ['type'] = $data['type'];
        $single_num = Feedback::where($where)->count();
        if($single_num){
            return msg_err(401,'已提交过相同的反馈意见，操作失败');
        }
        $r = Feedback::create($data);
        if($r->id){
            return msg_ok(200,'提交成功');
        }else{
            return msg_err(401,'提交失败');
        }
    }
}
