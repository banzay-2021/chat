<?php

namespace Chat\Models;

class Messages extends ModelBase
{

    public function getMessages($params)
    {

        $sql = 'SELECT id, user_from, user_to, message, created, status FROM messages';
        $sql .= ' WHERE (user_to=:to';
        $sql .= ' AND user_from=:from)';
        $sql .= ' OR ';
        $sql .= '(user_from=:to1';
        $sql .= ' AND user_to=:from1)';
        $sql .= ' ORDER BY created ASC';
        $sql .= ' LIMIT 10';

        return $this->db->row($sql, $params);
    }

    public function checkStatus($params)
    {

        $sql = 'SELECT id, user_from, user_to, message, created FROM messages';
        $sql .= ' WHERE user_to=:to';
        $sql .= ' AND status IS NULL';
        $sql .= ' ORDER BY created ASC';

        return $this->db->row($sql, $params);
    }
}
