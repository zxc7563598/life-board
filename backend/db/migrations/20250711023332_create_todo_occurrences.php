<?php

declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class CreateTodoOccurrences extends AbstractMigration
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
        $table = $this->table('todo_occurrences', ['id' => 'id', 'engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '重复事项实例完成记录表']);
        // 添加字段
        $table->addColumn('user_id', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'null' => false, 'comment' => '用户id'])
            ->addColumn('todo_id', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'null' => false, 'comment' => '对应的事项 ID'])
            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'null' => false, 'comment' => '创建时间'])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'null' => true, 'comment' => '更新时间'])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'null' => true, 'comment' => '逻辑删除'])
            ->create();
    }
}
