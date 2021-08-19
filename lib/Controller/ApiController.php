<?php
/**
 * Nextcloud - News
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author    Alessandro Cosentino <cosenal@gmail.com>
 * @author    Bernhard Posselt <dev@bernhard-posselt.com>
 * @author    David Guillot <david@guillot.me>
 * @copyright 2012 Alessandro Cosentino
 * @copyright 2012-2014 Bernhard Posselt
 * @copyright 2018 David Guillot
 */

namespace OCA\Assembly\Controller;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use OCA\Assembly\Db\ReportMapper;
use OCA\Assembly\Service\ReportService;
use \OCP\IRequest;
use \OCP\AppFramework\ApiController as BaseApiController;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\IDBConnection;
use Psr\Log\LoggerInterface;

/**
 * Class ApiController
 *
 * @package OCA\News\Controller
 */
class ApiController extends BaseApiController
{
    /** @var IDBConnection */
    protected $db;
    /** @var LoggerInterface */
    protected $logger;
    /**
     * @var ReportMapper
     */
    private $ReportMapper;
    /** @var ReportService */
    private $ReportService;

    public function __construct($appName,
                            IRequest $request,
                            ReportMapper $ReportMapper,
                            ReportService $ReportService,
                            IDBConnection $db,
                            LoggerInterface $logger)
    {
        parent::__construct($appName, $request);
        $this->ReportMapper =  $ReportMapper;
        $this->ReportService =  $ReportService;
        $this->db = $db;
        $this->logger = $logger;
    }

    /**
     * @NoCSRFRequired
     * @NoAdminRequired
     *
     * @return array
     */
    public function usersAvailable($groupId)
    {
        return $this->ReportMapper->usersAvailable($groupId);
    }

    /**
     * @NoCSRFRequired
     * @NoAdminRequired
     *
     * @return array
     */
    public function meetWebhook()
    {
        $input = $this->request->post;
        if (!empty($input['SubscribeURL'])) {
            file_get_contents($input['SubscribeURL']);
            return new DataResponse([], Http::STATUS_CREATED);
        }
        if (count($input) <=2) {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!count($input)) {
                return new DataResponse(array('msg' => 'Invalid JSON'), Http::STATUS_NOT_FOUND);
            }
        }
        if (empty($input['meeting'])) {
            $this->logger->error('Empty meeting id: ' . file_get_contents('php://input'), ['extra_context' => 'Validate SNS']);
            return new DataResponse(array('msg' => 'Empty meeting id'), Http::STATUS_NOT_FOUND);
        }
        $query = $this->db->getQueryBuilder();
        $query->select(['meeting_id'])->from('assembly_meetings')
                ->where($query->expr()->eq('meeting_id', $query->createNamedParameter($input['meeting'])));
        $stmt = $query->execute();
        $exist = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$exist) {
            $this->logger->error('Invalid meeting id: '. file_get_contents('php://input'), ['extra_context' => 'Invalid meeting id']);
            return new DataResponse(array('msg' => 'Invalid meeting id'), Http::STATUS_NOT_FOUND);
        }
        $insert = $this->db->getQueryBuilder();
        try {
            $insert->insert('assembly_participants')
                ->values([
                    'meeting_id' => $insert->createNamedParameter($input['meeting']),
                    'url' => $insert->createNamedParameter($input['url']),
                    'uid' => $insert->createNamedParameter($input['attendee']),
                    'password' => $insert->createNamedParameter($input['password'] ?? null),
                    'created_at' => $insert->createNamedParameter(time())
                ])
                ->execute();
        } catch (UniqueConstraintViolationException $e) {
            $this->logger->error('Query error: ' . $e->getMessage(), ['extra_context' => 'Validate SNS']);
            return new DataResponse(array('msg' => 'Participant already registered.'), Http::STATUS_FORBIDDEN);
        }
        return new DataResponse(array('msg' => 'Success'), Http::STATUS_CREATED);
    }

    /**
     * @NoCSRFRequired
     * @NoAdminRequired
     *
     * @return array
     */
    public function dashboard()
    {
        $return = $this->ReportService->getDashboard();
        return new DataResponse($return, Http::STATUS_OK);
    }

    /**
     * @NoCSRFRequired
     * @NoAdminRequired
     *
     * @return array
     */
    public function getMeetings()
    {
        $return = $this->ReportService->getMeetings();
        return new DataResponse($return, Http::STATUS_OK);
    }

    /**
     * @NoCSRFRequired
     * @NoAdminRequired
     *
     * @return array
     */
    public function getPools($meetId)
    {
        $return = $this->ReportService->getPools($meetId);
        return new DataResponse($return, Http::STATUS_OK);
    }

    /**
     * @NoCSRFRequired
     * @SubAdminRequired
     *
     * @return array
     */
    public function getTos($groupId)
    {
        $return = $this->ReportService->getTos($groupId);
        return new DataResponse($return, Http::STATUS_OK);
    }

    /**
     * @NoCSRFRequired
     * @SubAdminRequired
     *
     * @return array
     */
    public function getVotes($meetId)
    {
        $return = $this->ReportService->getVotes($meetId);
        return new DataResponse($return, Http::STATUS_OK);
    }

    /**
     * @NoCSRFRequired
     * @SubAdminRequired
     *
     * @return array
     */
    public function getAttendances($meetId)
    {
        $return = $this->ReportService->getAttendances($meetId);
        return new DataResponse($return, Http::STATUS_OK);
    }

    /**
     * @NoCSRFRequired
     * @SubAdminRequired
     *
     * @return array
     */
    public function getTotalVotes($meetId)
    {
        $return = $this->ReportService->getTotalVotes($meetId);
        return new DataResponse($return, Http::STATUS_OK);
    }
}
