<?php

namespace OCA\RocketchatNextcloud\Db;

class File
{
    public function find($id)
    {
        $db = \OC::$server->getDatabaseConnection();

        $query = "SELECT * FROM *PREFIX*filecache where fileid = ? LIMIT 1";

        $result = $db->executeQuery($query, [$id]);

        return $result->fetch();
    }
}
