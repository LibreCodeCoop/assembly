<?php
namespace OCA\Assembly\Db;

use OCP\AppFramework\Db\QBMapper;

class ReportMapper extends QBMapper
{
    public function getResult($userId, $formId)
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select(['f.title','a."text" as response','count(*) as total'])
            ->from('forms_v2_forms', 'f')
            ->join('f', 'forms_v2_questions', 'q', 'f.id = q.form_id')
            ->join('f', 'forms_v2_submissions', 's', 's.form_id = f.id')
            ->leftJoin('q', 'forms_v2_answers', 'a', 'a.submission_id =s.id')
            ->join('f', 'group_user', 'gu', "((f.access_json->'groups')::jsonb ? gu.gid")
            ->where($qb->expr()->eq('f.id', ':formId'))
            ->andWhere($qb->expr()->eq('gu.uid', ':$userId'))
            ->groupBy('a."text", f.id');

        return $this->findEntities($qb, ['formId'=>$formId, 'userId'=>$userId]);
    }
}