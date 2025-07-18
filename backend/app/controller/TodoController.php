<?php

namespace app\controller;

use support\Request;
use support\Response;
use app\model\TodoCategories;
use app\model\Todos;
use Carbon\Carbon;
use resource\enums\TodosEnums;

class TodoController
{
    /**
     * 获取清单分类
     * 
     * @return Response 
     */
    public function getTodoCategories(Request $request): Response
    {
        // 获取数据
        $todo_categories = TodoCategories::where('user_id', $request->uid)->get([
            'id' => 'id',
            'name' => 'name',
        ]);
        // 返回数据
        return success($request, [
            'todo_categories' => $todo_categories
        ]);
    }

    /**
     * 变更清单分类
     * 
     * @param integer $category_id 清单分类ID
     * @param string $name 清单名称
     * 
     * @return Response 
     */
    public function updateTodoCategory(Request $request): Response
    {
        // 获取参数
        $category_id = $request->data['category_id'] ?? null;
        $name = $request->data['name'];
        // 获取数据
        $todo_categories = new TodoCategories();
        if (!is_null($category_id)) {
            $todo_categories = TodoCategories::where('user_id', $request->uid)->where('id', $category_id)->first();
            if (empty($todo_categories)) {
                return fail($request, 800012);
            }
        }
        $todo_categories->user_id = $request->uid;
        $todo_categories->name = $name;
        $todo_categories->save();
        // 返回数据
        return success($request, []);
    }

    /**
     * 删除清单分类
     * 
     * @param integer $category_id 清单分类ID
     * 
     * @return Response 
     */
    public function deleteTodoCategory(Request $request): Response
    {
        // 获取参数
        $category_id = $request->data['category_id'];
        // 进行删除
        $todo_categories = TodoCategories::where('user_id', $request->uid)->where('id', $category_id)->first();
        if (empty($todo_categories)) {
            return fail($request, 800012);
        }
        // 去除待办事件的分类
        Todos::where('user_id', $request->uid)->where('category_id', $category_id)->update([
            'category_id' => null
        ]);
        $todo_categories->delete();
        // 返回数据
        return success($request, []);
    }

    /**
     * 获取待办事项列表
     * 
     * @param integer $category_id 清单分类ID
     * @param integer $end_at 截止时间（毫秒级时间戳）
     * 
     * @return Response 
     */
    public function listTodos(Request $request): Response
    {
        $carbon = new Carbon();
        // 获取参数
        $category_id = $request->data['category_id'] ?? null;
        $end_at = !empty($request->data['end_at']) ? $carbon->parse($request->data['end_at'] / 1000)->timezone(config('app.default_timezone'))->timestamp : $carbon->parse('3000-01-01 00:00:00')->timezone(config('app.default_timezone'))->timestamp;
        // 获取数据
        $todos = Todos::where('user_id', $request->uid)->where('start_at', '<=', $end_at);
        if (!is_null($category_id)) {
            $todos = $todos->where('category_id', $category_id);
        }
        // 非重复事件
        $data = [];
        $todos = $todos->orderBy('id', 'desc')->get([
            'id' => 'id',
            'title' => 'title',
            'color' => 'color',
            'start_at' => 'start_at',
            'end_at' => 'end_at',
            'completed' => 'completed',
            'repeat_type' => 'repeat_type',
            'repeat_interval' => 'repeat_interval',
            'repeat_until' => 'repeat_until'
        ]);
        foreach ($todos as $_todos) {
            $date = $carbon->parse($_todos->start_at)->timezone(config('app.default_timezone'))->format('Y年m月d日');
            if (($_todos->end_at - $_todos->start_at) > 86400) {
                $date = $carbon->parse($_todos->start_at)->timezone(config('app.default_timezone'))->format('Y年m月d日') . ' - ' . $carbon->parse($_todos->end_at)->timezone(config('app.default_timezone'))->format('Y年m月d日');
            }
            $data[] = [
                'id' => $_todos->id,
                'date' => $date,
                'title' => $_todos->title,
                'color' => $_todos->color,
                'completed' => $_todos->completed == TodosEnums\Completed::Yes->value,
                'repeat_type' => $_todos->repeat_type,
                'repeat_interval' => $_todos->repeat_interval,
                'repeat_until' => $_todos->repeat_until,
            ];
        }
        // 返回数据
        return success($request, [
            'data' => $data,
            'color' => TodosEnums\Color::all()
        ]);
    }

