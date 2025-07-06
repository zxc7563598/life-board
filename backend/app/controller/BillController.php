<?php

namespace app\controller;

use app\model\BillRecords;
use support\Request;
use Carbon\Carbon;
use resource\enums\BillRecordsEnums;
use support\Db;
use support\Response;

class BillController
{
    /**
     * 获取账单搜索枚举信息
     * 
     * @return Response 
     */
    public function getBillSearchEnums(Request $request): Response
    {
        // 获取数据
        $trade_type = BillRecords::where('user_id', $request->uid)
            ->groupBy('trade_type')
            ->pluck('trade_type');
        $income_type = BillRecordsEnums\IncomeType::all();
        $platform = BillRecordsEnums\Platform::all();
        // 返回数据
        return success($request, [
            'trade_type' => $trade_type,
            'income_type' => $income_type,
            'platform' => $platform
        ]);
    }

    /**
     * 获取账单列表信息(分页)
     * 
     * @param integer $pageNo 页码
     * @param integer $pageSize 每页展示条数
     * @param string $trade_no 交易单号
     * @param int $platform 交易平台
     * @param int $income_type 收支类型
     * @param array $trade_type 交易类型
     * @param array $trade_time 交易时间
     * 
     * @return Response 
     */
    public function getBillList(Request $request): Response
    {
        // 获取参数
        $pageNo = $request->data['pageNo'] ?? 1;
        $pageSize = $request->data['pageSize'] ?? 20;
        $trade_no = $request->data['trade_no'] ?? null;
        $platform = $request->data['platform'] ?? null;
        $income_type = $request->data['income_type'] ?? null;
        $trade_type = $request->data['trade_type'] ?? null;
        $trade_time = $request->data['trade_time'] ?? null;
        // 获取数据
        $list = BillRecords::where('user_id', $request->uid);
        if (!is_null($trade_no)) {
            $list = $list->where('trade_no', $trade_no);
        }
        if (!is_null($platform)) {
            $list = $list->where('platform', $platform);
        }
        if (!is_null($income_type)) {
            $list = $list->where('income_type', $income_type);
        }
        if (!is_null($trade_type)) {
            $list = $list->whereIn('trade_type', $trade_type);
        }
        if (!is_null($trade_time)) {
            list($start_time, $end_time) = $trade_time;
            $start_time = Carbon::parse($start_time / 1000)->timezone(config('app.default_timezone'))->setTime(0, 0, 0)->timestamp;
            $end_time = Carbon::parse($end_time / 1000)->timezone(config('app.default_timezone'))->setTime(23, 59, 59)->timestamp;
            $list = $list->whereBetween('trade_time', [$start_time, $end_time]);
        }
        $list = $list->orderBy('trade_time', 'desc')->paginate($pageSize, [
            'trade_no' => 'trade_no',
            'platform' => 'platform',
            'trade_type' => 'trade_type',
            'income_type' => 'income_type',
            'product_name' => 'product_name',
            'counterparty' => 'counterparty',
            'payment_method' => 'payment_method',
            'amount' => 'amount',
            'trade_status' => 'trade_status',
            'trade_time' => 'trade_time'
        ], 'page', $pageNo);
        foreach ($list as &$_list) {
            $_list->trade_time = Carbon::parse($_list->trade_time)->timezone(config('app.default_timezone'))->format('Y-m-d H:i:s');
        }
        $data = is_array($list) ? $list : $list->toArray();
        // 返回数据
        return success($request, [
            "total" => $data['total'],
            "pageData" => $data['data']
        ]);
    }

    /**
     * 获取账单列表信息(不分页)
     * 
     * @param int $income_type 收支类型
     * @param string $trade_type 交易类型
     * @param array $trade_time 交易时间
     * 
     * @return Response 
     */
    public function getBillListAll(Request $request): Response
    {
        // 获取参数
        $income_type = $request->data['income_type'] ?? null;
        $trade_type = $request->data['trade_type'] ?? null;
        $trade_time = $request->data['trade_time'] ?? null;
        // 获取数据
        $list = BillRecords::where('user_id', $request->uid);
        if (!is_null($income_type)) {
            $list = $list->where('income_type', $income_type);
        }
        if (!is_null($trade_type)) {
            $list = $list->where('trade_type', $trade_type);
        }
        if (!is_null($trade_time)) {
            list($start_time, $end_time) = $trade_time;
            $start_time = Carbon::parse($start_time / 1000)->timezone(config('app.default_timezone'))->setTime(0, 0, 0)->timestamp;
            $end_time = Carbon::parse($end_time / 1000)->timezone(config('app.default_timezone'))->setTime(23, 59, 59)->timestamp;
            $list = $list->whereBetween('trade_time', [$start_time, $end_time]);
        }
        $list = $list->orderBy('trade_time', 'desc')->get([
            'trade_no' => 'trade_no',
            'platform' => 'platform',
            'trade_type' => 'trade_type',
            'income_type' => 'income_type',
            'product_name' => 'product_name',
            'counterparty' => 'counterparty',
            'payment_method' => 'payment_method',
            'amount' => 'amount',
            'trade_status' => 'trade_status',
            'trade_time' => 'trade_time'
        ]);
        foreach ($list as &$_list) {
            $_list->trade_time = Carbon::parse($_list->trade_time)->timezone(config('app.default_timezone'))->format('Y-m-d H:i:s');
        }
        $data = is_array($list) ? $list : $list->toArray();
        // 返回数据
        return success($request, [
            "list_data" => $data
        ]);
    }

