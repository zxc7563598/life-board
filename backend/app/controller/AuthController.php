<?php

namespace app\controller;

use app\model\RefreshTokens;
use app\model\User;
use app\service\AuthService;
use support\Request;
use app\service\TokenService;
use resource\enums\UserEnums;
use resource\enums\RefreshTokensEnums;
use support\Response;

class AuthController
{

    /**
     * 执行注册
     * 
     * @param string $nickname 昵称
     * @param string $username 账号
     * @param string $password 密码
     * 
     * @return Response 
     */
    public function register(Request $request)
    {
        // 获取参数
        $nickname = $request->data['nickname'];
        $username = $request->data['username'];
        $password = $request->data['password'];
        // 获取数据
        $user = User::where('username', $username)->first();
        if (!empty($user)) {
            return fail($request, 800009);
        }
        // 进行注册
        $user = new User();
        $user->nickname = $nickname;
        $user->username = $username;
        $user->password = $password;
        $user->status = UserEnums\Status::Enable->value;
        $user->save();
        // 返回数据
        return success($request, []);
    }

    /**
     * 执行登录
     * 
     * @param string $username 账号
     * @param string $password 密码
     * @param string $browser_name 浏览器名称
     * @param string $browser_version 浏览器版本
     * @param string $engine_name 引擎名称
     * @param string $os_name 操作系统名称
     * @param string $os_version 操作系统版本
     * @param string $platform_type 获取平台类型
     * @param string $ua 提取ua
     * 
     * @param Request $request 
     */
    public function login(Request $request)
    {
        // 获取参数
        $username = $request->data['username'];
        $password = $request->data['password'];
        $ip = $request->header('ali-cdn-real-ip') ?? $request->getRealIp();
        $browser_name = $request->data['browser_name'] ?? null;
        $browser_version = $request->data['browser_version'] ?? null;
        $engine_name = $request->data['engine_name'] ?? null;
        $os_name = $request->data['os_name'] ?? null;
        $os_version = $request->data['os_version'] ?? null;
        $platform_type = $request->data['platform_type'] ?? null;
        $ua = $request->data['ua'] ?? null;
        // 获取数据
        $user = User::where('username', $username)->first();
        if (empty($user)) {
            return fail($request, 800003);
        }
        if ($user->status == UserEnums\Status::Disable->value) {
            return fail($request, 800004);
        }
        if ($user->password != sha1(sha1($password) . $user->salt)) {
            return fail($request, 800005);
        }
        $issueTokens = AuthService::issueTokens($user->id, $ip, $browser_name, $browser_version, $engine_name, $os_name, $os_version, $platform_type, $ua);
        // 返回数据
        return success($request, [
            'access_token' => $issueTokens['access_token'],
            'refresh_token' => $issueTokens['refresh_token']
        ]);
    }

    /**
     * 刷新token
     * 
     * @param string $refresh_token refresh_token
     * @param string $browser_name 浏览器名称
     * @param string $browser_version 浏览器版本
     * @param string $engine_name 引擎名称
     * @param string $os_name 操作系统名称
     * @param string $os_version 操作系统版本
     * @param string $platform_type 获取平台类型
     * @param string $ua 提取ua
     * 
     * @return Response 
     */
    public function refreshAll(Request $request)
    {
        // 获取参数
        $refresh_token = $request->data['refresh_token'];
        $ip = $request->header('ali-cdn-real-ip') ?? $request->getRealIp();
        $browser_name = $request->data['browser_name'] ?? null;
        $browser_version = $request->data['browser_version'] ?? null;
        $engine_name = $request->data['engine_name'] ?? null;
        $os_name = $request->data['os_name'] ?? null;
        $os_version = $request->data['os_version'] ?? null;
        $platform_type = $request->data['platform_type'] ?? null;
        $ua = $request->data['ua'] ?? null;
        // 解析refresh_token
        $payload = TokenService::parseToken($refresh_token);
        if (!$payload) {
            return fail($request, 800006);
        }
        // 撤销token
        $refresh_tokens = RefreshTokens::where('token', $refresh_token)->where('user_id', $payload['uid'])->first();
        if (empty($refresh_tokens)) {
            return fail($request, 800007);
        }
        if ($refresh_tokens->revoked == RefreshTokensEnums\Revoked::Yes->value) {
            return fail($request, 800008);
        }
        $refresh_tokens->revoked = RefreshTokensEnums\Revoked::Yes->value;
        $refresh_tokens->save();
        // 执行登录
        $issueTokens = AuthService::issueTokens($payload['uid'], $ip, $browser_name, $browser_version, $engine_name, $os_name, $os_version, $platform_type, $ua);
        // 返回数据
        return success($request, [
            'access_token' => $issueTokens['access_token'],
            'refresh_token' => $issueTokens['refresh_token']
        ]);
    }

    /**
     * 退出登录
     * 
     * @param string $refresh_token refresh_token
     * 
     * @return Response 
     */
    public function logout(Request $request)
    {
        // 获取参数
        $refresh_token = $request->data['refresh_token'];
        // 验证refresh_token
        $payload = TokenService::parseToken($refresh_token);
        if (!$payload) {
            return fail($request, 800006);
        }
        AuthService::revokeTokens($request->access_token, $refresh_token, $payload['uid']);
        // 返回数据
        return success($request, []);
    }
}
