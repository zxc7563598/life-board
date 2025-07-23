<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class FillDashboardWidgetsAddNowNewsCard extends AbstractMigration
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
                "id" => 9,
                "name" => "热门新闻",
                "component_key" => "NowNews",
                "description" => "展示当前热门新闻，支持快速查看和跳转",
                "is_active" => 1,
                "created_at" => time(),
                "updated_at" => time(),
                "deleted_at" => null
            ],
        ]);
        $table->saveData();
    }
}
