<?php

declare(strict_types=1);

namespace OCA\Assembly\Migration;

use Closure;
use Doctrine\DBAL\Types\Types;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

/**
 * Auto-generated migration step: Please modify to your needs!
 */
class Version1000Date20210819002240 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if (!$schema->hasTable('assembly_meeting_pools')) {
			$table = $schema->createTable('assembly_meeting_pools');
			$table->addColumn('meeting_id', Types::STRING, [
				'notnull' => true,
			]);
			$table->addColumn('form_id', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->addColumn('created_at', Types::INTEGER, [
				'notnull' => true,
				'length' => 4,
				'default' => 0,
			]);
			$table->setPrimaryKey(['meeting_id', 'form_id']);
		}
		return $schema;
	}
}
