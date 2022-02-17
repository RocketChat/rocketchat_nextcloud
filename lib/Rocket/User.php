<?php

namespace OCA\RocketIntegration\Rocket;

use Httpful\Request;
use Httpful\Mime;
use Ramsey\Uuid\Uuid;
use OCA\RocketIntegration\Db\RocketUser as RocketUserDb;
use OCP\IUserSession;
use Psr\Log\LoggerInterface;
use OCA\RocketIntegration\Db\Config;

class User extends Client
{
    /** USER_CREATE 
     * @expects 
     * - R email
     * - R name
     * - R password
     * - R username
     * - O active
     * - O roles
     * - O verified
     * @returns
     * - success
     * - user {
     *      _id
     *      username
     * }
    */
    const CREATE_USER = 'users.create';
    const REGISTER_USER = 'users.register';

    /** CREATE_TOKEN 
     * @expects
     * - R userId or username
     * @returns
     * - success
     * - data {
     *      userId
     *      authToken
     * }
    */
    const CREATE_TOKEN = 'users.createToken';
    const USER_INFO = 'users.info';
    const V1 = 'api/v1/';
    const ROCKET_LOGIN = 'login';

    /** @var IUserSession */
    private $userSession;

    /** @var RocketUserDb */
    private $rocketUserDb;

    /** @var LoggerInterface */
    private $logger;

    /** @var Config */
    private $config;

    public function __construct(IUserSession $userSession, RocketUserDb $rocketUserDb, LoggerInterface $logger, Config $config)
    {
        parent::__construct();
        $this->userSession = $userSession;
        $this->rocketUserDb = $rocketUserDb;
        $this->logger = $logger;
        $this->config = $config;
    }

    public function all()
    {
        try {
            $response = Request::get($this->api . 'users.list')->send();

            if ($response->code == 200 && isset($response->body->success) && $response->body->success == true) {
                return [
                    'status' => 'success',
                    'users' => $response->body->users,
                ];
            }

            return [
                'status' => 'fail',
                'message' => $response->body->error,
            ];
        } catch (\Exception $exception) {
            return [
                'status' => 'fail',
                'message' => 'Connection Error',
            ];
        }
    }

    public function getTokenByUserId($userId)
    {
        return;
    }

    public function createTokenByUserId($userId)
    {
        try {
            $payload = [
                'userId' => $userId,
            ];
            $response = Request::post($this->api . self::CREATE_TOKEN, $payload, Mime::FORM)->send();
            if ($response->code == 200 && isset($response->body->success) && $response->body->success == true) {
                return [
                    'status' => 'success',
                    'token' => $response->body->data->authToken,
                ];
            }
            return [
                'status' => 'error',
                'message' => 'Couldn\'t get token',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    public function createUser()
    {
        try {
            $user = $this->userSession->getUser();
            $username = $user->getUID();
            $name = $user->getDisplayName();
            $email = $user->getEMailAddress();
            $uuidPassword = Uuid::uuid4()->toString();
            $payload = [
                'username' => $username,
                'name' => $name,
                'pass' => $uuidPassword,
                'email' => $email
            ];
            $userId = $this->config->getAdminID();
            $authToken = $this->config->getPersonal_access_token();
            $rocketUrl = $this->config->getUrl();
            if ($rocketUrl[-1] != '/') {
                $rocketUrl .= '/';
            }
            $response = Request::post($rocketUrl . self::V1 . self::REGISTER_USER, $payload, Mime::FORM)
                ->addHeaders([
                    'X-Auth-Token' => $authToken,
                    'X-User-Id' => $userId,
                ])
                ->expectsJson()
                ->send();
            if (isset($response->body->success) && $response->body->success == true) {
                $_id = $response->body->user->_id;
                $token = $this->authenticateUser($username, $uuidPassword);
                return [
                    'status' => 'success',
                    'userId' => $_id,
                    'authToken' => $token
                ];
            }
            return [
                'status' => 'error',
                'message' => 'Couldn\'t create user'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function getUserInfo($ncUserId)
    {
        $userId = $this->config->getAdminID();
        $authToken = $this->config->getPersonal_access_token();
        $rocketUrl = $this->config->getUrl();
        $userInfoUrl = $rocketUrl . self::V1 . self::USER_INFO . '?' . http_build_query([
            'username' => $ncUserId,
        ]);

        $response = Request::get($userInfoUrl)
            ->addHeaders([
                'X-Auth-Token' => $authToken,
                'X-User-Id' => $userId,
            ])
            ->expectsJson()
            ->send();
        if ($response->code == 200 || $response->body->success == true) {
            return $response->body;
        }

        if ($response->body->success == false) {
            return false;
        }
        return false;
    }

    public function authenticateUser($ncUserId, $uuidPassword=false)
    {
        $userId = $this->config->getAdminID();
        $authToken = $this->config->getPersonal_access_token();
        $rocketUrl = $this->config->getUrl();
        $payload = [
            'username' => $ncUserId
        ];
        if ($rocketUrl[-1] != '/') {
            $rocketUrl .= '/';
        }
        $response = Request::post($rocketUrl . self::V1 . self::CREATE_TOKEN, $payload, Mime::FORM)
            ->addHeaders([
                'X-Auth-Token' => $authToken,
                'X-User-Id' => $userId,
            ])
            ->expectsJson()
            ->send();
        if ($response->code == 200 || $response->body->success == true) {
            $this->rocketUserDb->createRocketUser(
                $ncUserId,
                $response->body->data->userId,
                $response->body->data->authToken,
                $uuidPassword
            );
            return $response->body->data->authToken;
        }
        return false;
    }

    public function findByNcUserId($ncUserId)
    {
        /* Check if user exists in NC Db: means he has token */
        $userInDb = $this->rocketUserDb->getByNcUserId($ncUserId);
        if ($userInDb) {
            return $userInDb['rc_token'];
        }
        /* Check if user exists in RC : means we must authenticate and keep his token */
        $userInRocket = $this->getUserInfo($ncUserId);
        if ($userInRocket) {
            return $this->authenticateUser($ncUserId);
        }
        /* If none: create user in rocket then authenticate */
        return $this->createUser()['authToken'];
    }

    public function loginAdmin($url, $username, $password)
    {
        try {
            $payload = [
                'user' => $username,
                'password' => $password
            ];

            if ($url[-1] != '/') {
                $url .= '/';
            }

            $response = Request::post($url . self::V1 . self::ROCKET_LOGIN, $payload, Mime::FORM)
                ->expectsJson()
                ->send();
            if ($response->code == 200) {
                $userId = $response->body->data->userId;
                $authToken = $response->body->data->authToken;
                $this->config->resetAdminData();
                $this->config->storeAdminData($url, $authToken, $userId);
                return [
                    'status' => 'success',
                    'userId' => $userId,
                    'authToken' => $authToken,
                ];
            }

            return [
                'status' => 'error',
                'message' => 'Couldn\'t login user',
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}
