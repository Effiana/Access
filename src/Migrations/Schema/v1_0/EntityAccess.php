<?php
/**
 * This file is part of the Effiana package.
 *
 * (c) Effiana, LTD
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Dominik Labudzinski <dominik@labudzinski.com>
 */
declare(strict_types=0);

namespace Effiana\Access\Migrations\Schema\v1_0;

use Doctrine\DBAL\Schema\Schema;
use Effiana\MigrationBundle\Migration\Column;
use Effiana\MigrationBundle\Migration\Migration;
use Effiana\MigrationBundle\Migration\QueryBag;

class EntityAccess implements Migration
{

    /**
     * Modifies the given schema to apply necessary changes of a database
     * The given query bag can be used to apply additional SQL queries before and after schema changes
     *
     * @param Schema $schema
     * @param QueryBag $queries
     * @return void
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        if(!$schema->hasTable('effiana_entity_access')) {
            $table = $schema->createTable('effiana_entity_access');

            $table->addColumn('id', Column::INTEGER, ['notnull' => true, 'autoincrement' => true]);
            $table->addColumn('entity_id', Column::INTEGER, ['notnull' => true]);
            $table->addColumn('entity_class', Column::STRING, ['notnull' => true, 'length' => 1000]);
            $table->addColumn('mask', Column::INTEGER, ['notnull' => true, 'options' => ['default' => 0]]);

            $table->addColumn('created_at', Column::DATETIME, ['notnull' => true]);
            $table->addColumn('updated_at', Column::DATETIME, ['notnull' => true]);
            $table->addColumn('created_by_id', Column::INTEGER, ['notnull' => true]);
            $table->addColumn('updated_by_id', Column::INTEGER, ['notnull' => false]);
            $table->addColumn('deleted_by_id', Column::INTEGER, ['notnull' => false]);

            $table->addColumn('user_id', Column::INTEGER, ['notnull' => false]);
            $table->addColumn('project_role_id', Column::INTEGER, ['notnull' => false]);

            $table->setPrimaryKey(['id']);

            $table->addForeignKeyConstraint('users', ['created_by_id'], ['id']);
            $table->addForeignKeyConstraint('users', ['updated_by_id'], ['id']);
            $table->addForeignKeyConstraint('users', ['deleted_by_id'], ['id']);

            $table->addForeignKeyConstraint('project_role', ['project_role_id'], ['id']);
            $table->addForeignKeyConstraint('users', ['user_id'], ['id']);
        }
    }
}