<?php

namespace OCA\RocketchatNextcloud\Controller;

use OCA\RocketchatNextcloud\DB\Config;
use OCA\RocketchatNextcloud\Db\FileChat;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\IRequest;
use OCP\IServerContainer;
use Httpful\Request;
use Httpful\Mime;
use OCA\RocketchatNextcloud\Rocket\User;

class ConfigController extends Controller {
    protected $config;
    protected $appName;
    protected $server;
    protected $rocketApi;

    public function __construct($AppName, IRequest $request, IServerContainer $server, User $rocketApi)
    {
        parent::__construct($AppName, $request);

        $this->config = new Config();
        $this->server = $server;
        $this->appName = $AppName;
        $this->rocketApi = $rocketApi;
    }

    /**
     * Requires admin.
     *
     * @NoCSRFRequired
     * @param $url
     * @param $personalAccessToken
     * @param $userId
     * @return RedirectResponse
     */
    public function setupUrl()
    {
        if (isset($_POST['rcurl']) && isset($_POST['rcuser']) && isset($_POST['rcpassword'])) {
            $rcUrl = strval($_POST['rcurl']);
            $rcUser = strval($_POST['rcuser']);
            $rcPassword = strval($_POST['rcpassword']);

            $response = $this->rocketApi->loginAdmin($rcUrl, $rcUser, $rcPassword);
            return $response;
        }
    }

    /**
     * Requires admin.
     *
     * @NoCSRFRequired
     * @return RedirectResponse
     */
    public function resetConfig()
    {
        $this->config->resetAdminData();

        (new FileChat())->deleteAll();

        return new RedirectResponse(
            ($this->server->getURLGenerator())->linkToRoute('files.view.index')
        );
    }
}
