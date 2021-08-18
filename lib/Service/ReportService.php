<?php

namespace OCA\Assembly\Service;

use DateTime;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use OCA\Assembly\Db\ReportMapper;
use OCA\Forms\Db\Answer;
use OCA\Forms\Db\AnswerMapper;
use OCA\Forms\Db\FormMapper;
use OCA\Forms\Db\OptionMapper;
use OCA\Forms\Db\QuestionMapper;
use OCA\Forms\Db\SubmissionMapper;
use OCA\Forms\Service\FormsService;
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
    /** @var FormMapper */
    protected $formMapper;
    /** @var SubmissionMapper */
    protected $submissionMapper;
    /** @var FormsService */
    protected $formsService;
    /** @var AnswerMapper */
    protected $answerMapper;
    /** @var QuestionMapper */
    protected $questionMapper;
    /** @var OptionMapper */
    protected $optionMapper;
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
        FormMapper $formMapper,
        SubmissionMapper $submissionMapper,
        FormsService $formsService,
        AnswerMapper $answerMapper,
        QuestionMapper $questionMapper,
        OptionMapper $optionMapper,
        IURLGenerator $urlGenerator
    ) {
        $this->mapper = $mapper;
        $this->user = $user;
        $this->appConfig = $appConfig;
        $this->groupManager = $groupManager;
        $this->db = $db;
        $this->userSession = $userSession;
        $this->ReportMapper = $ReportMapper;
        $this->formMapper = $formMapper;
        $this->submissionMapper = $submissionMapper;
        $this->formsService = $formsService;
        $this->answerMapper = $answerMapper;
        $this->questionMapper = $questionMapper;
        $this->optionMapper = $optionMapper;
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
            $groups = $this->groupManager->getUserGroupIds($user);
        }
        $return['data'] = $this->ReportMapper->getPool($user->getUID());
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
                ->where($query->expr()->eq('ap.uid', $query->createNamedParameter($user->getUID())))
                ->andWhere($query->expr()->gt('am.meeting_time', $query->createNamedParameter(
                    time()-(60*60*24)
                )))
                ->orderBy('ap.created_at', 'ASC');
            $stmt = $query->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (isset($row['meeting_time']) && $row['meeting_time'] < time()) {
                $return['meetUrl'] = $row['url'];
            } else {
                $return['time'] = isset($row['meeting_time']) ? date('Y-m-d H:i:s', $row['meeting_time']) : null;
            }
        } else if ($this->appConfig->getAppValue('enable_jitsi_jwt')) {
            $return['meetUrl'] = $this->generateJitsiUrl($user, date('Ymd') . $groups[0], $groups);
        } else {
            $return['meetUrl'] = 'https://meet.jit.si/' . date('Ymd') . $groups[0];
        }
        return $return;
    }

    private function generateJitsiUrl($user, $slug, $groups)
    {
        $config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($this->appConfig->getAppValue('jitsi_secret'))
        );

        $token = $config->Builder()
            ->permittedFor($this->appConfig->getAppValue('jitsi_appid')) //aud
            ->issuedBy($this->appConfig->getAppValue('jitsi_appid')) // iss
            ->relatedTo(parse_url($this->appConfig->getAppValue('jitsi_url'))['host']) // sub
            ->withClaim('room', $slug) // room
            ->withClaim('moderator', in_array('admin', $groups)) // moderator
            ->withClaim('context', [
                'user' => [
                    'name' => $user->getDisplayName(),
                    'email' => $user->getEMailAddress()
                  ]
            ]) // room
            ->getToken($config->signer(), $config->signingKey());
        return $this->appConfig->getAppValue('jitsi_url') .
            '/' . $slug.
            '?jwt=' . $token->toString();
    }

    public function getReportUsingGroupId($formId, $groupId)
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

    public function getMeetings()
    {
        $user = $this->userSession->getUser();
        if ($user instanceof IUser) {
            $groups = $this->groupManager->getUserGroupIds($user);
        }
        $jitsiEnabled = $this->appConfig->getAppValue('enable_jitsi_jwt', false);
        $data = $this->ReportMapper->getMeetings($this->userSession->getUser()->getUID());
        $currentDate = (new DateTime())->format('Y-m-d H:i');
        foreach ($data as $id => $row) {
            if ($jitsiEnabled && $row['date'] <= $currentDate) {
                $row['url'] = $this->generateJitsiUrl($user, $row['slug'], $groups);
            }
            $data[$id] = $row;
        }
        return $data;
    }

    public function getPools($meetId)
    {
        $forms = $this->formMapper->findAll();
        $user = $this->userSession->getUser();
        $meeting = $this->ReportMapper->getMeetings($user->getUID(), $meetId);
        if (empty($meeting)) {
            return [];
        }
        $meeting = $meeting[0];

        $return = [];
        foreach ($forms as $key => $form) {
            $access = $form->getAccess();
            $inGroup = false;
            foreach ($access['groups'] as $group) {
                if ($this->groupManager->isInGroup($user->getUID(), $group)) {
                    $inGroup = true;
                }
            }
            if (!$inGroup) {
                continue;
            }

            $data = [
                'title' => $form->getTitle(),
                'description' => $form->getDescription(),
                'formId' => $form->getId(),
                'finishedAt' => null,
                'status' => 'enabled',
                'voted' => false
            ];

            if ($form->getExpires() > 0) {
                $date = new \DateTime();
                $date->createFromFormat('U', $form->getExpires());
                $data['finishedAt'] = $date->format('Y-m-d H:i:s');
                $data['status'] = 'disabled';
            }

            $submissions = $this->submissionMapper->findByForm($form->getId());
            $questions = [];
            foreach ($submissions as $submission) {
                if ($submission->getUserId() === $user->getUID()) {
                    $data['voted'] = true;
                }
                $answers = array_reduce($this->answerMapper->findBySubmission($submission->getId()), function (array $carry, Answer $answer) {
                    $questionId = $answer->getQuestionId();
    
                    // If key exists, insert separator
                    if (key_exists($questionId, $carry)) {
                        $carry[$questionId] .= '; ' . $answer->getText();
                    } else {
                        $carry[$questionId] = $answer->getText();
                    }
    
                    return $carry;
                }, []);
                foreach ($answers as $questionId => $text) {
                    if (!isset($questions[$questionId])) {
                        $questions[$questionId]['question'] = $this->questionMapper->findById($questionId);
                        $questions[$questionId]['options'] = $this->optionMapper->findByQuestion($questionId);
                    }
                    $data['questions'][$questionId]['text'] = $questions[$questionId]['question']->getText();
                    foreach ($questions[$questionId]['options'] as $option) {
                        if (!isset($data['questions'][$questionId]['options'][$option->getId()]['total'])) {
                            $data['questions'][$questionId]['options'][$option->getId()]['total'] = 0;
                            $data['questions'][$questionId]['options'][$option->getId()]['text'] = $option->getText();
                        }
                        if ($option->getText() === $text) {
                            $data['questions'][$questionId]['options'][$option->getId()]['total']++;
                        }
                    }
                }
            }
            $return[] = $data;
        }
        return $return;
    }

}
