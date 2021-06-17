<?php

namespace OCA\Assembly\Service;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use OCA\Assembly\Db\ReportMapper;
use OCP\AppFramework\Services\IAppConfig;
use OCP\IDBConnection;
use OCP\IGroupManager;
use OCP\IURLGenerator;
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
    /** @var IURLGenerator */
    protected $urlGenerator;

    public function __construct(
        ReportMapper $mapper,
        IUserSession $user,
        IAppConfig $appConfig,
        IGroupManager $groupManager,
        IDBConnection $db,
        IUserSession $userSession,
        ReportMapper $ReportMapper,
        IURLGenerator $urlGenerator
    ) {
        $this->mapper = $mapper;
        $this->user = $user;
        $this->appConfig = $appConfig;
        $this->groupManager = $groupManager;
        $this->db = $db;
        $this->userSession = $userSession;
        $this->ReportMapper =  $ReportMapper;
        $this->urlGenerator = $urlGenerator;
    }
    public function getResult($userId, $formId)
    {
        $this->mapper->getResult($userId, $formId);
    }

    public function getDashboard()
    {
        $user = $this->userSession->getUser();
        if ($user instanceof IUser) {
            $groups = $this->groupManager->getUserGroupIds($this->userSession->getUser());
        }
        $return['data'] = $this->ReportMapper->getPoll($this->userSession->getUser()->getUID());
        foreach ($return['data'] as $key => $item) {
            $return['data'][$key]['vote_url'] = $this->urlGenerator->linkToRoute(
                'forms.page.goto_form',
                ['hash' => $item['hash']]
            );
            $return['data'][$key]['result_url'] = $this->urlGenerator->linkToRoute(
                'assembly.page.report',
                [
                    'formId' => $item['formId'],
                    'groupId' => $item['groupId']
                ]
            );
            $return['data'][$key]['result_api_url'] = $this->urlGenerator->linkToRoute(
                'assembly.api.report',
                [
                    'formId' => $item['formId'],
                    'groupId' => $item['groupId']
                ]
            );
            unset($return['data'][$key]['hash']);
            unset($return['data'][$key]['formId']);
            unset($return['data'][$key]['groupId']);
        }
        if ($this->appConfig->getAppValue('enable_mutesi')) {
            $query = $this->db->getQueryBuilder();
            $query->select(['url', 'meeting_time'])->from('assembly_participants', 'ap')
                ->join('ap', 'assembly_meetings', 'am', 'am.meeting_id = ap.meeting_id')
                ->where($query->expr()->eq('ap.uid', $query->createNamedParameter($this->userSession->getUser()->getUID())))
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
        } else if ($this->appConfig->getAppValue('enable_jitsi_jwt')) {
            $config = Configuration::forSymmetricSigner(
                new Sha256(),
                InMemory::plainText($this->appConfig->getAppValue('jitsi_secret'))
            );

            $user = $this->userSession->getUser();
            $token = $config->Builder()
                ->permittedFor($this->appConfig->getAppValue('jitsi_appid')) //aud
                ->issuedBy($this->appConfig->getAppValue('jitsi_appid')) // iss
                ->relatedTo(parse_url($this->appConfig->getAppValue('jitsi_url'))['host']) // sub
                ->withClaim('room', date('Ymd') . $groups[0]) // room
                ->withClaim('moderator', in_array('admin', $groups)) // moderator
                ->withClaim('context', [
                    'user' => [
                        'name' => $user->getDisplayName(),
                        'email' => $user->getEMailAddress()
                      ]
                ]) // room
                ->getToken($config->signer(), $config->signingKey());
            $return['meetUrl'] = $this->appConfig->getAppValue('jitsi_url') .
            '/' . date('Ymd') . $groups[0].
                '?jwt=' . $token->toString();
        } else {
            $return['meetUrl'] = 'https://meet.jit.si/' . date('Ymd') . $groups[0];
        }
        return $return;
    }

    public function getReport($formId, $groupId)
    {

        $data = $this->ReportMapper->getResult($this->userSession->getUser()->getUID(), $formId);
        $available = $this->ReportMapper->usersAvailable($groupId);
        $responses = [];
        $metadata['total'] = 0;
        $metadata['available'] = count($available);
        foreach ($data as $row) {
            $responses[] = [
                'text' => $row['response'],
                'total' => $row['total']
            ];
            $metadata['total']+=$row['total'];
        }
        if($data){
            $metadata['title'] = $data[0]['title'];
        }
        return [
            'responses' => $responses,
            'metadata' => $metadata
        ];
    }

}