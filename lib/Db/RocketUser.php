<?php
/*
 _____________________________________________________________
|   Rocket Chat NextCloud App                                 |
|   Authors: Ruvenss G. Wilches & Pierre Locus                |
|   Proudly working for Rocket.Chat Inc                       |
|   All licences and code belong to Rocket.Chat Inc           |
|   Live long and Prosper                                     | 
|_____________________________________________________________|                                                                                                                                                                             
*/
namespace OCA\RocketchatNextcloud\Db;


class RocketUser
{
    protected $databaseName;
    protected $db;

    public function __construct()
    {
        $this->databaseName = 'rocket_users';
        $this->db = \OC::$server->getDatabaseConnection();
    }
    public function getByNcUserId($ncUserId)
    {
        $query = "SELECT * FROM *PREFIX*" . $this->databaseName . " WHERE nc_user_id=? LIMIT 1";
        $result = $this->db->executeQuery($query, [$ncUserId]);
        return $result->fetch();
    }

    public function updateRocketUser($ncUserId, $rcUserId, $rcToken, $uuidPassword='', $rcCurrentChannelId='')
    {
        $query = "UPDATE *PREFIX*" . $this->databaseName . " SET rc_user_id=?, rc_token=? WHERE nc_user_id=?";
        return $this->db->executeQuery($query, [
            $rcUserId,
            $rcToken,
            $ncUserId
        ]);
    }

    public function createRocketUser($ncUserId, $rcUserId, $rcToken, $uuidPassword='', $rcCurrentChannelId='')
    {
        // If user exists, update it and return token
        if ($this->getByNcUserId($ncUserId)) {
            return $this->updateRocketUser($ncUserId, $rcUserId, $rcToken, $uuidPassword='', $rcCurrentChannelId='');
        }
        // Recording basic user ID info
        $query = "INSERT INTO *PREFIX*" . $this->databaseName . " (nc_user_id, rc_user_id, rc_token, rc_uuid_password, rc_current_channel_id) VALUES (?, ?, ?, ?, ?)";
        $result = $this->db->executeQuery($query, [
            $ncUserId,
            $rcUserId,
            $rcToken,
            $uuidPassword,
            $rcCurrentChannelId
        ]);
        return $result;
    }
}
