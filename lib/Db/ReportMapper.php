<?php
namespace OCA\Assembly\Db;

use Doctrine\DBAL\ParameterType;
use OC\DB\QueryBuilder\Literal;
use OC\DB\QueryBuilder\QueryFunction;
use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;

class ReportMapper extends QBMapper
{
    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'forms_v2_forms');
    }

    public function getResult($userId, $formId)
    {
        $qb = $this->db->getQueryBuilder();
        return $qb->select('f.title')
            ->selectAlias('a.text', 'response')
            ->selectAlias($qb->func()->count('*'), 'total')
            ->from('forms_v2_forms', 'f')
            ->join('f', 'forms_v2_questions', 'q', 'f.id = q.form_id AND q.order > 0')
            ->join('f', 'forms_v2_submissions', 's', 's.form_id = f.id')
            ->leftJoin('q', 'forms_v2_answers', 'a', 'a.submission_id =s.id')
            ->where('f.id = :formId')
            ->andWhere(
                $qb->expr()->in(
                    'f.id',
                    new QueryFunction($this->db->getQueryBuilder()
                        ->select('s_f.id')
                        ->from('forms_v2_forms', 's_f')
                        ->leftJoin('s_f', 'group_user', 's_gu', "jsonb_exists((s_f.access_json->'groups')::jsonb, s_gu.gid)")
                        ->join('s_f', 'forms_v2_submissions', 's_s', 's_s.form_id = s_f.id')
                        ->leftJoin('s_s', 'forms_v2_answers', 's_a', 's_a.submission_id = s_s.id')
                        ->where('s_f.owner_id = :userId or s_a.id is not null or s_gu.uid is not null or s_s.user_id = :userId')
                        ->groupBy('s_f.id')
                        ->getSQL()
                    )
                )
            )
            ->groupBy('a.text', 'f.id')
            ->setParameter('formId', $formId, ParameterType::INTEGER)
            ->setParameter('userId', $userId, ParameterType::INTEGER)
            ->execute()
            ->fetchAll();
    }
    

    public function usersAvailable($groupId = null)
    {
        $qb = $this->db->getQueryBuilder();
        $query = $qb->select('u.displayname')
            ->select('u.uid')
            ->selectAlias('a.data', 'data')
            ->selectAlias('ts.timestamp', 'tos_date')
            ->selectAlias('at.last_activity', 'last_activity')
            ->from('users', 'u')
            ->join('u', 'accounts', 'a', 'a.uid = u.uid')
            ->join('u', 'termsofservice_sigs', 'ts', 'u.uid = ts.user_id')
            ->join('u', 'group_user', 'gu', 'gu.uid = u.uid')
            ->join(
                'u',
                new QueryFunction('('.$this->db->getQueryBuilder()
                    ->select('uid')
                    ->selectAlias($qb->func()->max('last_activity'), 'last_activity')
                    ->from('authtoken', 'sat')
                    ->groupBy('sat.uid')
                    ->getSQL().')'),
                'at',
                'at.uid = u.uid'
            );
        if ($groupId) {
            $query->where('gu.gid = :groupId')
                ->setParameter('groupId', $groupId);
        }
        $stmt = $query->execute();
        $return = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $tos_date = \DateTime::createFromFormat('U', $row['tos_date']);
            $row['tos_date'] = $tos_date->format('Y-m-d H:i:s');

            $last_activity = \DateTime::createFromFormat('U', $row['last_activity']);
            $row['last_activity'] = $last_activity->format('Y-m-d H:i:s');

            $user = json_decode($row['data']);
            unset($row['data']);
            $row['email'] = $user->email->value;
            $return[] = $row;
        }
        return $return;
    }

    public function getPool($userId)
    {
            $qb = $this->db->getQueryBuilder();
            $query = $qb->select('f.title')
                ->selectAlias('f.hash', 'hash')
                ->selectAlias('f.id', 'formId')
                ->selectAlias('g.gid', 'groupId')
                ->from('users', 'u')
                ->join('u', 'group_user', 'g', 'g.uid = u.uid')
                ->join('g', 'forms_v2_forms', 'f', "jsonb_exists((f.access_json->'groups')::jsonb, g.gid)")
                ->where('u.uid = :userId')
                ->andWhere('expires = 0  or expires > extract(epoch from now())')
                ->setParameter('userId', $userId);
            return $query
                ->execute()
                ->fetchAll();
    }

    public function getMeetings($userId, $meetId = null)
    {
        $qb = $this->db->getQueryBuilder();

        $query = $qb->select('m.meeting_id')
            ->selectAlias('m.meeting_time', 'date')
            ->addSelect('m.created_at')
            ->addSelect('m.created_by')
            ->addSelect('m.slug')
            ->addSelect('u.displayname')
            ->selectAlias('a.data', 'user_data')
            ->addSelect('m.description')
            ->addSelect('p.url')
            ->addSelect('m.deleted_at')
            ->addSelect('m.status')
            ->from('assembly_meetings', 'm')
            ->join('m', 'assembly_participants', 'p', 'm.meeting_id = p.meeting_id')
            ->join('p', 'users', 'u', 'u.uid = p.uid')
            ->join('m', 'accounts', 'a', 'a.uid = m.created_by')
            ->orderBy('m.meeting_time', 'DESC');
        if ($userId) {
            $query
                ->andWhere('u.uid = :userId')
                ->setParameter('userId', $userId);
        }
        if ($meetId) {
            $query
                ->andWhere('m.meeting_id = :meetId')
                ->setParameter('meetId', $meetId);
        }
        $stmt = $query->execute();
        $return = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            if (!empty($row['date'])) {
                $date = \DateTime::createFromFormat('U', $row['date']);
                $row['date'] = $date->format('Y-m-d H:i');
            }

            $row['deletedAt'] = $row['deleted_at'];
            if (!empty($row['deletedAt'])) {
                $date = \DateTime::createFromFormat('U', $row['deletedAt']);
                $row['deletedAt'] = $date->format('Y-m-d H:i');
            }
            unset($row['deleted_at']);

            $created_at = \DateTime::createFromFormat('U', $row['created_at']);
            $row['createdAt'] = $created_at->format('Y-m-d H:i');
            unset($row['created_at']);

            $user = json_decode($row['user_data']);
            unset($row['user_data']);
            $row['createdBy'] = [
                'displayName' => $user->displayname->value,
                'email' => $user->email->value,
                'userId' => $row['created_by']
            ];
            unset($row['user_data'], $row['displayname'], $row['created_by']);

            $row['meetingId'] = $row['meeting_id'];
            unset($row['meeting_id']);

            if ($row['deleted_at']) {
                $row['status'] = 'cancelled';
            } elseif (empty($row['status'])) {
                $row['status'] = 'waiting';
            }

            $return[] = $row;
        }
        return $return;
    }

    public function getPools($meetId, $userId)
    {
        $groups = $this->getGroupsOfUser($userId);

        $forms = $this->formMapper->findAll();

        $qb = $this->db->getQueryBuilder();
        $query = $qb->select('f.title')
            ->selectAlias('f.hash', 'hash')
            ->selectAlias('f.id', 'formId')
            ->selectAlias('g.gid', 'groupId')
            ->from('users', 'u')
            ->join('u', 'group_user', 'g', 'g.uid = u.uid')
            ->join('g', 'forms_v2_forms', 'f', "jsonb_exists((f.access_json->'groups')::jsonb, g.gid)")
            ->where('u.uid = :userId')
            ->andWhere('expires = 0  or expires > extract(epoch from now())')
            ->setParameter('userId', $userId);
        return $query
            ->execute()
            ->fetchAll();
        $qb = $this->db->getQueryBuilder();
        return;
    }

    private function getGroupsOfUser($userId): array
    {
        $qb = $this->db->getQueryBuilder();
        $stmt = $qb->selectAlias('g.gid', 'groupId')
            ->from('users', 'u')
            ->join('u', 'group_user', 'g', 'g.uid = u.uid')
            ->where('u.uid = :userId')
            ->setParameter('userId', $userId)
            ->execute();
        $groups = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $groups[] = $row['groupId'];
        }
        return $groups;
    }

    public function getTos($groupId)
    {
        $qb = $this->db->getQueryBuilder();
        $query = $qb
            ->addSelect('a.data')
            ->selectAlias('ts.timestamp', 'tos_date')
            ->from('users', 'u')
            ->join('u', 'accounts', 'a', 'a.uid = u.uid')
            ->leftJoin('u', 'termsofservice_sigs', 'ts', 'u.uid = ts.user_id')
            ->join('u', 'group_user', 'gu', 'gu.uid = u.uid')
            ->where('gu.gid = :groupId')
            ->setParameter('groupId', $groupId)
            ->orderBy('tos_date');

        $stmt = $query->execute();
        $return = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $user = json_decode($row['data']);
            $row['email'] = $user->email->value;
            unset($row['data']);

            $tos_date = \DateTime::createFromFormat('U', $row['tos_date']);
            $row['tosDate'] = $tos_date->format('Y-m-d H:i:s');
            unset($row['tos_date']);

            $return[] = $row;
        }
        return $return;
    }

    public function getVotes($meetId)
    {
        $qb = $this->db->getQueryBuilder();
        $query = $qb
            ->selectAlias('forms.title', 'question')
            ->selectAlias('submissions.timestamp', 'date')
            ->selectAlias('submissions.user_id', 'user')
            ->from('forms_v2_answers', 'answers')
            ->join('answers', 'forms_v2_questions', 'questions', 'questions.id = answers.question_id')
            ->join('answers', 'forms_v2_submissions', 'submissions', 'submissions.id = answers.submission_id')
            ->join('questions', 'forms_v2_forms', 'forms', 'forms.id = questions.form_id')
            ->join('submissions', 'group_user', 'gu', 'gu.uid = submissions.user_id')
            ->join('forms', 'assembly_meeting_pools', 'mp', 'forms.id = mp.form_id')
            ->join('mp', 'assembly_meetings', 'm', 'm.meeting_id = mp.meeting_id')
            ->where('m.slug = :meetId')
            ->setParameter('meetId', $meetId)
            ->groupBY(['forms.title', 'submissions.timestamp', 'submissions.user_id'])
            ->addOrderBy('date')
            ->addOrderBy('question');
        $stmt = $query->execute();
        $return = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $date = \DateTime::createFromFormat('U', $row['date']);
            $row['date'] = $date->format('Y-m-d H:i:s');

            $return[] = $row;
        }
        return $return;
    }

    public function getAttendances($meetId)
    {
        $qb = $this->db->getQueryBuilder();
        $query = $qb
            ->addSelect('a.data')
            ->addSelect('u.displayname')
            ->from('users', 'u')
            ->join('u', 'accounts', 'a', 'u.uid = a.uid')
            ->join('u', 'authtoken', 'at', 'u.uid = at.uid')
            ->join('u', 'group_user', 'gu', 'u.uid = gu.uid')
            ->join('u', 'assembly_participants', 'p', 'u.uid = p.uid')
            ->join('p', 'assembly_meetings', 'm', 'm.meeting_id = p.meeting_id')
            ->where('m.slug = :meetId')
            ->setParameter('meetId', $meetId)
            ->groupBy(['a.data', 'u.displayname'])
            ->addOrderBy('u.displayname');

        $stmt = $query->execute();
        $return = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $user = json_decode($row['data']);
            $row['email'] = $user->email->value;
            unset($row['data']);

            $return[] = $row;
        }
        return $return;
    }
}
