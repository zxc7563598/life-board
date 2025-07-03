<?php

use Carbon\Carbon;
use support\Response;
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

/**
 * 日志信息存储
 *
 * @param string $paths 存储路径
 * @param string $title 存储名称
 * @param string $message 存储内容
 * @param array $context 存储内容
 * 
 * @return void
 */
function sublog($paths, $title, $message, $context): void
{
    $date = Carbon::now()->timezone(config('app.default_timezone'))->format('Y-m-d');
    $handlers = [new Handlers\FileHandler(runtime_path("logs/{$date}/{$paths}"))];
    if (config('app.debug') == 1) {
        $handlers[] = new Handlers\ConsoleHandler();
    }
    $log = new Logger($handlers);
    $log->info($title, $message, $context);
}
