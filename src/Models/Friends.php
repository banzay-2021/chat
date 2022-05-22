<?php

namespace Chat\Models;

class Friends extends ModelBase
{
    public function getFriends($params)
    {

        $sql = 'SELECT users.id, users.name FROM friends';
        $sql .= ' INNER JOIN users ON friends.friend_id=users.id ';
        $sql .= ' WHERE friends.user_id=:id';
        $sql .= ' ORDER BY users.name';

        return $this->db->row($sql, $params);
    }
}
