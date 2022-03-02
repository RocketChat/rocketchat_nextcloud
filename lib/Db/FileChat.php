<?php

namespace OCA\RocketchatNextcloud\Db;

class FileChat
{
    protected $databaseName;
    protected $db;

    public function __construct()
    {
        $this->databaseName = 'rocket_file_chats';

        $this->db = \OC::$server->getDatabaseConnection();
    }

    public function getByFileId($fileId)
    {
        $query = "SELECT * FROM *PREFIX*" . $this->databaseName . " WHERE file_id=? LIMIT 1";

        $result = $this->db->executeQuery($query, [(int) $fileId]);

        return $result->fetch();
    }

    public function create($fileId, $chatId)
    {
        $query = "INSERT INTO *PREFIX*" . $this->databaseName . "(file_id, chat_id, created) VALUES (?, ?, ?)";

        return $this->db->executeQuery($query, [
            (int) $fileId,
            $chatId,
            date('Y-m-d H:i:s')
        ]);
    }

    public function deleteAll()
    {
        $query = "DELETE FROM *PREFIX*" . $this->databaseName;

        $this->db->executeQuery($query);
    }
}
