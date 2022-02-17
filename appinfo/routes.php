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
return [
    'routes' => [
	   ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
	   ['name' => 'page#file', 'url' => '/file-chat', 'verb' => 'GET'],
	   ['name' => 'config#setupUrl', 'url' => '/setup-url', 'verb' => 'POST'],
	   ['name' => 'settings#resetConfig', 'url' => '/reset-config', 'verb' => 'POST'],
	   ['name' => 'file#store', 'url' => '/file', 'verb' => 'POST'],
       ['name' => 'settings#setAdminConfig', 'url' => '/admin-config', 'verb' => 'PUT'],
       ['name' => 'settings#getWidgetContent', 'url' => '/widget-content', 'verb' => 'GET'],
    ]
];
