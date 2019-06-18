<?php

namespace App\Http\Controllers\Pay;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\OrderModel;

class AlipayController extends Controller
{


    public $app_id;
    public $gate_way;
    public $notify_url;
    public $return_url;
    public $rsaPrivateKeyFilePath;
    public $aliPubKey;


    public function __construct()
    {
        $this->app_id = env('ALIPAY_APPID');
        $this->gate_way = 'https://openapi.alipaydev.com/gateway.do';
        $this->notify_url = env('ALIPAY_NOTIFY_URL');
        $this->return_url = env('ALIPAY_RETURN_URL');
        $this->rsaPrivateKeyFilePath = storage_path('app/keys/alipay/priv.key');    //应用私钥
        $this->aliPubKey = storage_path('app/keys/alipay/ali_pub.key'); //支付宝公钥
    }


    public function test()
    {
        echo $this->aliPubKey;echo '</br>';
        echo $this->rsaPrivateKeyFilePath;echo '</br>';
    }


    /**
     * 订单支付
     * @param $oid
     */
    public function pay($oid)
    {

        //验证订单状态 是否已支付 是否是有效订单
        $order_info = OrderModel::where(['oid'=>$oid])->first()->toArray();
        echo '<pre>';print_r($order_info);echo '</pre>';echo '<hr>';

        //判断订单是否已被支付
        if($order_info['pay_time']>0){
            die("订单已支付，请勿重复支付");
        }
        //判断订单是否已被删除
        if($order_info['is_delete']==1){
            die("订单已被删除，无法支付");
        }

        //业务参数
        $bizcont = [
            'subject'           => 'Lening-Order: ' .$oid,
            'out_trade_no'      => $oid,
            'total_amount'      => $order_info['order_amount'] / 100,
            'product_code'      => 'QUICK_WAP_WAY',
        ];

        //公共参数
        $data = [
            'app_id'   => $this->app_id,
            'method'   => 'alipay.trade.wap.pay',
            'format'   => 'JSON',
            'charset'   => 'utf-8',
            'sign_type'   => 'RSA2',
            'timestamp'   => date('Y-m-d H:i:s'),
            'version'   => '1.0',
            'notify_url'   => $this->notify_url,        //异步通知地址
            'return_url'   => $this->return_url,        // 同步通知地址
            'biz_content'   => json_encode($bizcont),
        ];

        //签名
        $sign = $this->rsaSign($data);
        $data['sign'] = $sign;
        $param_str = '?';
        foreach($data as $k=>$v){
            $param_str .= $k.'='.urlencode($v) . '&';
        }

        $url = rtrim($param_str,'&');
        $url = $this->gate_way . $url;
        header("Location:".$url);       // 重定向到支付宝支付页面
    }


    public function rsaSign($params) {
        return $this->sign($this->getSignContent($params));
    }

    protected function sign($data) {

        $priKey = file_get_contents($this->rsaPrivateKeyFilePath);
        $res = openssl_get_privatekey($priKey);

        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');

        openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);

        if(!$this->checkEmpty($this->rsaPrivateKeyFilePath)){
            openssl_free_key($res);
        }
        $sign = base64_encode($sign);
        return $sign;
    }


    public function getSignContent($params) {
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {

                // 转换成目标字符集
                $v = $this->characet($v, 'UTF-8');
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }

        unset ($k, $v);
        return $stringToBeSigned;
    }


    protected function checkEmpty($value) {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;

        return false;
    }

    /**
     * 转换字符集编码
     * @param $data
     * @param $targetCharset
     * @return string
     */
    function characet($data, $targetCharset) {

        if (!empty($data)) {
            $fileType = 'UTF-8';
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
            }
        }
        return $data;
    }


    /**
     * 支付宝异步通知
     */
    public function notify()
    {
        $p = json_encode($_POST);
        $log_str = "\n>>>>>> " .date('Y-m-d H:i:s') . ' '.$p . " \n";
        file_put_contents('logs/alipay_notify',$log_str,FILE_APPEND);
        echo 'success';

        //TODO 验签 更新订单状态
    }

    /**
     * 支付宝同步通知
     */
    public function aliReturn()
    {
        echo '<pre>';print_r($_GET);echo '</pre>';
    }



    //调起支付宝支付
    public function aliTest()
    {

        $ali_gateway = 'https://openapi.alipaydev.com/gateway.do';
        //1 组合参数
        $app_param = [
            'subject'       => '测试订单-'.time() . mt_rand(11111,99999),
            'out_trade_no'  => 0615 . time(). '_' . mt_rand(11111,99999),
            'total_amount'  => mt_rand(1,999) / 100,
            'product_code'  => 'QUICK_WAP_WAY'
        ];

        $pub_param = [
            'app_id'    => '2016092500593666',          //沙箱账号
            'method'    => 'alipay.trade.wap.pay',    //手机网站支付
            'charset'   => 'utf-8',
            'sign_type' => 'RSA2',
            'timestamp' => date('Y-m-d H:i:s'),
            'version'   => '1.0',
            'biz_content'   => json_encode($app_param),                  //业务参数 json
        ];
        echo '<pre>';print_r($pub_param);echo '</pre>';echo '<hr>';

        // 2 计算签名
        ksort($pub_param);

        echo '<pre>';print_r($pub_param);echo '</pre>';echo '<hr>';
        $str = "";
        foreach($pub_param as $k=>$v){
            $str .= $k . '=' . $v . '&';
        }
        echo $str;
        $str = rtrim($str,'&');

        //私钥签名
        openssl_sign($str,$signature,openssl_get_privatekey("file://".storage_path('keys/priv.pem')),OPENSSL_ALGO_SHA256);

        $pub_param['sign'] = base64_encode($signature);echo '<hr>';
        echo '<pre>';print_r($pub_param);echo '</pre>';

        $url_param = "";
        foreach($pub_param as $k=>$v){
            $url_param .= $k . '=' . urlencode($v) . '&';
        }

        $request_url = $ali_gateway . '?' .$url_param;echo '<hr>';
        echo $request_url;

        header("Location:".$request_url);


    }
}
