<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class TestController extends Controller
{
    //
    public function testGet(Request $request)
    {
        //echo $request->input('a');echo '</br>';
        //echo $request->input('b');echo '</br>';
        $arr = $request->toArray();
        echo '<pre>';print_r($arr);echo '</pre>';echo '<hr>';
        $u = $request->all();
        echo '<pre>';print_r($u);echo '</pre>';echo '<hr>';
        $c = $request->cookie();
        echo '<pre>';print_r($c);echo '</pre>';echo '<hr>';
        $h = $request->header();
        //echo '<pre>';print_r($h);echo '</pre>';echo '<hr>';

        $file = $request->file('xxx');
        $file->getClientOriginalExtension();

        $ua = $request->header('user-agent');           //获取user-agent

        echo '<pre>';print_r($_SERVER);echo '</pre>';

    }

    public function upload1()
    {
        return view('test.upload1');
    }


    public function upload2(Request $request)
    {
        echo '<pre>';print_r($_FILES);echo '</pre>';echo '<hr>';

        $file = $request->file('xxx');
        echo '<pre>';print_r($file);echo '</pre>';

        $origin_name = $file->getClientOriginalName();
        echo 'originName: '.$origin_name;echo '</br>';
        $ext = $file->getClientOriginalExtension();
        echo 'Ext: '.$ext;echo '</br>';

        $new_file_name = Str::random(8). '.' . $ext;
        echo 'New File Name: '.$new_file_name;echo '<hr>';

        $path = date('Y-m-d');
        $rs = $file->storeAs($path,$new_file_name);     //保存文件 默认位置 storage/app
        var_dump($rs);
    }


    /**
     * 对称加密
     */
    public function secretTest()
    {
        $data = [
            'nickname'  => 'zhangsan',
            'email'     => 'zhangsan@qq.com',
            'age'       => 11,
            'bank_id'   => '112233445567'
        ];

        $method = 'AES-256-CBC';
        $pass = 'xxyyzz';
        $iv = '1809a1809a1809aa';

        //加密数据
        $json_str = json_encode($data);     //将发送的数据 json格式化
        $send_data = base64_encode( openssl_encrypt($json_str,$method,$pass,OPENSSL_RAW_DATA,$iv) );

        echo $send_data;echo '<hr>';
        //发送数据

        $api_url = 'http://api.1809a.com/test/sec';


        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$api_url);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$send_data);     // raw 格式
        curl_setopt($ch,CURLOPT_HTTPHEADER,[
            'Content-Type:text/plain'
        ]);

        $response = curl_exec($ch);

        //监控错误码
        $err_code = curl_errno($ch);

        if($err_code>0){
            echo "CURL 错误码:".$err_code;
            exit;
        }
        curl_close($ch);
    }


    /**
     * 非对称加密
     */
    public function rsaTest()
    {

        $data = [
            'nickname'  => 'lisi',
            'email'     => 'lisi@qq.com',
            'age'       => 22,
            'bank_id'   => 'weoriuweroi'
        ];
        //加密数据

        $json_str = json_encode($data);

        //加密
        $k = openssl_pkey_get_private('file://'.storage_path('app/keys/private.pem'));   // file://
        openssl_private_encrypt($json_str,$enc_data,$k);


        $send_data = base64_encode($enc_data);
        $api_url = 'http://api.1809a.com/test/rsa';

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$api_url);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$send_data);     // raw 格式
        curl_setopt($ch,CURLOPT_HTTPHEADER,[
            'Content-Type:text/plain'
        ]);

        $response = curl_exec($ch);
        //监控错误码
        $err_code = curl_errno($ch);

        if($err_code>0){
            echo "CURL 错误码:".$err_code;
            exit;
        }
        curl_close($ch);
    }

    /**
     * 非对称加密签名
     *
     */
    public function testSign()
    {
        $data = [
            'oid'       => 123456,
            'amount'    => 2000,
            'title'     => '测试订单',
            'username'  => 'zhangsan'
        ];


        $json_str = json_encode($data);     //要发送的数据
        $k = openssl_get_privatekey('file://'.storage_path('app/keys/private.pem'));
        //计算签名  使用私钥对数据签名
        openssl_sign($json_str,$signature,$k);
       // echo 'signature: '.$signature;echo '</br>';
        $b64 = base64_encode($signature);

        $api_url = 'http://api.1809a.com/test/sign?sign='.urlencode($b64);
        echo 'URL: '. $api_url;echo '<hr>';

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$api_url);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$json_str);     // raw 格式
        curl_setopt($ch,CURLOPT_HTTPHEADER,[
            'Content-Type:text/plain'
        ]);

        $response = curl_exec($ch);
        //监控错误码
        $err_code = curl_errno($ch);

        if($err_code>0){
            echo "CURL 错误码:".$err_code;
            exit;
        }
        curl_close($ch);
    }

    public function testCdn()
    {
        return view('test.cdn1');
    }

}
