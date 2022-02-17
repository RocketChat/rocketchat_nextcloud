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
if ((@include_once __DIR__ . '/../vendor/autoload.php')===false) {
    throw new Exception('Cannot include autoload. Did you run install dependencies using composer?');
}

$app = \OC::$server->query(\OCA\RocketIntegration\AppInfo\Application::class);
$app->register();
