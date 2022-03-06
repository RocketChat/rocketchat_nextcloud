<?php

namespace OCA\RocketchatNextcloud\Rocket;

use Httpful\Request;
use Httpful\Mime;
use OCA\RocketchatNextcloud\Db\Config;

class Client
{
    protected $api;

    public function __construct()
    {
        $config = new Config();
        $this->api = $config->getUrl() . '/api/v1/';
    }

    public function getUserIdAuthToken()
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
        return [
            'userId' => $userId,
            'authToken' => $authToken
        ];
    }

    public function rcPost($endpoint, $payload)
    {
        $userToken = $this->getUserIdAuthToken();
        $userId = $userToken['userId'];
        $authToken = $userToken['authToken'];

        return Request::post($this->api . $endpoint, $payload, Mime::FORM)
            ->addHeaders([
                'X-Auth-Token' => $authToken,
                'X-User-Id' => $userId,
            ])
            ->sendsJson()
            ->expectsJson()
            ->send();
    }

    public function rcGet($endpoint, $payload)
    {
        $userToken = $this->getUserIdAuthToken();
        $userId = $userToken['userId'];
        $authToken = $userToken['authToken'];

        $endpoint .= '?' . http_build_query($payload);
        return Request::get($this->api . $endpoint)
            ->addHeaders([
                'X-Auth-Token' => $authToken,
                'X-User-Id' => $userId,
            ])
            ->expectsJson()
            ->send();
    }
}
