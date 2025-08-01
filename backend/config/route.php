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

Route::group('/api', function () {
    Route::post('/auth/register', [controller\AuthController::class, 'register'])->name('[执行注册]');
    Route::post('/auth/login', [controller\AuthController::class, 'login'])->name('[执行登录]');
    Route::post('/auth/refresh', [controller\AuthController::class, 'refreshAll'])->name('[刷新token]');
    Route::post('/auth/profile', [controller\AuthController::class, 'getProfile'])->name('[获取用户个人信息]');
    Route::post('/auth/set-profile', [controller\AuthController::class, 'setProfile'])->name('[变更用户个人信息]');
    Route::post('/auth/set-billmail-config', [controller\AuthController::class, 'setBillmailConfig'])->name('[变更账单监听邮箱配置]');
    Route::post('/auth/logout', [controller\AuthController::class, 'logout'])->name('[退出登录]');

    Route::post('/home/get-user-widgets', [controller\HomeController::class, 'getUserWidgets'])->name('[获取用户配置组件信息]');
    Route::post('/home/save-user-widgets', [controller\HomeController::class, 'saveUserWidgets'])->name('[变更用户配置组件]');

    Route::post('/bill/has-user-imap-config', [controller\BillController::class, 'hasUserImapConfig'])->name('[验证用户是否配置了 imap 以及 imap 是否有效]');
    Route::post('/bill/get-bill-search-enums', [controller\BillController::class, 'getBillSearchEnums'])->name('[获取账单搜索枚举信息]');
    Route::post('/bill/get-bill-list', [controller\BillController::class, 'getBillList'])->name('[获取账单列表信息(分页)]');
    Route::post('/bill/get-bill-list-all', [controller\BillController::class, 'getBillListAll'])->name('[获取账单列表信息(不分页)]');
    Route::post('/bill/get-income-categories', [controller\BillController::class, 'getIncomeCategories'])->name('[获取收入分类]');
    Route::post('/bill/get-expense-categories', [controller\BillController::class, 'getExpenseCategories'])->name('[获取支出分类]');
    Route::post('/bill/get-financial-summary', [controller\BillController::class, 'getFinancialSummary'])->name('[获取阶段收支信息]');

    Route::post('/todo/get-todo-categories', [controller\TodoController::class, 'getTodoCategories'])->name('[获取清单分类]');
    Route::post('/todo/update-todo-category', [controller\TodoController::class, 'updateTodoCategory'])->name('[变更清单分类]');
    Route::post('/todo/delete-todo-category', [controller\TodoController::class, 'deleteTodoCategory'])->name('[删除清单分类]');
    Route::post('/todo/list-todos', [controller\TodoController::class, 'listTodos'])->name('[获取待办事项列表]');
    Route::post('/todo/get-todo', [controller\TodoController::class, 'getTodo'])->name('[获取单条待办事项]');
    Route::post('/todo/save-todo', [controller\TodoController::class, 'saveTodo'])->name('[存储待办事项]');
    Route::post('/todo/delete-todo', [controller\TodoController::class, 'deleteTodo'])->name('[删除待办事项]');
    Route::post('/todo/complete-todo', [controller\TodoController::class, 'completeTodo'])->name('[完成待办事项]');
    Route::post('/todo/uncomplete-todo', [controller\TodoController::class, 'uncompleteTodo'])->name('[取消完成待办事项]');
    Route::post('/todo/get-todo-calendar', [controller\TodoController::class, 'getTodoCalendar'])->name('[获取待办日历信息]');
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