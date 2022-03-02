<?php

declare(strict_types=1);

/*

 _____________________________________________________________
|   Rocket Chat NextCloud App                                 |
|   Authors: Ruvenss G. Wilches & Pierre Locus                |
|   Proudly working for Rocket.Chat Inc                       |
|   All licences and code belong to Rocket.Chat Inc           |
|   Live long and Prosper                                     | 
|_____________________________________________________________|                                                                                                                                                                             
*/

namespace OCA\RocketchatNextcloud\AppInfo;

use OCA\RocketchatNextcloud\Db\Config;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;

class Application extends App implements IBootstrap{
    public const APP_ID = 'rocketchat_nextcloud';

    public function __construct()
    {
        parent::__construct(self::APP_ID);
    }

    public function register(IRegistrationContext $context): void {
		if ( ! \OC::$server->getAppManager()->isEnabledForUser(self::APP_ID)) {
            return;
        }

        $config = new Config();

        if ($config->getUrl()) {
            $this->registerNavigationRoute();
            $this->registerScripts();
        }
	}

	public function boot(IBootContext $context): void {
		// TODO: the day we need boot
        return;
	}

    protected function registerNavigationRoute()
    {
        $server = $this->getContainer()->getServer();

        $server->getNavigationManager()->add(function() use ($server) {
            return [
                'id' => self::APP_ID,
                'order' => 1,
                'name' => 'Chat',
                'href' => $server->getURLGenerator()->linkToRouteAbsolute(self::APP_ID . '.page.index'),
                'icon' => $server->getURLGenerator()->imagePath(self::APP_ID, 'rocket-logo.png'),
//                'type' => $user instanceof IUser ? 'link' : 'hidden', // Here we can also check if the app is disabled for the auth user..
            ];
        });
    }

    protected function registerScripts()
    {
        $eventDispatcher = \OC::$server->getEventDispatcher();
        $eventDispatcher->addListener('OCA\Files::loadAdditionalScripts', function() {
            script(self::APP_ID, 'rocketchat_nextcloud');
//            style(self::name, 'styles');
        });
    }
}