    /**
     * 获取收入分类
     * 
     * @param array $trade_time 交易时间
     * 
     * @return Response 
     */
    public function getIncomeCategories(Request $request): Response
    {
        // 获取参数
        $trade_time = $request->data['trade_time'] ?? null;
        // 获取数据
        $bill_records = BillRecords::where('user_id', $request->uid)->where('income_type', BillRecordsEnums\IncomeType::Income->value);
        if (!is_null($trade_time)) {
            list($start_time, $end_time) = $trade_time;
            $start_time = Carbon::parse($start_time / 1000)->timezone(config('app.default_timezone'))->setTime(0, 0, 0)->timestamp;
            $end_time = Carbon::parse($end_time / 1000)->timezone(config('app.default_timezone'))->setTime(23, 59, 59)->timestamp;
            $bill_records = $bill_records->whereBetween('trade_time', [$start_time, $end_time]);
        }
        $bill_records = $bill_records->groupBy('trade_type')->orderBy('total', 'desc')->get([
            'trade_type' => 'trade_type',
            'count' => Db::raw('COUNT(*) as count'),
            'total' => Db::raw('SUM(amount) as total')
        ]);
        // 返回数据
        return success($request, [
            'bill_records' => $bill_records
        ]);
    }
    /**
     * 获取支出分类
     * 
     * @param array $trade_time 交易时间
     * 
     * @return Response 
     */
    public function getExpenseCategories(Request $request): Response
    {
        // 获取参数
        $trade_time = $request->data['trade_time'] ?? null;
        // 获取数据
        $bill_records = BillRecords::where('user_id', $request->uid)->where('income_type', BillRecordsEnums\IncomeType::Expense->value);
        if (!is_null($trade_time)) {
            list($start_time, $end_time) = $trade_time;
            $start_time = Carbon::parse($start_time / 1000)->timezone(config('app.default_timezone'))->setTime(0, 0, 0)->timestamp;
            $end_time = Carbon::parse($end_time / 1000)->timezone(config('app.default_timezone'))->setTime(23, 59, 59)->timestamp;
            $bill_records = $bill_records->whereBetween('trade_time', [$start_time, $end_time]);
        }
        $bill_records = $bill_records->groupBy('trade_type')->orderBy('total', 'desc')->get([
            'trade_type' => 'trade_type',
            'count' => Db::raw('COUNT(*) as count'),
            'total' => Db::raw('SUM(amount) as total')
        ]);
        // 返回数据
        return success($request, [
            'bill_records' => $bill_records
        ]);
    }

