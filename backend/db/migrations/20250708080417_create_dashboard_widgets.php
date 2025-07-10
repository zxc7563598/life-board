<?php

declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class CreateDashboardWidgets extends AbstractMigration
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
        $table = $this->table('dashboard_widgets', ['id' => 'id', 'engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '系统预定义组件']);
        // 添加字段
        $table->addColumn('name', 'string', ['null' => false, 'comment' => '组件名称'])
            ->addColumn('component_key', 'string', ['null' => false, 'comment' => '唯一标识'])
            ->addColumn('description', 'text', ['null' => false, 'comment' => '组件描述'])
            ->addColumn('is_active', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => false, 'default' => 1, 'comment' => '是否启用'])
            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'null' => false, 'comment' => '创建时间'])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'null' => true, 'comment' => '更新时间'])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'null' => true, 'comment' => '逻辑删除'])
            ->create();
        // 添加数据
        $table->insert([
            [
                "id" => 1,
                "name" => "默认欢迎组件",
                "component_key" => "WelcomeMessage",
                "description" => "欢迎使用个人生活管理平台，可自定义页面组件布局",
                "is_active" => 1,
                "created_at" => time(),
                "updated_at" => time(),
                "deleted_at" => null
            ],
            [
                "id" => 2,
                "name" => "账单模块说明组件",
                "component_key" => "BillModuleGuide",
                "description" => "简要介绍如何通过邮箱导入账单并进行数据分析",
                "is_active" => 1,
                "created_at" => time(),
                "updated_at" => time(),
                "deleted_at" => null
            ],
            [
                "id" => 3,
                "name" => "支出分类 Top10（饼图）",
                "component_key" => "ExpenseCategoryTop10",
                "description" => "展示支出账单的分类占比，点击分类查看详细支出记录",
                "is_active" => 1,
                "created_at" => time(),
                "updated_at" => time(),
                "deleted_at" => null
            ],
            [
                "id" => 4,
                "name" => "收入分类 Top10（饼图）",
                "component_key" => "IncomeCategoryTop10",
                "description" => "展示收入账单的分类占比，点击分类查看详细收入记录",
                "is_active" => 1,
                "created_at" => time(),
                "updated_at" => time(),
                "deleted_at" => null
            ],
            [
                "id" => 5,
                "name" => "收益概览（折线图）",
                "component_key" => "IncomeExpenseOverview",
                "description" => "展示每月的收入与支出走势，点击节点查看明细",
                "is_active" => 1,
                "created_at" => time(),
                "updated_at" => time(),
                "deleted_at" => null
            ],
        ]);
        $table->saveData();
    }
}
