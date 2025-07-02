<?php

return [
    0 => 'success', // 成功

    800001 => "User session expired", // 账号登录已过期（无access_token）
    800002 => "User session expired", // 账号登录已过期（access_token过期或不存在）
    800003 => "Account does not exist. Please register", // 账号不存在，请注册
    800004 => "Account has been disabled", // 账号已停用
    800005 => "Incorrect username or password", // 账号或密码错误
    800006 => "User session expired", // 账号登录已过期（refresh_token过期或不存在）
    800007 => "User session expired", // 账号登录已过期（refresh_token存在但数据库查不到）
    800008 => "User session expired", // 账号登录已过期（refresh_token存在但已被销毁）
    800009 => "This account is already registered", // 账号已经被注册
    800010 => "User session expired", // 账号登录已过期（refresh_token存在但已被销毁）

    900001 => "Invalid request parameters", // 请求参数异常
    900002 => "Signature verification failed", // 签名验证异常
    900003 => "Expired access token", // 过期的访问
    900004 => "Data parsing error", // 数据解析异常
];
