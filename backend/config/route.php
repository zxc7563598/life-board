<?php

/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Webman\Route;
use app\controller;
use Webman\RedisQueue\Client;

Route::any('/test', function () {
    $data = json_decode('{"user_id":1,"path":"\/www\/wwwroot\/life-board\/backend\/public\/bill\/wechat\/20250703145005\/\u5fae\u4fe1\u652f\u4ed8\u8d26\u5355(20240701-20241001)\u2014\u2014\u3010\u89e3\u538b\u5bc6\u7801\u53ef\u5728\u5fae\u4fe1\u652f\u4ed8\u516c\u4f17\u53f7\u67e5\u770b\u3011.zip"}', true);
    print_r($data);
    Client::send('wechat-bill-parser', $data);
});

Route::group('/api', function () {
    Route::post('/auth/register', [controller\AuthController::class, 'register'])->name('[执行注册]');
    Route::post('/auth/login', [controller\AuthController::class, 'login'])->name('[执行登录]');
    Route::post('/auth/refresh', [controller\AuthController::class, 'refreshAll'])->name('[刷新token]');
    Route::post('/auth/profile', [controller\AuthController::class, 'getProfile'])->name('[获取用户个人信息]');
    Route::post('/auth/set-profile', [controller\AuthController::class, 'setProfile'])->name('[变更用户个人信息]');
    Route::post('/auth/set-billmail-config', [controller\AuthController::class, 'setBillmailConfig'])->name('[变更账单监听邮箱配置]');
    Route::post('/auth/logout', [controller\AuthController::class, 'logout'])->name('[退出登录]');
})->middleware([
    app\middleware\AccessMiddleware::class,
    app\middleware\LangMiddleware::class,
    app\middleware\AuthMiddleware::class
]);

// 允许所有的options请求
Route::options('[{path:.+}]', function () {
    return response('', 204)
        ->withHeaders([
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With',
        ]);
});

Route::fallback(function () {
    // 最后兜底
    return json([
        'code' => 0,
        'message' => '别看了哥们，没这个页面',
        'data' => (object)[]
    ]);
});

Route::disableDefaultRoute(); // 关闭默认路由