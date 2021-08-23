<?php

namespace OCA\Assembly\Settings;

use OCA\Assembly\AppInfo\Application;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Settings\IIconSection;

class AdminSettings implements IIConSection{

	/** @var IL10N */
	private $l;
	/** @var IURLGenerator */
	private $urlGenerator;

	public function __construct(IL10N $l, IURLGenerator $urlGenerator) {
		$this->l = $l;
		$this->urlGenerator = $urlGenerator;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getID(): string {
		return Application::APP_ID;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getName(): string {
		return $this->l->t('Assembly');
	}

    /**
	 * {@inheritdoc}
	 */
	public function getPriority(): int {
		return 60;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getIcon(): string {
		return $this->urlGenerator->imagePath(Application::APP_ID, 'app.svg');
	}
}