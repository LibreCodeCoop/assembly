<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2019 Lyseon Tech <dev@LT.coop.br>
 *
 * @author Lyseon Tech <dev@LT.coop.br>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Assembly\Command;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use OC\Core\Command\Base;
use OCP\Http\Client\IClientService;
use OCP\IDBConnection;
use OCP\IGroup;
use OCP\IGroupManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class BbbRegistry extends Base {
	/** @var IDBConnection */
	protected $db;

	/** @var IGroupManager */
	protected $groupManager;

	/** @var IClientService */
	protected $clientService;

	/**
	 * @param IGroupManager $groupManager
	 */
	public function __construct(IGroupManager $groupManager, IDBConnection $db, IClientService $clientService) {
		$this->db = $db;
		$this->clientService = $clientService;
		$this->groupManager = $groupManager;
		parent::__construct();
	}

	protected function configure() {
		$this
			->setName('assembly:bbb:registry')
			->setDescription('Registry new meet')
			->addArgument(
				'url',
				InputArgument::REQUIRED,
				'The base URL',
				null
			)
			->addOption(
				'start',
				's',
				InputOption::VALUE_OPTIONAL,
				'Start time in format Y-m-d\TH:i:s, example 2020-09-15T17:45:00',
				date('Y-m-d\TH:i:s')
			)
			->addOption(
				'output',
				'o',
				InputOption::VALUE_OPTIONAL,
				'Output format (plain, json or json_pretty, default is plain)',
				$this->defaultOutputFormat
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		$groups = $this->groupManager->search('');
		$groupIDs = array_map(function (IGroup $group) {
			return $group->getGID();
		}, $groups);
		$helper = $this->getHelper('question');
		$question = new ChoiceQuestion(
			'Please select the group:',
			array_merge($groupIDs, ['Cancel'])
		);
		$question->setErrorMessage('Group %s is invalid.');

		$answer = $helper->ask($input, $output, $question);
		$group = array_filter($groups, function(IGroup $group) use ($answer) {
			return $group->getGID() == $answer;
		});
		if (!$group || count($group) > 1) {
			$output->writeln('Nothing todo.');
			return 1;
		}
		$group = array_values($group)[0];

		$date = \DateTime::createFromFormat('Y-m-d\TH:i:s', $input->getOption('start'));
		if (!$date) {
			$output->writeln('Invalid date.');
			return 1;
		}

		$body = $this->formatBody($date->format('Y-m-d\TH:i:s'), $group);
		$client = $this->clientService->newClient();
		$options = [
			'headers' => [ 'Content-Type' => 'application/json' ],
			'body' => json_encode($body),
			'nextcloud' => ['allow_local_address' => true]
		];
		$response = $client->post($input->getArgument('url') . '/meetings', $options);
		$statusCode = $response->getStatusCode();
		if ($statusCode !== 200) {
			$output->writeln(sprintf("<error>Invalid Status code: '%s', I wait for 201.</error>", $statusCode));
			return 1;
		}
		$responseBody = json_decode($response->getBody());
		if (!$responseBody) {
			$output->writeln('Invalid response from Mutesi.');
			return 1;
		}
		$query = $this->db->getQueryBuilder();
		try {
			$query->insert('assembly_meetings')
				->values([
					'meeting_id' => $query->createNamedParameter($responseBody->id),
					'created_at' => $query->createNamedParameter(time()),
					'meeting_time' => $query->createNamedParameter($date->format('U'))
				])
				->execute();
		} catch (UniqueConstraintViolationException $e) {
			$output->writeln('<error>Meeting already registered</error>');
			return 1;
		}
		$output->writeln(sprintf('Success! Transaction ID: <info>%s</info>', $responseBody->id));
		$output->writeln('Request body:');
		$this->writeArrayInOutputFormat($input, $output, $body);
		return 0;
	}

	/**
	 * @param IGroup $group
	 * @return array
	 */
	private function formatBody(string $start, IGroup $group) {
		$users = $group->getUsers();
		$body = new \stdClass();
		$body->name = $group->getDisplayName();
		$body->datetime = $start . '.000Z';
		$body->attendees = array_values(array_map(function ($user) {
			return (object)[
				'id' => $user->getUID(),
				'name' => $user->getDisplayName() ?? $user->getUID()
			];
		}, $users));
		return $body;
	}
}
