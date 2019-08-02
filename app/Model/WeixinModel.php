<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;

class WeixinModel extends Model
{
    //

    public static function getAccessToken()
    {

        //先获取缓存，如果不存在则请求接口
        $redis_key = 'wx_access_token';
        $token = Redis::get($redis_key);
        if($token){
            //echo 'Cache: ';echo '</br>';
        }else{
            //echo 'No Cache: ';echo '</br>';
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env("WX_APPID").'&secret='.env("WX_APPSECRET");
            //echo $url;die;
            $json_str = file_get_contents($url);
            $arr = json_decode($json_str,true);
            echo '<pre>';print_r($arr);echo '</pre>';

            Redis::set($redis_key,$arr['access_token']);
            Redis::expire($redis_key,3600);         //设置过期时间
            $token = $arr['access_token'];
        }

        return $token;

    }
}
