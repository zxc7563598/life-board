<?php

namespace app\controller;

use app\model\RefreshTokens;
use app\model\User;
use app\service\AuthService;
use support\Request;
use app\service\TokenService;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\InvalidCastException;
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
    public function register(Request $request): Response
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
    public function login(Request $request): Response
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
    public function refreshAll(Request $request): Response
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
     * 获取用户个人信息
     * 
     * @return Response 
     */
    public function getProfile(Request $request): Response
    {
        // 获取数据
        $user = User::where('id', $request->uid)->first([
            'nickname' => 'nickname',
            'username' => 'username',
            'mail_host' => 'mail_host',
            'mail_username' => 'mail_username',
            'mail_password' => 'mail_password',
        ]);
        if (empty($user)) {
            return fail($request, 800010);
        }
        // 获取邮件服务提供商(IMAP 邮件服务器地址一般是稳定且长期不变的，因此直接硬编码即可) {imap.gmail.com:993/imap/ssl}INBOX
        $mail_providers = [
            ['label' => 'Gmail', 'value' => 'imap.gmail.com:993/imap/ssl'],
            ['label' => 'Yahoo', 'value' => 'imap.mail.yahoo.com:993/imap/ssl'],
            ['label' => 'iCloud', 'value' => 'imap.mail.me.com:993/imap/ssl'],
            ['label' => 'Outlook', 'value' => 'imap-mail.outlook.com:993/imap/ssl'],
            ['label' => 'QQ', 'value' => 'imap.qq.com:993/imap/ssl'],
            ['label' => '163', 'value' => 'imap.163.com:993/imap/ssl'],
            ['label' => '新浪', 'value' => 'imap.sina.com.cn:993/imap/ssl'],
            ['label' => '阿里云', 'value' => 'imap.aliyun.com:993/imap/ssl'],
            ['label' => 'Zoho', 'value' => 'imap.zoho.com:993/imap/ssl'],
            ['label' => 'Yandex', 'value' => 'imap.yandex.com:993/imap/ssl'],
            ['label' => 'Fastmail', 'value' => 'imap.fastmail.com:993/imap/ssl'],
            ['label' => '其他', 'value' => '']
        ];
        // 返回数据
        return success($request, [
            'profile_card' => [
                'nickname' => $user->nickname,
                'username' => $user->username,
            ],
            'billmail_config' => [
                'mail_providers' => $mail_providers,
                'mail_host' => $user->mail_host,
                'mail_username' => $user->mail_username,
                'mail_password' => $user->mail_password,
            ]
        ]);
    }

    /**
     * 变更用户个人信息
     * 
     * @param string $nickname 昵称
     * @param string $username 账号
     * @param string $password 密码
     * 
     * @return Response 
     */
    public function setProfile(Request $request): Response
    {
        // 获取参数
        $nickname = $request->data['nickname'];
        $username = $request->data['username'];
        $password = $request->data['password'] ?? null;
        // 获取数据
        $user = User::where('id', $request->uid)->first();
        if (empty($user)) {
            return fail($request, 800010);
        }
        $login = false;
        if (!empty($password) || ($user->username != $username)) {
            $login = true;
        }
        // 存储信息
        $user->nickname = $nickname;
        $user->username = $username;
        if (!empty($password)) {
            $user->password = $password;
        }
        $user->save();
        // 返回数据
        return success($request, [
            'login' => $login
        ]);
    }

    /**
     * 变更账单监听邮箱配置
     * 
     * @param string $mail_host 服务地址
     * @param string $mail_username 邮箱账号
     * @param string $mail_password 密码或授权码
     * 
     * @return Response 
     */
    public function setBillmailConfig(Request $request): Response
    {
        // 获取参数
        $mail_host = $request->data['mail_host'];
        $mail_username = $request->data['mail_username'];
        $mail_password = $request->data['mail_password'];
        // 获取数据
        $user = User::where('id', $request->uid)->first();
        if (empty($user)) {
            return fail($request, 800010);
        }
        // 测试连接
        $imap = imap_open("{{$mail_host}}INBOX", $mail_username, $mail_password, OP_READONLY);
        if ($imap === false) {
            return fail($request, 800011);
        }
        imap_close($imap);
        // 存储信息
        $user->mail_host = $mail_host;
        $user->mail_username = $mail_username;
        $user->mail_password = $mail_password;
        $user->save();
        // 返回数据
        return success($request, []);
    }

    /**
     * 退出登录
     * 
     * @param string $refresh_token refresh_token
     * 
     * @return Response 
     */
    public function logout(Request $request): Response
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
