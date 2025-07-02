<?php

declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class CreateBillRecords extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        // 创建表
        $table = $this->table('bill_records', ['id' => 'id', 'engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '账单记录表']);
        // 添加字段
        $table->addColumn('user_id', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'null' => false, 'comment' => '用户id'])
            ->addColumn('trade_no', 'string', ['null' => true, 'comment' => '交易单号'])
            ->addColumn('merchant_order_no', 'string', ['null' => true, 'comment' => '商户单号'])
            ->addColumn('platform', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => true,  'comment' => '平台（支付宝、微信）'])
            ->addColumn('income_type', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => true,  'comment' => '收支类型（收入、支出、或者不记收支）'])
            ->addColumn('trade_type', 'string', ['null' => true, 'comment' => '交易类型（分类）'])
            ->addColumn('product_name', 'string', ['null' => true, 'comment' => '商品（交易名称）'])
            ->addColumn('counterparty', 'string', ['null' => true, 'comment' => '交易对方（商户名称）'])
            ->addColumn('payment_method', 'string', ['null' => true, 'comment' => '交易方式（余额、银行卡）'])
            ->addColumn('amount', 'decimal', ['precision' => 10, 'scale' => 2, 'null' => true, 'comment' => '金额'])
            ->addColumn('trade_status', 'string', ['null' => true, 'comment' => '交易状态（成功、失败、关闭、退款等）'])
            ->addColumn('trade_time', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'null' => false, 'comment' => '交易时间'])
            ->addColumn('remark', 'string', ['null' => true, 'comment' => '备注'])
            ->addColumn('is_hidden', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'default' => 0, 'null' => false,  'comment' => '是否隐藏'])
            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'null' => false, 'comment' => '创建时间'])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'null' => true, 'comment' => '更新时间'])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'null' => true, 'comment' => '逻辑删除'])
            ->addIndex(['user_id', 'deleted_at'], [
                'name' => 'user_id_no_deleted_at',
                'unique' => false,
            ])->create();
    }
}