    /**
     * 获取阶段收支信息
     * 
     * @param array $trade_time 交易时间
     * @param string $granularity 时间粒度(day/week/month)
     * 
     * @return Response 
     */
    public function getFinancialSummary(Request $request): Response
    {
        // 获取参数
        $trade_time = $request->data['trade_time'] ?? null;
        $granularity = $request->data['granularity'] ?? 'month';
        // 获取数据
        $bill_records = BillRecords::where('user_id', $request->uid);
        if (!is_null($trade_time)) {
            list($start_time, $end_time) = $trade_time;
            $start_time = Carbon::parse($start_time / 1000)->timezone(config('app.default_timezone'))->setTime(0, 0, 0)->timestamp;
            $end_time = Carbon::parse($end_time / 1000)->timezone(config('app.default_timezone'))->setTime(23, 59, 59)->timestamp;
            $bill_records = $bill_records->whereBetween('trade_time', [$start_time, $end_time]);
        }
        $bill_records = $bill_records->groupByRaw("FROM_UNIXTIME(trade_time, '%Y-%m-%d'),income_type")->orderByRaw("FROM_UNIXTIME(trade_time, '%Y-%m-%d')")->get([
            'trade_date' => Db::raw("FROM_UNIXTIME(trade_time, '%Y-%m-%d') as trade_date"),
            'income_type' => 'income_type',
            'count' => Db::raw('count(*) as count'),
            'total_amount' => Db::raw('sum(amount) as total_amount')
        ])->toArray();
        // 获取日期范围（从最早记录到当天）
        $start_date = count($bill_records) > 0 ? Carbon::parse($bill_records[0]['trade_date'])->timezone(config('app.default_timezone')) : Carbon::today()->timezone(config('app.default_timezone'));
        $end_date = Carbon::today()->timezone(config('app.default_timezone'));
        // 初始化完整日期数组（包含所有可能的日期）
        $date_range = [];
        $current_date = $start_date->copy();
        while ($current_date <= $end_date) {
            $date_key = $current_date->format('Y-m-d');
            $date_range[$date_key] = [
                'income' => ['count' => 0, 'total_amount' => 0],
                'expense' => ['count' => 0, 'total_amount' => 0],
                'date' => $current_date->format('Y-m-d'),
                'timestamp' => $current_date->timestamp
            ];
            $current_date->addDay();
        }
        // 填充已有数据
        foreach ($bill_records as $record) {
            $date_key = $record['trade_date'];
            $type = $record['income_type'] == BillRecordsEnums\IncomeType::Income->value ? 'income' : 'expense';
            if (isset($date_range[$date_key])) {
                $date_range[$date_key][$type] = [
                    'count' => $record['count'],
                    'total_amount' => $record['total_amount']
                ];
            }
        }
        // 按粒度分组处理
        $result = [];
        switch ($granularity) {
            case 'day':
                // 直接返回每日数据（格式：2023年3月15日）
                $result = array_map(function ($item) {
                    return [
                        'range' => Carbon::parse($item['date'])->timezone(config('app.default_timezone'))->format('Y年n月j日'),
                        'income' => $item['income'],
                        'expense' => $item['expense'],
                        'start_timestamp' => Carbon::parse($item['date'])->timezone(config('app.default_timezone'))->setTime(0, 0, 0)->timestamp,
                        'end_timestamp' => Carbon::parse($item['date'])->timezone(config('app.default_timezone'))->setTime(23, 59, 59)->timestamp
                    ];
                }, array_values($date_range));
                break;

            case 'week':
                // 按周分组（周一到周日）
                $grouped = [];
                foreach ($date_range as $date_str => $data) {
                    $date = Carbon::parse($date_str)->timezone(config('app.default_timezone'));
                    $year = $date->year;
                    $week = $date->weekOfYear;
                    $key = $year . '-' . $week;

                    if (!isset($grouped[$key])) {
                        $monday = $date->copy()->startOfWeek();
                        $sunday = $date->copy()->endOfWeek();
                        $grouped[$key] = [
                            'range' => $monday->format('Y年n月j日') . ' - ' . $sunday->format('Y年n月j日'),
                            'income' => ['count' => 0, 'total_amount' => 0],
                            'expense' => ['count' => 0, 'total_amount' => 0],
                            'start_timestamp' => $monday->timestamp,
                            'end_timestamp' => $sunday->timestamp
                        ];
                    }

                    $grouped[$key]['income']['count'] += $data['income']['count'];
                    $grouped[$key]['income']['total_amount'] += round($data['income']['total_amount'], 2);
                    $grouped[$key]['expense']['count'] += $data['expense']['count'];
                    $grouped[$key]['expense']['total_amount'] += round($data['expense']['total_amount'], 2);
                }
                $result = array_values($grouped);
                break;

            case 'month':
                // 按月分组
                $grouped = [];
                foreach ($date_range as $date_str => $data) {
                    $date = Carbon::parse($date_str)->timezone(config('app.default_timezone'));
                    $key = $date->format('Y-m');

                    if (!isset($grouped[$key])) {
                        $grouped[$key] = [
                            'range' => $date->format('Y年n月'),
                            'income' => ['count' => 0, 'total_amount' => 0],
                            'expense' => ['count' => 0, 'total_amount' => 0],
                            'start_timestamp' => $date->firstOfMonth()->timestamp,
                            'end_timestamp' => $date->lastOfMonth()->setTime(23, 59, 59)->timestamp
                        ];
                    }

                    $grouped[$key]['income']['count'] += $data['income']['count'];
                    $grouped[$key]['income']['total_amount'] += round($data['income']['total_amount'], 2);
                    $grouped[$key]['expense']['count'] += $data['expense']['count'];
                    $grouped[$key]['expense']['total_amount'] += round($data['expense']['total_amount'], 2);
                }
                $result = array_values($grouped);
                break;
        }
        foreach ($result as &$_result) {
            $_result['income']['total_amount'] = round($_result['income']['total_amount'], 2);
            $_result['expense']['total_amount'] = round($_result['expense']['total_amount'], 2);
        }
        // 返回数据
        return success($request, [
            'date_range' => $result
        ]);
    }
}
