<?php

declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class CreateTodos extends AbstractMigration
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
        $table = $this->table('todos', ['id' => 'id', 'engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '待办事项表']);
        // 添加字段
        $table->addColumn('user_id', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'null' => false, 'comment' => '用户id'])
            ->addColumn('category_id', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'null' => true, 'comment' => '所属分类（可为空）'])
            ->addColumn('title', 'string', ['null' => false, 'comment' => '标题'])
            ->addColumn('content', 'text', ['null' => true, 'comment' => '内容描述'])
            ->addColumn('color', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => false, 'comment' => '颜色'])
            ->addColumn('start_at', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'null' => false, 'comment' => '起始时间'])
            ->addColumn('end_at', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'null' => false, 'comment' => '结束时间'])
            ->addColumn('completed_at', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'null' => true, 'comment' => '完成时间，非重复状态下为彻底完成时间，重复状态下为最后一次完成时间'])
            ->addColumn('repeat_type', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => false, 'comment' => '重复类型'])
            ->addColumn('repeat_interval', 'integer', ['limit' => MysqlAdapter::INT_MEDIUM, 'null' => true, 'comment' => '间隔：每 X 天/周/月/年'])
            ->addColumn('repeat_until', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'null' => true, 'comment' => '重复截止时间'])
            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'null' => false, 'comment' => '创建时间'])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'null' => true, 'comment' => '更新时间'])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'null' => true, 'comment' => '逻辑删除'])
            ->create();
    }
}
