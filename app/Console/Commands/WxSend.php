<?php

namespace App\Console\Commands;

use App\Model\WeixinUserModel;
use Illuminate\Console\Command;
use App\Model\WeixinModel;
use GuzzleHttp\Client;

class WxSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wx:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '群发微信消息';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $user_list = WeixinUserModel::where(['sub_status'=>1])->get()->toArray();
        $openid_arr = array_column($user_list,'openid');
        $msg = date('Y-m-d H:i:s');
        $response = $this->sendMsg($openid_arr,$msg);
        echo $response;
    }


    /**
     * 根据openid消息群发
     */
    public function sendMsg($openid_arr,$content)
    {

        $msg = [
            "touser"    => $openid_arr,
            "msgtype"   => "text",
            "text"  => [
                "content"   => $content
            ]
        ];

        $data = json_encode($msg,JSON_UNESCAPED_UNICODE);

        $token = WeixinModel::getAccessToken();
        //openid群发接口
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.$token;

        $client = new Client();
        $response = $client->request('post',$url,[
            'body'  => $data
        ]);

        return $response->getBody();
    }

}
