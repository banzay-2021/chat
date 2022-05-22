<?php

namespace Chat\Models;

use \Chat\Http\Request;

class Users extends ModelBase
{

    public function getUserInfo($params)
    {

        $sql = 'SELECT id, name, login FROM users';
        $sql .= ' WHERE id=:id';

        return $this->db->rows($sql, $params);
    }

    public function login($params)
    {

        $sql = 'SELECT id, name, login FROM users';
        $sql .= ' WHERE login=:login';
        $sql .= ' AND pass=:pass';

        return $this->db->rows($sql, $params);
    }

    public function checkLogin($params)
    {

        $sql = 'SELECT id FROM users';
        $sql .= ' WHERE login=:login';

        return $this->db->column($sql, $params);
    }
}
