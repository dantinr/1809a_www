<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class Request10times
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $key = 'request10times:ip:'.$_SERVER['REMOTE_ADDR'].':token:'.$request->input('token');
        $num = Redis::get($key);
        if($num>10){        //超过限制
            $response = [
                'errno' => 50020,
                'msg'   => '请求受限'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        Redis::incr($key);
        Redis::expire($key,5);
        return $next($request);
    }
}
