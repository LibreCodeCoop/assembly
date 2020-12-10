<?php
namespace OCA\Assembly\Controller;

use OC\AppFramework\Services\AppConfig;
use OCA\Assembly\Db\ReportMapper;
use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\AppFramework\Services\IAppConfig;
use OCP\IDBConnection;
use OCP\IGroupManager;
use OCP\IUser;
use OCP\IUserSession;

class PageController extends Controller {
    /** @var IDBConnection */
	protected $db;

	private $userId;
	/** @var IGroupManager */
	protected $groupManager;

	/** @var IUserSession */
	protected $userSession;

	/** @var AppConfig */
	protected $appConfig;

	public function __construct(string $AppName,
								IRequest $request,
								string $UserId,
								ReportMapper $ReportMapper,
								IGroupManager $groupManager,
								IUserSession $userSession,
								IAppConfig $appConfig,
								IDBConnection $db) {
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
		$this->groupManager = $groupManager;
		$this->ReportMapper =  $ReportMapper;
		$this->userSession = $userSession;
		$this->appConfig = $appConfig;
		$this->db = $db;
	}

	/**
	 * CAUTION: the @Stuff turns off security checks; for this page no admin is
	 *          required and no CSRF check. If you don't know what CSRF is, read
	 *          it up in the docs or you might create a security hole. This is
	 *          basically the only required method to add this exemption, don't
	 *          add it to any other method if you don't exactly know what it does
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index() {
		$user = $this->userSession->getUser();
		if ($user instanceof IUser) {
			$groups = $this->groupManager->getUserGroupIds($this->userSession->getUser());
		} else {
			$groups = [];
		}
		$data = $this->ReportMapper->getPoll($this->userId);
		if ($this->appConfig->getAppValue('enable_mutesi')) {
			$query = $this->db->getQueryBuilder();
			$query->select(['url', 'meeting_time'])->from('assembly_participants', 'ap')
				->join('ap', 'assembly_meetings', 'am', 'am.meeting_id = ap.meeting_id')
				->where($query->expr()->eq('ap.uid', $query->createNamedParameter($this->userId)))
				->andWhere($query->expr()->gt('am.meeting_time', $query->createNamedParameter(
					time()-(60*60*24)
				)))
				->orderBy('ap.created_at', 'ASC');
			$stmt = $query->execute();
			$row = $stmt->fetch(\PDO::FETCH_ASSOC);
			if ($row && $row['meeting_time'] < time()) {
				$meetUrl = $row['url'];
			} else {
				$meetUrl = '/index.php/apps/assembly/videocall/' . ($row['meeting_id'] ?? 'wait');
			}
		} else {
			$meetUrl = 'https://meet.jit.si/' . date('Ymd') . $groups[0];
		}
		return new TemplateResponse('assembly', 'content/index',
			[
				'data' => $data,
				'group' => $groups,
				'meetUrl' => $meetUrl
			]
		);  // templates/report.php

	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */	
	public function report($formId, $groupId) {

		$data = $this->ReportMapper->getResult($this->userId, $formId);
		$available = $this->ReportMapper->usersAvailable($groupId);
		$responses = [];
		$metadata['total'] = 0;
		$metadata['available'] = count($available);
		foreach ($data as $row) {
			$responses[$row['response']] = $row['total'];
			$metadata['total']+=$row['total'];
		}
		if($data){
			$metadata['title'] = $data[0]['title'];
		}
		return new TemplateResponse('assembly', 'content/report', 
			[
				'responses'=>$responses,
				'metadata'=>$metadata
			] );  // templates/report.php
	}	

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function videocall($meetingId) {
		$query = $this->db->getQueryBuilder();
		$query->select(['url', 'meeting_time'])->from('assembly_participants', 'ap')
			->join('ap', 'assembly_meetings', 'am', 'am.meeting_id = ap.meeting_id');
		if ($meetingId != 'wait') {
			$query->where($query->expr()->eq('am.meeting_id', $query->createNamedParameter($meetingId)));
		}
		$query->andWhere($query->expr()->eq('ap.uid', $query->createNamedParameter($this->userId)));
		$stmt = $query->execute();
		$row = $stmt->fetch(\PDO::FETCH_ASSOC);
		if ($row) {
			if ($row['meeting_time'] < time()) {
				header('Location: ' . $row['url']);
			}
		}
		$response =  new TemplateResponse(
			'assembly',
			'content/videocall',
			[
				'time' => isset($row['meeting_time']) ? date('Y-m-d H:i:s', $row['meeting_time']) : null
			]
		);

		return $response;
	}
}
