<?php

namespace OCA\RocketIntegration\Rocket;

use Httpful\Request;
use OCA\RocketIntegration\Db\Config;

class Client
{
    protected $api;

    public function __construct()
    {
        $config = new Config();

        $userId = '';
        $authToken = '';

        foreach ($config->getAdminData() as $key => $setting) {
            if ($setting['configkey'] === 'user_id') {
                $userId = $setting['configvalue'];
            }

            if ($setting['configkey'] === 'personal_access_token') {
                $authToken = $setting['configvalue'];
            }
        }

        $this->api = $config->getUrl() . '/api/v1/';

        $tmp = Request::init()
            ->addHeaders([
                'X-Auth-Token' => $authToken,
                'X-User-Id' => $userId,
            ])
            ->sendsJson()
            ->expectsJson();

        Request::ini($tmp);
    }
}
