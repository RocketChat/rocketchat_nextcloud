<?php

namespace OCA\RocketIntegration\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version000000Date20211203123456 extends SimpleMigrationStep {

    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     * @return null|ISchemaWrapper
     */
    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        if (!$schema->hasTable('rocket_file_chats')) {
            $table = $schema->createTable('rocket_file_chats');

            $table->addColumn('file_id', 'integer', [
                'autoincrement' => false,
                'notnull' => true,
                'unsigned' => true,
                'length' => 8,
            ]);
            $table->addColumn('chat_id', 'string', [
                'notnull' => true,
                'length' => 255,
            ]);
            $table->addColumn('created', 'datetime', [
                'notnull' => false,
            ]);

            $table->setPrimaryKey(['file_id']);
        }

        if (!$schema->hasTable('rocket_users')) {
            $table = $schema->createTable('rocket_users');

            $table->addColumn('nc_user_id', 'string', [
                'notnull' => true,
                'length' => 30,
            ]);

            $table->addColumn('rc_user_id', 'string', [
                'notnull' => true,
                'length' => 30
            ]);

            $table->addColumn('rc_token', 'string', [
                'notnull' => true,
                'length' => 50,
            ]);
            $table->addColumn('rc_current_channel_id', 'string', [
                'notnull' => true,
                'length' => 50,
            ]);
            $table->addColumn('rc_uuid_password', 'string', [
                'length' => 50,
            ]);
            $table->setPrimaryKey('nc_user_id');
        }

        return $schema;
    }
}
