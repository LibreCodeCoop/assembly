<?php

namespace OCA\Assembly\AppInfo;

use OCP\AppFramework\App;

class Application extends App {
	public const APP_ID = 'assembly';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}
}
