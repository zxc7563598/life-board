<?php

namespace app\middleware;

use support\Request;
use app\service\TokenService;
use Carbon\Carbon;
use support\Redis;
use support\Response;

class AuthMiddleware
{
    public function process(Request $request, callable $handler): Response
    {
        // 获取路由数据
        $route = $request->route;
        $path = $route->getPath();
        // 特定路由跳过验证
        // if (in_array($path,[
        //     '/api/xxx/xxx',
        //     ])) {
        //     return $handler($request);
        // }
        // 获取请求参数
        $param = $request->all();
        // 验证签名
        if (!isset($param['timestamp']) || !isset($param['sign'])) {
            return fail($request, 900001);
        }
        // 验证签名
        if (md5(config('app')['sign_key'] . $param['timestamp']) != $param['sign']) {
            return fail($request, 900002);
        }
        // 验证时间是否正确
        $difference = Carbon::now()->timezone(config('app.default_timezone'))->diffInSeconds(Carbon::parse((int)$param['timestamp'])->timezone(config('app.default_timezone')));
        if ($difference > 60) {
            return fail($request, 900003);
        }
        // 解密数据
        $data = openssl_decrypt($param['en_data'], 'aes-128-cbc', config('app')['aes_key'], 0, config('app')['aes_iv']);
        if (!$data) {
            return fail($request, 900004);
        }
        // 完成签名验证,传递数据
        $request->data = json_decode($data, true);
        $request->access_token = null;
        // 登录验证，忽略特定路由
        $whitelisting = [
            '/api/auth/register',
            '/api/auth/login',
            '/api/auth/refresh',
        ];
        if (!in_array($path, $whitelisting)) {
            $auth = $request->header('authorization');
            if (!$auth || !str_starts_with($auth, 'Bearer ')) {
                return fail($request, 800001);
            }
            $request->access_token = substr($auth, 7);
            if (Redis::exists('blacklist:' . $request->access_token)) {
                return fail($request, 800002);
            }
            $payload = TokenService::parseToken($request->access_token);
            if (!$payload) {
                return fail($request, 800002);
            }
            $request->uid = $payload['uid'];
        }
        return $handler($request);
    }
}
