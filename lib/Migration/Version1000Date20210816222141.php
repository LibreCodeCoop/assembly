<?php

declare(strict_types=1);

namespace OCA\Assembly\Migration;

use Closure;
use Doctrine\DBAL\Types\Types;
use OCP\DB\ISchemaWrapper;
use OCP\IDBConnection;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

/**
 * Auto-generated migration step: Please modify to your needs!
 */
class Version1000Date20210816222141 extends SimpleMigrationStep {

	/** @var IDBConnection */
	protected $connection;
	private $rows = [];

	/**
	 * @param IDBConnection $connection
	 */
	public function __construct(IDBConnection $connection) {
		$this->connection = $connection;
	}

	public function preSchemaChange(IOutput $output, \Closure $schemaClosure, array $options) {
		$query = $this->connection->getQueryBuilder();
		$query->select('id', 'url', 'password')
			->from('assembly_participants', 'p');
		$this->rows = $query->executeQuery()->fetchAll();
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();
		$participants = $schema->getTable('assembly_participants');

		$participants->dropColumn('url');

		$participants->addColumn('url', Types::STRING, [
			'notnull' => false,
			'length' => 512,
		]);

		$participants->dropColumn('password');

		$participants->addColumn('password', Types::STRING, [
			'notnull' => false,
			'length' => 256,
		]);

		$meetings = $schema->getTable('assembly_meetings');

		$meetings->addColumn('created_by', Types::STRING, [
			'notnull' => false,
			'length' => 64,
		]);
		$meetings->addColumn('slug', Types::STRING, [
			'notnull' => false,
			'length' => 64,
		]);
		$meetings->addColumn('description', Types::TEXT, [
			'notnull' => false,
		]);
		$meetings->addColumn('deleted_at', Types::INTEGER, [
			'notnull' => false,
			'length' => 4,
			'default' => 0,
		]);
		$meetings->addColumn('status', Types::STRING, [
			'notnull' => false,
			'length' => 64,
		]);

		return $schema;
	}

	public function postSchemaChange(IOutput $output, \Closure $schemaClosure, array $options) {
		foreach ($this->rows as $row) {
			$query = $this->connection->getQueryBuilder();
			$query
				->update('assembly_participants')
				->set('url', $query->createNamedParameter((string)$row['url']))
				->set('password', $query->createNamedParameter((string)$row['password']))
				->where($query->expr()->eq('id', $query->createNamedParameter($row['id'])));
	
			$query->executeQuery();
		}
	}
}
