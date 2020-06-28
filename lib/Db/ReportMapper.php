<?php
namespace OCA\Assembly\Db;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;

class ReportMapper extends QBMapper
{
    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'forms_v2_forms');
    }

    public function getResult($userId, $formId)
    {
        $eb = new ExpressionBuilder($this->db);
        $qb = $this->db->getQueryBuilder();
        return $qb->select('f.title')
            ->selectAlias('a.text', 'response')
            ->selectAlias($qb->func()->count('*'), 'total')
            ->from('forms_v2_forms', 'f')
            ->join('f', 'forms_v2_questions', 'q', 'f.id = q.form_id')
            ->join('f', 'forms_v2_submissions', 's', 's.form_id = f.id')
            ->leftJoin('q', 'forms_v2_answers', 'a', 'a.submission_id =s.id')
            ->where('f.id = :formId')
            ->andWhere(
                $eb->in(
                    'f.id',
                    $this->db->getQueryBuilder()
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
            ->groupBy('a.text', 'f.id')
            ->setParameter('formId', $formId??1, ParameterType::INTEGER)
            ->setParameter('userId', $userId??1, ParameterType::INTEGER)
            ->execute()
            ->fetchAll();
    }
}