<?php
/*
 _____________________________________________________________
|   Rocket Chat NextCloud App                                 |
|   Authors: Ruvenss G. Wilches & Pierre Locus                |
|   Proudly working for Rocket.Chat Inc                       |
|   All licences and code belong to Rocket.Chat Inc           |
|   Live long and Prosper                                     |
|_____________________________________________________________|
*/
namespace OCA\RocketIntegration\AppInfo;
use OCA\RocketIntegration\Db\Config;
use OCP\AppFramework\App;

class Application extends App {
    const APP_ID = 'rocket_integration';

    public function __construct()
    {
        parent::__construct(self::APP_ID);
    }

    public function register()
    {
        if ( ! \OC::$server->getAppManager()->isEnabledForUser(self::APP_ID)) {
            return;
        }

        $config = new Config();

        if ($config->getUrl()) {
            $this->registerNavigationRoute();
            $this->registerScripts();
        }
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
            script(self::APP_ID, 'rocket_integration');
//            style(self::name, 'styles');
        });
    }
}

