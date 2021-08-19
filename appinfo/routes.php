<?php
/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\Assembly\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
	'routes' => [
		// API
		['name' => 'api#usersAvailable', 'url' => '/api/v1/usersAvailable', 'verb' => 'GET'],
		['name' => 'api#dashboard', 'url' => '/api/v1/dashboard', 'verb' => 'GET'],
		['name' => 'api#meetWebhook', 'url' => '/api/v1/meetWebhook', 'verb' => 'POST'],
		['name' => 'api#getMeetings', 'url' => '/api/v1/meet', 'verb' => 'GET'],
		['name' => 'api#getPools', 'url' => '/api/v1/pools/{meetId}', 'verb' => 'GET'],
		['name' => 'api#getTos', 'url' => '/api/v1/report/tos/{groupId}', 'verb' => 'GET'],
		['name' => 'api#getVotes', 'url' => '/api/v1/report/votes/{meetId}', 'verb' => 'GET'],
		['name' => 'api#getVotes', 'url' => '/api/v1/report/votes/{meetId}', 'verb' => 'GET'],
		['name' => 'api#getAttendances', 'url' => '/api/v1/report/attendances/{meetId}', 'verb' => 'GET'],

		// Pages
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
		['name' => 'page#videocall', 'url' => '/videocall/{meetingId}', 'verb' => 'GET'],
	]
];
