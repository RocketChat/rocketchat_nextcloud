<?php

namespace OCA\RocketchatNextcloud\Rocket;

use Httpful\Request;
use Httpful\Mime;
use Ramsey\Uuid\Uuid;
use OCA\RocketchatNextcloud\Db\RocketUser as RocketUserDb;
use OCP\IUserSession;
use Psr\Log\LoggerInterface;
use OCA\RocketchatNextcloud\Db\Config;

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

    public function createTokenByUserId($userId)
    {
        try {
            $payload = [
                'userId' => $userId,
            ];
            $response = $this->rcPost(self::CREATE_TOKEN, $payload);
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
            $username = str_replace('@', '_at_', $username);
            $name = $user->getDisplayName();
            $email = $user->getEMailAddress();
            $uuidPassword = Uuid::uuid4()->toString();
            $payload = [
                'username' => $username,
                'name' => $name,
                'pass' => $uuidPassword,
                'email' => $email
            ];

            $response = $this->rcPost(self::REGISTER_USER, $payload);
            $this->logger->error('Trying to register User in RocketChat :' . print_r($response, true));
            if (isset($response->body->success) && $response->body->success == true) {
                $_id = $response->body->user->_id;
                $this->logger->error('Success ! Getting token with authenticate');
                $token = $this->authenticateUser($username, $uuidPassword);
                $this->logger->error(print_r([
                    'status' => 'success',
                    'userId' => $_id,
                    'authToken' => $token
                ], true));
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
        try {
            $payload = [
                'username' => $ncUserId,
            ];
            $response = $this->rcGet(self::USER_INFO, $payload);
            $this->logger->error('Got response from GetUserInfo : ' . print_r($response, true));
            if ($response->code == 200 && $response->body->success == true) {
                return $response->body;
            }
        } catch (Exception $e) {
            $this->logger->error('Error in getUserInfo : ' . $e->getMessage());
        }
        return false;
    }

    public function authenticateUser($ncUserId, $uuidPassword=false)
    {
        try {
            $payload = [
                'username' => $ncUserId
            ];
            $response = $this->rcPost(self::CREATE_TOKEN, $payload);
            $this->logger->info('Got response from RC Authenticate User:' . print_r($response, true));
            if ($response->code == 200 && $response->body->success == true) {
                $this->rocketUserDb->createRocketUser(
                    $ncUserId,
                    $response->body->data->userId,
                    $response->body->data->authToken,
                    $uuidPassword
                );
                return $response->body->data->authToken;
            }
        } catch (Exception $e) {
            $this->logger->error('Exception when authenticating user : ' . $e->getMessage());
        }
        return false;
    }

    public function findByNcUserId($ncUserId)
    {
        try {
            // Bypassing for now, even if tokens shouldn't expire 
            // it looks like I cannot re-use the one I have in database
            /* Check if user exists in NC Db: means he has token */
            // $userInDb = $this->rocketUserDb->getByNcUserId($ncUserId);
            // if ($userInDb) {
            //     $this->logger->warning('User was in DB, token is');
            //     $this->logger->warning($userInDb['rc_token']);
            //     return $userInDb['rc_token'];
            // }

            /* Check if user exists in RC : means we must authenticate and keep his token */
            $this->logger->error('Trying to get User Info from RocketChat');
            $userInRocket = $this->getUserInfo($ncUserId);
            if ($userInRocket) {
                $this->logger->error('Found user in Rocket, authenticating...');
                $auth = $this->authenticateUser($ncUserId);
                $this->logger->error(print_r($auth, true));
                return $auth;
            }
            /* If none: create user in rocket then authenticate */

            return $this->createUser()['authToken'];
        } catch (Exception $e) {
            $this->logger->error('Exception when creating user in Rocket Chat : ' . $e->getMessage());
        }
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
            $this->logger->warning('Couldn\'t login admin, unknown error');
            return [
                'status' => 'error',
                'message' => 'Couldn\'t login admin',
            ];
        } catch (Exception $e) {
            $this->logger->error('Exception when login admin : ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}
