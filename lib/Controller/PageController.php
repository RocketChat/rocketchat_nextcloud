<?php

namespace OCA\RocketchatNextcloud\Controller;

use OCA\RocketchatNextcloud\AppInfo\Application;
use OCA\RocketchatNextcloud\Db\Config;
use OCA\RocketchatNextcloud\Rocket\User as RocketUser;
use OCP\IRequest;
use OCP\IServerContainer;
use OCP\IUserSession;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use Psr\Log\LoggerInterface;

class PageController extends Controller {
    protected $config;
    protected $appName;
    protected $server;

	/** @var LoggerInterface */
	private $logger;

    /** @var IUserSession */
    private $userSession;

    /** @var RocketUser */
    private $rocketUser;

	public function __construct($AppName,
                                IRequest $request, 
                                LoggerInterface $logger,
                                IServerContainer $server, 
                                IUserSession $userSession,
                                RocketUser $rocketUser) {
		parent::__construct($AppName, $request);
		$this->config = new Config();
		$this->server = $server;
		$this->appName = Application::APP_ID;
        $this->logger = $logger;
        $this->userSession = $userSession;
        $this->rocketUser = $rocketUser;
	}

	/**
	 * CAUTION: the @Stuff turns off security checks; for this page no admin is
	 *          required and no CSRF check. If you don't know what CSRF is, read
	 *          it up in the docs or you might create a security hole. This is
	 *          basically the only required method to add this exemption, don't
	 *          add it to any other method if you don't exactly know what it does
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index() {
	    $rocketChatUrl = $this->config->getUrl();
	    if ( ! $rocketChatUrl) {
            return new DataResponse(['message' => 'Not found!'], 404);
        }

        $userUID = $this->userSession->getUser()->getUID();
        $authToken = $this->rocketUser->findByNcUserId($userUID);

		$response = new TemplateResponse($this->appName, 'index', [
		    'url' => $rocketChatUrl,
            'token' => $authToken,
        ]);

        $policy = new ContentSecurityPolicy();
        $policy->addAllowedChildSrcDomain('*');
        $policy->addAllowedFrameDomain('*');

        $response->setContentSecurityPolicy($policy);

        return $response;
	}

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @param $chat
     * @param $new
     * @return DataResponse|TemplateResponse
     */
    public function file($chat, $new) {

        $rocketChatUrl = $this->config->getUrl();

        if ( ! $rocketChatUrl) {
            return new DataResponse(['message' => 'Not found!'], 404);
        }

        $response = new TemplateResponse($this->appName, 'index', [
            'url' => $rocketChatUrl . "/group/{$chat}?layout=embedded",
            'new' => $new,
        ]);

        $policy = new ContentSecurityPolicy();
        $policy->addAllowedChildSrcDomain('*');
        $policy->addAllowedFrameDomain('*');

        $response->setContentSecurityPolicy($policy);

        return $response;
    }
}
