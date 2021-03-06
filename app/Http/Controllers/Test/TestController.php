<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Model\UserModel;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use App\Jobs\ShowDate;
use App\Model\TestModel;

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

        $u = UserModel::limit(5)->get();
        echo '<pre>';print_r($u);echo '</pre>';


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

    public function insert100k()
    {
        for($i=0;$i<100000;$i++){
            $length1 = mt_rand(5,10);
            $length2 = mt_rand(5,10);
            $e_num = mt_rand(0,3);
            $email = [
                '@qq.com',
                '@163.com',
                '@gmail.com',
                '@sohu.com'
            ];


            $u = [
                'name'  => Str::random($length1),
                'email' => Str::random($length2) . '@' . $email[$e_num],
                'age'   => mt_rand(10,100),
               // 'add_time'=> time()
            ];

            $uid = UserModel::insertGetId($u);
            echo 'UID: '.$uid;echo '</br>';
        }
    }


    //分表测试
    public function cut1()
    {
        $uid = Redis::incr('incr:generate_uid');
        echo 'uid: '.$uid;echo '</br>';
        $table_id = $uid % 5;

        $data = [
            'uid'           => $uid,
            'user_name'     => Str::random(8),
            'email'         => Str::random(10).'@qq.com',
            'add_time'      => time(),
        ];

        $table = 'p_user_'.$table_id;
        echo $table;

        $rs = DB::table($table)->insertGetId($data);echo '</br>';

        var_dump($rs);
    }

    public function cut2()
    {
        $timestamp = mt_rand(946656000,1546272000);
        $timestamp2 = mt_rand(946656000,1546272000);
        $date = date('Y-m-d',$timestamp);
        $date2 = date('Y-m-d',$timestamp2);
        $data = [
            'id'    => mt_rand(1,9999999),
            'fname' => Str::random(5),
            'lname' => Str::random(8),
            'hired' => $date,
            'separated' => $date2,
            'job_code'  => mt_rand(1,100000),
            //'store_id'  => mt_rand(1,20)
            'store_id'  => 20
        ];

        $id = DB::table('employees')->insertGetId($data);
        var_dump($id);
    }

    public function mysql1()
    {
        for($i=0;$i<100;$i++)
        {
            echo 'i='.$i;echo '</br>';
            $data = [
                'age'   => mt_rand(1,100),
                'name'  => Str::random(10)
            ];
            DB::table('p_users')->insert($data);
        }

    }

    /**
     * 用户注册
     */
    public function reg()
    {
        echo __METHOD__;echo '</br>';

        ShowDate::dispatch()->onQueue('send_email');
    }


    public function cdn()
    {
        return view('test.cdn');
    }

    public function addRecord()
    {
        for($i=1590;$i<10000;$i++)
        {
            $data = [
                'id'    => $i,
                'fname' => Str::random(8),
                'lname' => Str::random(10),
                'hired' => date('Y-m-d',mt_rand(strtotime('2000-01-01'),strtotime('2019-10-10'))),
                'separated' => date('Y-m-d',mt_rand(strtotime('2000-01-01'),strtotime('2019-10-10'))),
                'job_code'  => mt_rand(1,1000),
                'store_id'  => mt_rand(1,20),
            ];

            TestModel::insert($data);
            echo $i;echo '</br>';

        }
    }


    public function reg1()
    {


        $u_pass = Str::random(8);
        $pass = password_hash($u_pass,PASSWORD_BCRYPT);

        $redis_userid_key = 'str:count:userid';
        $uid = Redis::incr($redis_userid_key);

        echo 'uid: '.$uid;echo '</br>';
        $user_info = [
            'uid'   => $uid,
            'name'  => Str::random(8),
            'pass'  => $pass,
            'email' => Str::random(8).'@qq.com',
            'reg_time'  => time()
        ];
        echo '<pre>';print_r($user_info);echo '</pre>';

        $user_table = 'users_'. $uid%5;          //计算分表
        echo $user_table;echo '<hr>';

        $id = DB::table($user_table)->insertGetId($user_info);
        var_dump($id);


    }

}
