<?php
use Carbon\Carbon;
use Carbon\Exceptions\InvalidTimeZoneException;
use support\Response;
use Hejunjie\Utils;
use Hejunjie\ErrorLog\Logger;
use Hejunjie\ErrorLog\Handlers;

/**
 * Api响应成功
 *
 * @param object $request Webman\Http\Request对象
 * @param array|object $data 返回数据
 * 
 * @return Response
 */
function success($request, $data = [], $message = ''): Response
{
    $request->res = [
        'code' => 0,
        'message' => !empty($message) ? $message : (trans(config('code')[0]) ?? 'error'),
        'data' => empty($data) ? [] : $data
    ];
    return json($request->res, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES + JSON_PRESERVE_ZERO_FRACTION);
}

/**
 * Api响应失败
 *
 * @param object $request Webman\Http\Request对象
 * @param array $data 返回数据
 * 
 * @return Response
 */
function fail($request, $code = 500, $data = [], $message = ''): Response
{
    // 记录错误信息
    $request->res = [
        'code' => $code,
        'message' => !empty($message) ? $message : (trans(config('code')[$code]) ?? 'error'),
        'data' => empty($data) ? [] : $data
    ];
    return json($request->res, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES + JSON_PRESERVE_ZERO_FRACTION);
}