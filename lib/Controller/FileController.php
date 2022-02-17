<?php

namespace OCA\RocketIntegration\Controller;

use OCA\RocketIntegration\Db\FileChat;
use OCA\RocketIntegration\Db\Config;
use OCA\RocketIntegration\Db\File;
use OCA\RocketIntegration\Rocket\Channel;
use OCA\RocketIntegration\Rocket\Discussion;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\IServerContainer;
use Psr\Log\LoggerInterface;

class FileController extends Controller
{
    protected $config;
    protected $appName;
    protected $server;
    protected $logger;

    public function __construct($AppName, IRequest $request, IServerContainer $server, LoggerInterface $logger)
    {
        parent::__construct($AppName, $request);

        $this->config = new Config();
        $this->server = $server;
        $this->appName = $AppName;
        $this->logger = $logger;
    }

    /**
     * @NoAdminRequired
     * @param $id
     * @param $name
     * @param $isGroupFolder
     * @return JSONResponse
     */
    public function store($id, $name, $isGroupFolder)
    {
        if (!$id || !$name) {
            return new JSONResponse([
                'message' => 'File required.',
            ], 403);
        }

        $fileTable = new File();

        $file = $fileTable->find($id);

        if ( ! $file) {
            return new JSONResponse([
                'message' => 'File not found.',
            ], 403);
        }

        try {
            $result = $this->getChat($id, $name);
        } catch (\Exception $exception) {
            return new JSONResponse([
                'message' => $exception->getMessage(),
            ], 403);
        }

        return new JSONResponse([
            'redirect' => ($this->server->getURLGenerator())->linkToRoute($this->appName . '.page.file')
                . "?chat={$result['id']}" . '&new=' . ($result['isNew'] ? '1' : '0'),
        ]);
    }

    protected function getChat($id, $name)
    {
        $fileChat = new FileChat();

        $record = $fileChat->getByFileId($id);

        // Chat already exists in out DB.
        if ($record && $record['chat_id']) {
            return [
                'id' => $record['chat_id'],
                'isNew' => false,
            ];
        }

        $channel = new Channel();

        $filesChannelName = 'nextcloud_files';
        $info = $channel->info($filesChannelName);

        // Channel does not exist, create it.
        if ($info['status'] !== 'success') {
            // Create channel
            $response = $channel->create($filesChannelName, []);

            if ($response['status'] !== 'success') {
                throw new \Exception('Could not create channel. (' . json_encode($info) . ')');
            }

            $channel = $response['channel'];
        } else {
            $channel = $info['channel'];
        }

        $createResponse = (new Discussion())->create($name, $channel->_id);

        $chatId = $createResponse['discussion']->name;

        // Only store in DB if the discussion is not a 'new discussion', only store in DB the general one.
        $fileChat->create($id, $chatId);

        return [
            'id' => $chatId,
            'isNew' => true,
        ];
    }
}