    /**
     * 获取单条待办事项
     * 
     * @param integer $todo_id 待办清单ID
     * 
     * @return Response 
     */
    public function getTodo(Request $request): Response
    {
        // 获取参数
        $todo_id = $request->data['todo_id'] ?? null;
        // 获取数据
        $todos = null;
        if (!is_null($todo_id)) {
            $todos = Todos::where('id', $todo_id)->where('user_id', $request->uid)->first([
                'category_id' => 'category_id',
                'title' => 'title',
                'content' => 'content',
                'color' => 'color',
                'start_at' => 'start_at',
                'end_at' => 'end_at',
                'repeat_type' => 'repeat_type',
                'repeat_interval' => 'repeat_interval',
                'repeat_until' => 'repeat_until'
            ]);
        }
        // 获取用户分类
        $todo_categories = TodoCategories::where('user_id', $request->uid)->get([
            'key' => 'id as key',
            'value' => 'name as value',
        ]);
        // 返回数据
        return success($request, [
            'repeat_type' => TodosEnums\RepeatType::all(),
            'color' => TodosEnums\Color::all(),
            'data' => $todos,
            'categories' => $todo_categories
        ]);
    }

    /**
     * 存储待办事项
     * 
     * @param integer $todo_id 待办清单ID
     * @param integer $category_id 清单分类ID
     * @param string $title 清单标题
     * @param string $content 清单内容
     * @param integer $color 清单颜色
     * @param integer $start_at 开始时间（毫秒级时间戳）
     * @param integer $end_at 结束时间（毫秒级时间戳）
     * @param integer $repeat_type 重复类型
     * @param integer $repeat_interval 重复间隔
     * @param integer $repeat_until 重复截止时间（毫秒级时间戳）
     * 
     * @return Response 
     */
    public function saveTodo(Request $request): Response
    {
        // 获取参数
        $todo_id = $request->data['todo_id'] ?? null;
        $category_id = $request->data['category_id'] ?? null;
        $title = $request->data['title'];
        $content = $request->data['content'] ?? '';
        $color = $request->data['color'] ?? mt_rand(1, 20);
        $start_at = !empty($request->data['start_at']) ? Carbon::parse($request->data['start_at'] / 1000)->timezone(config('app.default_timezone'))->setTime(0, 0)->timestamp : Carbon::now()->timezone(config('app.default_timezone'))->setTime(0, 0)->timestamp;
        $end_at = !empty($request->data['end_at']) ? Carbon::parse($request->data['end_at'] / 1000)->timezone(config('app.default_timezone'))->setTime(23, 59, 59)->timestamp : Carbon::now()->timezone(config('app.default_timezone'))->setTime(23, 59, 59)->timestamp;
        $repeat_type = $request->data['repeat_type'] ?? TodosEnums\RepeatType::None->value;
        $repeat_interval = $request->data['repeat_interval'] ?? null;
        $repeat_until = !empty($request->data['repeat_until']) ? Carbon::parse($request->data['repeat_until'] / 1000)->timezone(config('app.default_timezone'))->setTime(23, 59, 59)->timestamp : null;
        // 获取数据
        $todos = new Todos();
        if (!is_null($todo_id)) {
            $todos = Todos::where('id', $todo_id)->where('user_id', $request->uid)->first();
            if (empty($todos)) {
                return fail($request, 800013);
            }
        }
        // 存储数据
        $todos->user_id = $request->uid;
        $todos->category_id = $category_id;
        $todos->title = $title;
        $todos->content = $content;
        $todos->color = $color;
        $todos->start_at = $start_at;
        $todos->end_at = $end_at;
        $todos->repeat_type = $repeat_type;
        $todos->repeat_interval = $repeat_interval;
        $todos->repeat_until = $repeat_until;
        $todos->save();
        // 返回数据
        return success($request, []);
    }

