<?php

namespace OCA\Assembly\Service;

use OCA\Assembly\Db\ReportMapper;

class ReportService
{
    /**
     * @var ReportMapper
     */
    protected $mapper;
    
    public function __construct(ReportMapper $mapper)
    {
        $this->mapper =  $mapper;
    }
    public function getResult($userId, $formId)
    {
        $this->mapper->getResult($userId, $formId);
    }

}