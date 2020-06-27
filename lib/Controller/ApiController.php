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

use OCA\Assembly\Service\ReportService;
use \OCP\IRequest;
use \OCP\IUserSession;
use \OCP\AppFramework\ApiController as BaseApiController;

/**
 * Class ApiController
 *
 * @package OCA\News\Controller
 */
class ApiController extends BaseApiController
{
    /**
     * @var IUserSession
     */
    private $userSession;
    /**
     * @var ReportService
     */
    private $reportService;



    /**
     * ApiController constructor.
     *
     * Stores the user session to be able to leverage the user in further methods
     *
     * @param string        $appName        The name of the app
     * @param IRequest      $request        The request
     * @param IUserSession  $userSession    The user session
     */
    public function __construct($appName, IRequest $request, IUserSession $userSession, ReportService $reportService)
    {
        parent::__construct($appName, $request);
        $this->userSession = $userSession;
        $this->reportService =  $reportService;
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
     * Indication of the API levels
     *
     * @PublicPage
     * @NoCSRFRequired
     * @CORS
     *
     * @return array
     */
    public function report()
    {
        $this->reportService->getResult($this->getUserId(), $formId);
        return "xxx";
    }
}
