<?php
namespace OCA\Assembly\Controller;

use OCA\Assembly\Db\ReportMapper;
use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;

class PageController extends Controller {
	private $userId;

	public function __construct($AppName, IRequest $request, $UserId, ReportMapper $ReportMapper){
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
		$this->ReportMapper =  $ReportMapper;
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
		return new TemplateResponse('assembly', 'index');  // templates/index.php
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */	
	public function report() {
		$data = $this->ReportMapper->getResult($this->userId, 1);
		$available = $this->ReportMapper->usersAvailable();
		$responses = [];
		foreach ($data as $row) {
			$responses[$row['response']] = $row['total'];
		}
		if($data){
			$metadata['title'] = $data[0]['title'];
			$metadata['total'] = count($data);
			$metadata['available'] = count($available);
		}else{
			$metadata['total'] = 0;
			$metadata['available'] = 0;
		}
		return new TemplateResponse('assembly', 'content/report', 
			[
				'responses'=>$responses,
				'metadata'=>$metadata
			] );  // templates/report.php
	}	

}
