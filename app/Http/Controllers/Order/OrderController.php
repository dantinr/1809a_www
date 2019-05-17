<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Str;
use App\Model\OrderModel;

class OrderController extends Controller
{


    /**
     * 订单详情
     */
    public function info()
    {
        $oid = intval($_GET['oid']);
        $info = OrderModel::where(['oid'=>$oid])->first();
        echo '<pre>';print_r($info);echo '</pre>';
    }

    /**
     * 生成新订单
     */
    public function newOrder()
    {
        echo date('Y-m-d H:i:s');echo '</br>';
        $order_sn = '1809a_' .date('ymdhi') .'_'. mt_rand(1111,9999) .'_'. strtolower(Str::random(8));
        echo '<hr>';

        $data = [
            'order_sn'  => $order_sn,
            'uid'       => 0,
            'order_amount'  => mt_rand(1,10),
            'add_time'  => time(),
        ];

        $oid = OrderModel::insertGetId($data);
        echo 'oid: '.$oid;
        echo '<hr>';
        echo '<pre>';print_r($data);echo '</pre>';
    }
}
