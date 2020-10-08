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
use \OCP\IRequest;
use \OCP\IUserSession;
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
     * @var IUserSession
     */
    private $userSession;
    /**
     * @var ReportMapper
     */
    private $ReportMapper;



    /**
     * ApiController constructor.
     *
     * Stores the user session to be able to leverage the user in further methods
     *
     * @param string        $appName        The name of the app
     * @param IRequest      $request        The request
     * @param IUserSession  $userSession    The user session
     */
    public function __construct($appName,
                            IRequest $request,
                            IUserSession $userSession,
                            ReportMapper $ReportMapper,
                            IDBConnection $db,
                            LoggerInterface $logger)
    {
        parent::__construct($appName, $request);
        $this->userSession = $userSession;
        $this->ReportMapper =  $ReportMapper;
        $this->db = $db;
        $this->logger = $logger;
    }

    /**
     * @return IUser
     */
    protected function getUser()
    {
        return $this->userSession->getUser();
    }

    /**
     * @return string
     */
    protected function getUserId()
    {
        return $this->getUser()->getUID();
    }

    /**
     * @PublicPage
     * @NoCSRFRequired
     * @CORS
     *
     * @return array
     */
    public function report($formId)
    {
        return $this->ReportMapper->getResult($this->getUserId(), $formId);
    }

    /**
     * @PublicPage
     * @NoCSRFRequired
     * @CORS
     *
     * @return array
     */
    public function usersAvailable($groupId)
    {
        return $this->ReportMapper->usersAvailable($groupId);
    }

    /**
     * @PublicPage
     * @NoCSRFRequired
     * @CORS
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
}
