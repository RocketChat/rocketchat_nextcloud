<?php

namespace OCA\RocketIntegration\Rocket;

use Httpful\Request;
use Psr\Log\LoggerInterface;

class Channel extends Client
{
    public function __construct()
    {
        parent::__construct();
    }

    public function create($name, $members)
    {
        try {
            $response = Request::post($this->api . 'channels.create')
                ->body([
                    'name' => $name,
                    'members' => $members,
                ])
                ->send();

            if ($response->code == 200 && isset($response->body->success) && $response->body->success == true) {
                return [
                    'status' => 'success',
                    'channel' => $response->body->channel,
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

    public function info($name)
    {
        try {
            $response = Request::get($this->api . 'channels.info?roomName=' . $name)->send();

            if ($response->code == 200 && isset($response->body->success) && $response->body->success == true) {
                return [
                    'status' => 'success',
                    'channel' => $response->body->channel,
                ];
            }

            return [
                'status' => 'fail',
                'message' => $response->body,
            ];
        } catch (\Exception $exception) {
            return [
                'status' => 'fail',
                'message' => 'Connection Error',
            ];
        }
    }
}
