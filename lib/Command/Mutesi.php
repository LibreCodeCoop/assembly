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

use OC\Core\Command\Base;
use OCP\AppFramework\Services\IAppConfig;
use OCP\GlobalScale\IConfig;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Mutesi extends Base {
	/** @var IAppConfig */
	protected $appConfig;

	public function __construct(IAppConfig $appConfig) {
		$this->appConfig = $appConfig;
		parent::__construct();
	}

	protected function configure() {
		$this
			->setName('assembly:mutesi')
			->setDescription('set mutesi status')
			->addOption(
				'on',
				null,
				InputOption::VALUE_NONE,
				'enable mutesi'
			)
			->addOption(
				'off',
				null,
				InputOption::VALUE_NONE,
				'disable mutesi'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		$enabled = $this->appConfig->getAppValue('enable_mutesi');
		if ($input->getOption('on')) {
			if (!$enabled) {
				$this->appConfig->setAppValue('enable_mutesi', '1');
				$output->writeln('Mutesi enabled');
			} else {
				$output->writeln('Mutesi already enabled');
			}
		} elseif ($input->getOption('off')) {
			if ($enabled) {
				$this->appConfig->setAppValue('enable_mutesi', '0');
				$output->writeln('Mutesi disabled');
			} else {
				$output->writeln('Mutesi already disabled');
			}
		} else {
			if ($enabled) {
				$output->writeln('Mutesi is currently enabled');
			} else {
				$output->writeln('Mutesi is currently disabled');
			}
		}
		return 0;
	}
}
