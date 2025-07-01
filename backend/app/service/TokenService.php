<?php

namespace app\service;

/**
 * Class TokenService
 * 简易 JWT 实现，用于生成和验证 access_token / refresh_token
 */
class TokenService
{

    /**
     * 生成 JWT 字符串
     *
     * @param array $payload 业务负载，自动加入 exp 字段
     * @param int $exp 到期时间戳（单位：秒）
     * 
     * @return string 完整的 JWT 字符串
     */
    public static function generateToken(array $payload, int $exp): string
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $payload['exp'] = $exp;

        $base64UrlHeader = self::base64UrlEncode(json_encode($header));
        $base64UrlPayload = self::base64UrlEncode(json_encode($payload));
        $signature = self::sign("$base64UrlHeader.$base64UrlPayload");

        return "$base64UrlHeader.$base64UrlPayload.$signature";
    }

    /**
     * 解析并验证 JWT 字符串
     *
     * @param string|null $token JWT 字符串
     * 
     * @return array|null 解码后的 payload，失败或过期返回 null
     */
    public static function parseToken(?string $token): ?array
    {
        if (empty($token)) {
            return null;
        }
        [$h, $p, $s] = explode('.', $token);
        if (self::sign("$h.$p") !== $s) return null;

        $payload = json_decode(self::base64UrlDecode($p), true);
        return (time() <= $payload['exp']) ? $payload : null;
    }

    /**
     * 生成 access_token（短期令牌）
     *
     * @param int $uid 用户id
     * @param int $exp 到期时间戳（单位：秒）
     * 
     * @return string
     */
    public static function generateAccessToken(int $uid, int $exp): string
    {
        return self::generateToken(['uid' => $uid], $exp);
    }

    /**
     * 生成 refresh_token（长期令牌）
     *
     * @param int $uid 用户id
     * @param int $exp 到期时间戳（单位：秒）
     * 
     * @return string
     */
    public static function generateRefreshToken(int $uid, int $exp): string
    {
        return self::generateToken(['uid' => $uid], $exp);
    }

    /**
     * 生成签名
     *
     * @param string $data 待签名数据（header.payload）
     * 
     * @return string base64url 编码后的签名
     */
    protected static function sign(string $data): string
    {
        return self::base64UrlEncode(hash_hmac('sha256', $data, config('app.key'), true));
    }
    /**
     * base64url 编码（去掉 `=`、替换 `/+`）
     *
     * @param string $data 待编码数据
     * 
     * @return string
     */
    protected static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * base64url 解码
     *
     * @param string $data 待解码数据
     * 
     * @return string
     */
    protected static function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
