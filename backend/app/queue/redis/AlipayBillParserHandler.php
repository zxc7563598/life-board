<?php

namespace app\queue\redis\bill;

use app\model\BillRecords;
use Carbon\Carbon;
use Webman\RedisQueue\Consumer;
use Hejunjie\AlipayBillParser\AlipayBillParser;
use Hejunjie\AlipayBillParser\ParseOptions;
use resource\enums\BillRecordsEnums;

class AlipayBillParserHandler implements Consumer
{
    public $queue = 'alipay-bill-parser';

    public $connection = 'default';

    // 消费
    public function consume($data)
    {
        // 获取参数
        $zipFile = $data['path'];
        $user_id = $data['user_id'];
        // 获取数据
        $options = new ParseOptions($zipFile);
        $options->onPasswordFound = function ($password) {
            return true;
        };
        $options->onDataParsed = function ($data) use ($user_id) {
            foreach ($data['data'] as $row) {
                $bill_records = BillRecords::where('user_id', $user_id)->where('trade_no', $row[9])->count();
                if (!$bill_records) {
                    $bill_records = new BillRecords();
                    $bill_records->user_id = $user_id;
                    $bill_records->trade_no = $row[9];
                    $bill_records->merchant_order_no = $row[10];
                    $bill_records->platform = BillRecordsEnums\Platform::Alipay->value;
                    $bill_records->income_type = match (true) {
                        str_contains($row[5], '收入') => BillRecordsEnums\IncomeType::Income->value,
                        str_contains($row[5], '支出') => BillRecordsEnums\IncomeType::Expense->value,
                        default => BillRecordsEnums\IncomeType::Uncategorized->value,
                    };;
                    $bill_records->trade_type = $row[1];
                    $bill_records->product_name = $row[4];
                    $bill_records->counterparty = $row[2];
                    $bill_records->payment_method = $row[7];
                    $bill_records->amount = $row[6];
                    $bill_records->trade_status = $row[8];
                    $bill_records->trade_time = Carbon::parse($row[0])->timezone(config('app.default_timezone'))->timestamp;
                    $bill_records->remark = $row[11];
                    $bill_records->is_hidden = BillRecordsEnums\IsHidden::No->value;
                    $bill_records->save();
                }
            }
            return true;
        };
        $parser = new AlipayBillParser();
        $parser->parse($options);
    }

    // 消费失败回调
    public function onConsumeFailure(\Throwable $e, $package)
    {
        echo "consume failure\n";
        echo $e->getMessage() . "\n";
        var_export($package);
    }
}
