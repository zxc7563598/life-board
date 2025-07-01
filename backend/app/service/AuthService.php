<?php

namespace app\service;

use app\model\RefreshTokens;
use Carbon\Carbon;
use Hejunjie\Ip138\IP138;
use resource\enums\RefreshTokensEnums;
use support\Redis;

/**
 * Class AuthService
 * 登录操作封装，用于账号的登录/退出登录
 */
class AuthService
{
    public static function issueTokens($user_id, $ip, $browser_name = null, $browser_version = null, $engine_name = null, $os_name = null, $os_version = null, $platform_type = null, $ua = null): array
    {
        // 获取token
        $timestamp = Carbon::now()->timezone(config('app.default_timezone'))->timestamp;
        $expires_at = $timestamp + 604800;
        $access = TokenService::generateAccessToken($user_id, ($timestamp + 3600));
        $refresh = TokenService::generateRefreshToken($user_id, $expires_at);
        // 获取IP请求地址
        $ip_address = null;
        if (!empty(config('account.ip138.ipdata_token'))) {
            $ipLookup = (new IP138(IP138::URL_DOMESTIC_HTTPS))->ipLookup($ip, 'jsonp', '', config('account.ip138.ipdata_token'));
            if (isset($ipLookup['data']) && count($ipLookup['data'])) {
                $ip_address = implode(' ', $ipLookup['data']);
            }
        }
        // 记录 refresh_token
        $refresh_token = new RefreshTokens();
        $refresh_token->user_id = $user_id;
        $refresh_token->token = $refresh;
        $refresh_token->expires_at = $expires_at;
        $refresh_token->ip = $ip;
        $refresh_token->ip_address = $ip_address;
        $refresh_token->browser_name = $browser_name;
        $refresh_token->browser_version = $browser_version;
        $refresh_token->engine_name = $engine_name;
        $refresh_token->os_name = $os_name;
        $refresh_token->os_version = $os_version;
        $refresh_token->platform_type = $platform_type;
        $refresh_token->ua = $ua;
        $refresh_token->save();
        // 返回数据
        return [
            'access_token' => $access,
            'refresh_token' => $refresh
        ];
    }

    public static function revokeTokens($access_token, $refresh_token, $uid): void
    {
        // 注销refresh_token
        $refresh_tokens = RefreshTokens::where('token', $refresh_token)->where('user_id', $uid)->first();
        if (!empty($refresh_tokens)) {
            $refresh_tokens->revoked = RefreshTokensEnums\Revoked::Yes->value;
            $refresh_tokens->save();
        }
        // 将access_token加入黑名单
        if (!empty($access_token)) {
            $tokenKey = 'blacklist:' . $access_token;
            Redis::set($tokenKey, 1);
            Redis::expire($tokenKey, 3600);
        }
    }
}
