<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class FillDashboardWidgetsAddRealTimeClockCard extends AbstractMigration
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
                "id" => 6,
                "name" => "此刻时间",
                "component_key" => "RealTimeClockCard",
                "description" => "显示当前系统时间，自动更新，适配明亮与暗黑模式",
                "is_active" => 1,
                "created_at" => time(),
                "updated_at" => time(),
                "deleted_at" => null
            ],
        ]);
        $table->saveData();
    }
}
