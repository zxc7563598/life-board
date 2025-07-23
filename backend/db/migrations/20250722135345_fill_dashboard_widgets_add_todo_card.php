<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class FillDashboardWidgetsAddTodoCard extends AbstractMigration
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
        $table = $this->table('dashboard_widgets');
        // 添加数据
        $table->insert([
            [
                "id" => 7,
                "name" => "今日待办",
                "component_key" => "TodoCard",
                "description" => "展示截止到今日未完成的待办事项，支持快速添加和查看",
                "is_active" => 1,
                "created_at" => time(),
                "updated_at" => time(),
                "deleted_at" => null
            ],
            [
                "id" => 8,
                "name" => "待办日历",
                "component_key" => "Calendar",
                "description" => "展示待办事项的日历视图，支持查看和管理待办事项",
                "is_active" => 1,
                "created_at" => time(),
                "updated_at" => time(),
                "deleted_at" => null
            ],
        ]);
        $table->saveData();
    }
}
