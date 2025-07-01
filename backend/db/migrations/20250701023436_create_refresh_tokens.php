<?php

declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class CreateRefreshTokens extends AbstractMigration
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
        $table = $this->table('refresh_tokens', ['id' => 'id', 'engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci', 'comment' => '用户refresh_token表']);
        // 添加字段
        $table->addColumn('user_id', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'null' => false, 'comment' => '用户id'])
            ->addColumn('token', 'string', ['null' => false, 'limit' => 512, 'comment' => 'token'])
            ->addColumn('expires_at', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'null' => false, 'comment' => '过期时间'])
            ->addColumn('revoked', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => false, 'default' => 0, 'comment' => '是否销毁'])
            ->addColumn('ip', 'string', ['null' => false, 'comment' => 'IP地址'])
            ->addColumn('ip_address', 'string', ['null' => true, 'comment' => 'IP对应地址'])
            ->addColumn('browser_name', 'string', ['null' => true, 'comment' => '浏览器名称'])
            ->addColumn('browser_version', 'string', ['null' => true, 'comment' => '浏览器版本'])
            ->addColumn('engine_name', 'string', ['null' => true, 'comment' => '获取引擎名称'])
            ->addColumn('os_name', 'string', ['null' => true, 'comment' => '操作系统名称'])
            ->addColumn('os_version', 'string', ['null' => true, 'comment' => '操作系统版本'])
            ->addColumn('platform_type', 'string', ['null' => true, 'comment' => '获取平台类型'])
            ->addColumn('ua', 'string', ['null' => true, 'limit' => 512, 'comment' => '提取ua'])
            ->addColumn('created_at', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'null' => false, 'comment' => '创建时间'])
            ->addColumn('updated_at', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'null' => true, 'comment' => '更新时间'])
            ->addColumn('deleted_at', 'integer', ['limit' => MysqlAdapter::INT_BIG, 'null' => true, 'comment' => '逻辑删除']);
    }
}
