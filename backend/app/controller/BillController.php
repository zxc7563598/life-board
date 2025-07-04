<?php

namespace app\controller;

use app\model\BillRecords;
use support\Request;
use Carbon\Carbon;
use resource\enums\BillRecordsEnums;
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
     * 获取账单列表信息
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
}
