<?php

declare(strict_types=1);

namespace OCA\Assembly\Command;

use OC\Core\Command\Base;
use OCP\AppFramework\Services\IAppConfig;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class JitsiEnable extends Base {
	/** @var IAppConfig */
	protected $appConfig;

	public function __construct(IAppConfig $appConfig) {
		$this->appConfig = $appConfig;
		parent::__construct();
	}

	protected function configure() {
		$this
			->setName('assembly:jitsi-enable')
			->setDescription('Enable run meetings with Jitsi')
			->addOption(
				'appid',
				null,
				InputOption::VALUE_REQUIRED,
				'Application id'
			)
			->addOption(
				'secret',
				null,
				InputOption::VALUE_REQUIRED,
				'Secret key'
			)
			->addOption(
				'url',
				null,
				InputOption::VALUE_REQUIRED,
				'Jitsi base URL'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		$appId = $input->getOption('appid');
		if (!$appId) {
			$output->writeln('<error>AppId is mandatory</error>');
			return Base::FAILURE;
		}
		$secret = $input->getOption('secret');
		if (!$secret) {
			$output->writeln('<error>Secret is mandatory</error>');
			return Base::FAILURE;
		}
		$url = $input->getOption('url');
		if (!$url) {
			$output->writeln('<error>Url is mandatory</error>');
			return Base::FAILURE;
		}
		$this->appConfig->setAppValue('enable_mutesi', '0');
		$this->appConfig->setAppValue('enable_jitsi_jwt', '1');
		$this->appConfig->setAppValue('jitsi_appid', $appId);
		$this->appConfig->setAppValue('jitsi_secret', $secret);
		$this->appConfig->setAppValue('jitsi_url', $url);
		$output->writeln('Jitsi enabled');
		return Base::SUCCESS;
	}
}
