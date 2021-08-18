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
            $tos_date = new \DateTime();
            $tos_date->createFromFormat('U', $row['tos_date']);
            $row['tos_date'] = $tos_date->format('Y-m-d H:i:s');

            $last_activity = new \DateTime();
            $last_activity->createFromFormat('U', $row['last_activity']);
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
            ->join('m', 'users', 'u', 'u.uid = m.created_by')
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
                $date = new \DateTime();
                $date->createFromFormat('U', $row['date']);
                $row['date'] = $date->format('Y-m-d H:i');
            }

            $row['deletedAt'] = $row['deleted_at'];
            if (!empty($row['deletedAt'])) {
                $date = new \DateTime();
                $date->createFromFormat('U', $row['deletedAt']);
                $row['deletedAt'] = $date->format('Y-m-d H:i');
            }
            unset($row['deleted_at']);

            $created_at = new \DateTime();
            $created_at->createFromFormat('U', $row['created_at']);
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
}
