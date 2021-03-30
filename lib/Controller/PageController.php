<?php
namespace OCA\Assembly\Controller;

use OCA\Assembly\Db\ReportMapper;
use OCA\Assembly\Service\ReportService;
use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Controller;
use OCP\IDBConnection;

class PageController extends Controller {
    /** @var IDBConnection */
	protected $db;
	/** @var ReportService */
	protected $ReportService;

	private $userId;



	public function __construct(string $AppName,
								IRequest $request,
								string $UserId,
								ReportMapper $ReportMapper,
								ReportService $ReportService,
								IDBConnection $db) {
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
		$this->ReportMapper =  $ReportMapper;
		$this->ReportService = $ReportService;
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
		$return = $this->ReportService->getDashboard();
		return new TemplateResponse('assembly', 'content/index', $return);

	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */	
	public function report($formId, $groupId) {
		$return = $this->ReportService->getReport($formId, $groupId);
		return new TemplateResponse('assembly', 'content/report', $return);
	}	

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function videocall($meetingId) {
		$query = $this->db->getQueryBuilder();
		$query->select(['url', 'meeting_time'])->from('assembly_participants', 'ap')
			->join('ap', 'assembly_meetings', 'am', 'am.meeting_id = ap.meeting_id')
			->where($query->expr()->gt('am.meeting_time', $query->createNamedParameter(
				time()-(60*60*24)
			)));
		if ($meetingId != 'wait') {
			$query->andWhere($query->expr()->eq('am.meeting_id', $query->createNamedParameter($meetingId)));
		}
		$query->andWhere($query->expr()->eq('ap.uid', $query->createNamedParameter($this->userId)));
		$query->orderBy('ap.created_at', 'ASC');
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
