<?php
namespace OCA\Assembly\Controller;

use OCA\Assembly\Db\ReportMapper;
use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\IGroupManager;
use OCP\IUser;
use OCP\IUserSession;

class PageController extends Controller {
	private $userId;
	/** @var IGroupManager */
	protected $groupManager;

	/** @var IUserSession */
	protected $userSession;

	public function __construct($AppName, IRequest $request, $UserId, ReportMapper $ReportMapper, IGroupManager $groupManager, IUserSession $userSession){
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
		$this->groupManager = $groupManager;
		$this->ReportMapper =  $ReportMapper;
		$this->userSession = $userSession;
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
		return new TemplateResponse('assembly', 'content/index',
			[
				'data'=>$data,
				'group'=>$groups
			] );  // templates/report.php

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
	public function videocall($groupId) {
		$response =  new TemplateResponse('assembly', 'content/videocall',
			[
				'group'=>$groupId
			] );  // templates/report.php

			$policy = new ContentSecurityPolicy();
			$policy->addAllowedChildSrcDomain('*');
			$policy->addAllowedFrameDomain('*');
			$response->setContentSecurityPolicy($policy);

			return $response;

	}
}
