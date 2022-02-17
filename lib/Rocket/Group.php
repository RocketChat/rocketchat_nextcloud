<?php

namespace OCA\RocketIntegration\Rocket;

use Httpful\Request;

class Group extends Client
{
    public function __construct()
    {
        parent::__construct();
    }

    public function create($name, $members)
    {
        try {
            $response = Request::post($this->api . 'groups.create')
                ->body([
                    'name' => $name,
                    'members' => $members,
                ])
                ->send();

            if ($response->code == 200 && isset($response->body->success) && $response->body->success == true) {
                return [
                    'status' => 'success',
                    'group' => $response->body->group,
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
            $response = Request::get($this->api . 'groups.info?roomName=' . $name)->send();

            if ($response->code == 200 && isset($response->body->success) && $response->body->success == true) {
                return [
                    'status' => 'success',
                    'group' => $response->body->group,
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
