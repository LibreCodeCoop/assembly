<?php

declare(strict_types=1);

namespace OCA\Assembly\Command;

use OC\Core\Command\Base;
use OCP\AppFramework\Services\IAppConfig;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class JitsiDisable extends Base {
	/** @var IAppConfig */
	protected $appConfig;

	public function __construct(IAppConfig $appConfig) {
		$this->appConfig = $appConfig;
		parent::__construct();
	}

	protected function configure() {
		$this
			->setName('assembly:jitsi-disable')
			->setDescription('disable jitsi');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		$this->appConfig->setAppValue('enable_jitsi_jwt', '0');
		$output->writeln('Jitsi disabled');
		return Base::SUCCESS;
	}
}