    /**
     * 删除待办事项
     * 
     * @param integer $todo_id 待办清单ID
     * 
     * @return Response 
     */
    public function deleteTodo(Request $request): Response
    {
        // 获取参数
        $todo_id = $request->data['todo_id'];
        // 删除数据
        Todos::where('id', $todo_id)->where('user_id', $request->uid)->delete();
        // 返回数据
        return success($request, []);
    }

    /**
     * 完成待办事项
     * 
     * @param integer $todo_id 待办清单ID
     * 
     * @return Response 
     */
    public function completeTodo(Request $request): Response
    {
        // 获取参数
        $todo_id = $request->data['todo_id'];
        // 获取数据
        $todos = Todos::where('id', $todo_id)->where('user_id', $request->uid)->first();
        if (empty($todos)) {
            return fail($request, 800013);
        }
        $is_repeat = false;
        switch ($todos->repeat_type) {
            case TodosEnums\RepeatType::Daily->value:
                $is_repeat = true;
                $start_at = Carbon::parse($todos->start_at)->timezone(config('app.default_timezone'))->addDays($todos->repeat_interval)->setTime(0, 0)->timestamp;
                $end_at = Carbon::parse($todos->end_at)->timezone(config('app.default_timezone'))->addDays($todos->repeat_interval)->setTime(23, 59, 59)->timestamp;
                break;
            case TodosEnums\RepeatType::Weekly->value:
                $is_repeat = true;
                $start_at = Carbon::parse($todos->start_at)->timezone(config('app.default_timezone'))->addWeeks($todos->repeat_interval)->setTime(0, 0)->timestamp;
                $end_at = Carbon::parse($todos->end_at)->timezone(config('app.default_timezone'))->addWeeks($todos->repeat_interval)->setTime(23, 59, 59)->timestamp;
                break;
            case TodosEnums\RepeatType::Monthly->value:
                $is_repeat = true;
                $start_at = Carbon::parse($todos->start_at)->timezone(config('app.default_timezone'))->addMonths($todos->repeat_interval)->setTime(0, 0)->timestamp;
                $end_at = Carbon::parse($todos->end_at)->timezone(config('app.default_timezone'))->addMonths($todos->repeat_interval)->setTime(23, 59, 59)->timestamp;
                break;
            case TodosEnums\RepeatType::Yearly->value:
                $is_repeat = true;
                $start_at = Carbon::parse($todos->start_at)->timezone(config('app.default_timezone'))->addYears($todos->repeat_interval)->setTime(0, 0)->timestamp;
                $end_at = Carbon::parse($todos->end_at)->timezone(config('app.default_timezone'))->addYears($todos->repeat_interval)->setTime(23, 59, 59)->timestamp;
                break;
        }
        if ($is_repeat) {
            $new_todos = new Todos();
            $new_todos->user_id = $todos->user_id;
            $new_todos->category_id = $todos->category_id;
            $new_todos->title = $todos->title;
            $new_todos->content = $todos->content;
            $new_todos->color = $todos->color;
            $new_todos->start_at = $todos->start_at;
            $new_todos->end_at = $todos->end_at;
            $new_todos->completed = TodosEnums\Completed::Yes->value;
            $new_todos->repeat_type = TodosEnums\RepeatType::None->value;
            $new_todos->save();
            if (empty($todos->repeat_until) || $todos->repeat_until >= $start_at) {
                $todos->start_at = $start_at;
                $todos->end_at = $end_at;
                $todos->save();
            } else {
                $todos->delete();
            }
        } else {
            $todos->completed = TodosEnums\Completed::Yes->value;
            $todos->save();
        }
        // 返回数据
        return success($request, []);
    }

    /**
     * 取消完成待办事项
     * 
     * @param integer $todo_id 待办清单ID
     * 
     * @return Response 
     */
    public function uncompleteTodo(Request $request): Response
    {
        // 获取参数
        $todo_id = $request->data['todo_id'];
        // 获取数据
        $todos = Todos::where('id', $todo_id)->where('user_id', $request->uid)->first();
        if (empty($todos)) {
            return fail($request, 800013);
        }
        // 取消完成记录
        $todos->completed = TodosEnums\Completed::No->value;
        $todos->save();
        // 返回成功
        return success($request, []);
    }

    // // 获取待办日历信息
    // public function getTodoCalendar(Request $request): Response{}
}
