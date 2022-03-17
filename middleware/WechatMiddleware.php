<?php namespace Beysong\Wechat\Middleware;

use Closure;
use Illuminate\Http\Response;
use Beysong\Wechat\Classes\WechatManager;

class WechatMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!WechatManager::checkWechat()) {
            return response()->json('please open in wechat');
        }
        return $next($request);
    }
}