<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ModifyUserAddBillmailConfig extends AbstractMigration
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
        $table = $this->table('user');
        $table->addColumn('mail_password', 'string', ['null' => true, 'after' => 'salt', 'comment' => '邮箱密码或授权码'])
            ->addColumn('mail_username', 'string', ['null' => true, 'after' => 'salt', 'comment' => '用于收账单的邮箱账号'])
            ->addColumn('mail_host', 'string', ['null' => true, 'after' => 'salt', 'comment' => '邮箱服务器（如 imap.qq.com）'])
            ->update();
    }
}
