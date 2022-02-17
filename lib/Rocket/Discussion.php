<?php

namespace OCA\RocketIntegration\Rocket;

use Httpful\Request;

class Discussion extends Client
{
    public function __construct()
    {
        parent::__construct();
    }

    public function create($name, $channelId)
    {
        try {
            $response = Request::post($this->api . 'rooms.createDiscussion')
                ->body([
                    'prid' => $channelId,
                    't_name' => $name,
                ])
                ->send();

            if ($response->code == 200 && isset($response->body->success) && $response->body->success == true) {
                return [
                    'status' => 'success',
                    'discussion' => $response->body->discussion,
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

    public function info($discussionId)
    {
        try {
            $response = Request::get($this->api . 'rooms.info?roomId=' . $discussionId)->send();

            if ($response->code == 200 && isset($response->body->success) && $response->body->success == true) {
                return [
                    'status' => 'success',
                    'discussion' => $response->body->room,
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
}
