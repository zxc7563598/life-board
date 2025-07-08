<?php

namespace app\controller;

use support\Request;
use support\Response;
use app\model\DashboardWidgets;
use app\model\UserDashboardWidgets;
use resource\enums\DashboardWidgetsEnums;

class HomeController
{
    /**
     * 获取用户配置组件信息
     * 
     * @return Response 
     */
    public function getUserWidgets(Request $request): Response
    {
        // 获取数据
        $user_dashboard_widgets = UserDashboardWidgets::where('user_id', $request->uid)->orderBy('order_index', 'asc')->get([
            'widget_id' => 'widget_id',
            'order_index' => 'order_index',
            'is_active' => 'is_active'
        ]);
        $dashboard_widgets = DashboardWidgets::where('is_active', DashboardWidgetsEnums\IsActive::Enable->value)->get([
            'id' => 'id',
            'name' => 'name',
            'component_key' => 'component_key',
            'description' => 'description'
        ]);
        $widgets = [];
        $user_widgets = [];
        foreach ($dashboard_widgets as $_dashboard_widgets) {
            $widgets[$_dashboard_widgets->id] = $_dashboard_widgets->component_key;
        }
        foreach ($user_dashboard_widgets as $_user_dashboard_widgets) {
            if (isset($widgets[$_user_dashboard_widgets->widget_id])) {
                $user_widgets[] = $widgets[$_user_dashboard_widgets->widget_id];
            }
        }
        // 返回数据
        return success($request, [
            'dashboard_widgets' => $dashboard_widgets,
            'user_widgets' => $user_widgets
        ]);
    }

    /**
     * 变更用户配置组件
     * 
     * @param array $widgets 组件信息
     * 
     * @return Response 
     */
    public function saveUserWidgets(Request $request): Response
    {
        // 获取参数
        $widgets = $request->data['widgets'];
        // 删除用户先前配置
        UserDashboardWidgets::where('user_id', $request->uid)->delete();
        // 为用户添加新的配置
        foreach ($widgets as $_widgets) {
            $user_dashboard_widgets = new UserDashboardWidgets();
            $user_dashboard_widgets->user_id = $request->uid;
            $user_dashboard_widgets->widget_id = $_widgets['widget_id'];
            $user_dashboard_widgets->order_index = $_widgets['order_index'];
            $user_dashboard_widgets->is_active = $_widgets['is_active'];
            $user_dashboard_widgets->save();
        }
        // 返回数据
        return success($request, []);
    }
}
