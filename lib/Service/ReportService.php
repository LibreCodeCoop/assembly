<?php

namespace OCA\Assembly\Service;

use OCA\Assembly\Db\ReportMapper;
use OCP\AppFramework\Services\IAppConfig;
use OCP\IDBConnection;
use OCP\IGroupManager;
use OCP\IUser;
use OCP\IUserSession;

class ReportService
{
    /**
     * @var ReportMapper
     */
    protected $mapper;
    /** @var IUserSession */
    protected $user;
    /** @var AppConfig */
    protected $appConfig;
    /** @var IGroupManager */
    protected $groupManager;
    /** @var IDBConnection */
    protected $db;
    /** @var IUserSession */
    protected $userSession;
    /** @var ReportMapper */
    protected $ReportMapper;

    public function __construct(
        ReportMapper $mapper,
        IUserSession $user,
        IAppConfig $appConfig,
        IGroupManager $groupManager,
        IDBConnection $db,
        IUserSession $userSession,
        ReportMapper $ReportMapper
    ) {
        $this->mapper = $mapper;
        $this->user = $user;
        $this->appConfig = $appConfig;
        $this->groupManager = $groupManager;
        $this->db = $db;
        $this->userSession = $userSession;
        $this->ReportMapper =  $ReportMapper;
    }
    public function getResult($userId, $formId)
    {
        $this->mapper->getResult($userId, $formId);
    }

    public function getDashboard()
    {
        $user = $this->userSession->getUser();
        if ($user instanceof IUser) {
            $return['groups'] = $this->groupManager->getUserGroupIds($this->userSession->getUser());
        }
        $return['data'] = $this->ReportMapper->getPoll($this->userId);
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
            if ($row['meeting_time'] < time()) {
                $return['meetUrl'] = $row['url'];
            } else {
                $return['time'] = isset($row['meeting_time']) ? date('Y-m-d H:i:s', $row['meeting_time']) : null;
            }
        } else {
            $return['meetUrl'] = 'https://meet.jit.si/' . date('Ymd') . $return['groups'][0];
        }
        return $return;
    }

}