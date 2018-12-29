<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/11
 * Time: 13:25
 */
namespace chuanglan;


class ChuanglanSmsApi {



    protected $chuanglan_config;


    
    public function __construct()
    {
        $this->chuanglan_config['api_send_url'] = 'http://smsbj1.253.com/msg/send/json';

//创蓝变量短信接口URL, 请求地址请参考253云通讯自助通平台查看或者询问您的商务负责人获取
        $this->chuanglan_config['API_VARIABLE_URL'] = 'http://xxx/msg/variable/json';

//创蓝短信余额查询接口URL, 请求地址请参考253云通讯自助通平台查看或者询问您的商务负责人获取
        $this->chuanglan_config['api_balance_query_url'] = 'http://xxx/msg/balance/json';
//创蓝账号 替换成你自己的账号
        $this->chuanglan_config['api_account']	= 'N7343955';

//创蓝密码 替换成你自己的密码
        $this->chuanglan_config['api_password']	= 'tYFGzRKyI79295';
    }

    /**
     * 发送短信
     *
     * @param string $mobile 		手机号码
     * @param string $msg 			短信内容
     * @param string $needstatus 	是否需要状态报告
     */
    public function sendSMS( $mobile, $msg, $needstatus = 'true') {


        //创蓝接口参数
        $postArr = array (
            'account'  =>  $this->chuanglan_config['api_account'],
            'password' => $this->chuanglan_config['api_password'],
            'msg' => urlencode($msg),
            'phone' => $mobile,
            'report' => $needstatus
        );
        $result = $this->curlPost( $this->chuanglan_config['api_send_url'] , $postArr);
        return $result;
    }

    /**
     * 发送变量短信
     *
     * @param string $msg 			短信内容
     * @param string $params 	最多不能超过1000个参数组
     */
    public function sendVariableSMS( $msg, $params) {


        //创蓝接口参数
        $postArr = array (
            'account' => $this->chuanglan_config['api_account'],
            'password' =>$this->chuanglan_config['api_password'],
            'msg' => $msg,
            'params' => $params,
            'report' => 'true'
        );

        $result = $this->curlPost( $this->chuanglan_config['API_VARIABLE_URL'], $postArr);
        return $result;
    }


    /**
     * 查询额度
     *
     *  查询地址
     */
    public function queryBalance() {


        //查询参数
        $postArr = array (
            'account' => $this->chuanglan_config['api_account'],
            'password' => $this->chuanglan_config['api_password'],
        );
        $result = $this->curlPost($this->chuanglan_config['api_balance_query_url'], $postArr);
        return $result;
    }

    /**
     * 通过CURL发送HTTP请求
     * @param string $url  //请求URL
     * @param array $postFields //请求参数
     * @return mixed
     */
    private function curlPost($url,$postFields){
        $postFields = json_encode($postFields);
        $ch = curl_init ();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8'
            )
        );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt( $ch, CURLOPT_TIMEOUT,1);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
        $ret = curl_exec ( $ch );
        if (false == $ret) {
            $result = curl_error(  $ch);
        } else {
            $rsp = curl_getinfo( $ch, CURLINFO_HTTP_CODE);
            if (200 != $rsp) {
                $result = "请求状态 ". $rsp . " " . curl_error($ch);
            } else {
                $result = $ret;
            }
        }
        curl_close ( $ch );
        return $result;
    }

}