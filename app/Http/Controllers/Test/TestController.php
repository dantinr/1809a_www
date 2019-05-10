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
}
