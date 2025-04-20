<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateRecipesTable extends AbstractMigration
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
        $this->table('recipes')
            ->addColumn('title', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('category', 'integer', ['null' => false, 'signed' => false])
            ->addColumn('ingredients', 'text', ['null' => true])
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('tags', 'text', ['null' => true])
            ->addColumn('steps', 'text', ['null' => true])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addForeignKey('category', 'categories', 'id', ['delete'=> 'CASCADE']) // Foreign key constraint
            ->create();
    }
}
