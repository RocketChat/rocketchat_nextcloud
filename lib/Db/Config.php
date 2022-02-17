<?php

namespace OCA\RocketIntegration\Db;

use OCA\RocketIntegration\AppInfo\Application;

class Config {
    protected $rocketChatUrlKey;
    protected $tokenKey;
    protected $userIdKey;
    protected $appName;

    public function __construct()
    {
        $this->rocketChatUrlKey = 'rocket_chat_url';
        $this->tokenKey = 'personal_access_token';
        $this->userIdKey = 'user_id';
        $this->appName = Application::APP_ID;
    }
    public function getPersonal_access_token()
    {
        $db = \OC::$server->getDatabaseConnection();

        $query = "SELECT * FROM *PREFIX*appconfig where configkey=? and appid=? LIMIT 1";

        $result = $db->executeQuery($query, [$this->tokenKey, $this->appName]);
        $row = $result->fetch();

        return $row ? $row['configvalue'] : '';
    }
    public function getAdminID()
    {
        $db = \OC::$server->getDatabaseConnection();

        $query = "SELECT * FROM *PREFIX*appconfig where configkey=? and appid=? LIMIT 1";

        $result = $db->executeQuery($query, [$this->userIdKey, $this->appName]);
        $row = $result->fetch();

        return $row ? $row['configvalue'] : '';
    }
    public function getUrl()
    {
        $db = \OC::$server->getDatabaseConnection();

        $query = "SELECT * FROM *PREFIX*appconfig where configkey=? and appid=? LIMIT 1";

        $result = $db->executeQuery($query, [$this->rocketChatUrlKey, $this->appName]);
        $row = $result->fetch();

        return $row ? $row['configvalue'] : '';
    }

    public function getAdminData()
    {
        $db = \OC::$server->getDatabaseConnection();

        $query = "SELECT * FROM *PREFIX*appconfig WHERE appid=? AND (configkey=? OR configkey=? OR configkey=?)";

        $result = $db->executeQuery($query, [$this->appName, $this->tokenKey, $this->userIdKey, $this->rocketChatUrlKey]);

        return $result->fetchAll();
    }

    public function storeAdminData($url, $token, $userId)
    {
        $db = \OC::$server->getDatabaseConnection();

        $urlQuery = "INSERT INTO *PREFIX*appconfig(appid,configkey,configvalue) VALUES(?,?,?)";
        $db->executeQuery($urlQuery, [$this->appName, $this->rocketChatUrlKey, $url]);

        $tokenQuery = "INSERT INTO *PREFIX*appconfig(appid,configkey,configvalue) VALUES(?,?,?)";
        $db->executeQuery($tokenQuery, [$this->appName, $this->tokenKey, $token]);

        $userIdQuery = "INSERT INTO *PREFIX*appconfig(appid,configkey,configvalue) VALUES(?,?,?)";
        $db->executeQuery($userIdQuery, [$this->appName, $this->userIdKey, $userId]);
    }

    public function resetAdminData()
    {
        $db = \OC::$server->getDatabaseConnection();

        $query = "DELETE FROM *PREFIX*appconfig WHERE appid = ? AND configkey != 'enabled'";
        $db->executeQuery($query, [$this->appName]);
    }
}
