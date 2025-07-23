<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class FillDashboardWidgetsAddSearchCard extends AbstractMigration
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
                "id" => 10,
                "name" => "快速搜索",
                "component_key" => "SearchCard",
                "description" => "提供快速搜索功能，支持在百度、Google、哔哩哔哩和知乎等平台进行搜索",
                "is_active" => 1,
                "created_at" => time(),
                "updated_at" => time(),
                "deleted_at" => null
            ],
        ]);
        $table->saveData();
    }
}
