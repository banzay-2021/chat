<?php

namespace Chat\Util;

use PDO;
use \Chat\Conf;

class DataBase
{
    protected $db;

    public function __construct()
    {
        $params = Conf::$MySQL;

        $dsn = 'mysql:host=' . $params['host'] . ';dbname=' . $params['name'];
        $this->db = new PDO($dsn, $params['user'], $params['pass']);
        $this->db->exec("set names utf8");
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            foreach ($params as $key => $val) {
                $stmt->bindValue(':' . $key, $val);
            }
        }
        $stmt->execute();
        return $stmt;
    }

    public function row($sql, $params = [])
    {
        $query = $this->query($sql, $params);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function rows($sql, $params = [])
    {
        $query = $this->query($sql, $params);
        return $query->fetchObject();
    }

    public function column($sql, $params = [])
    {
        $query = $this->query($sql, $params);
        return $query->fetchColumn();
    }

    public function insertFromParams($sql, $params) {
        return $this->query($sql, $params);
    }

    public function getCountLines($table)
    {
        $sql = "SELECT COUNT(1) FROM " . $table;
        $query = $this->db->query($sql);
        if ($result = $query->fetchColumn()) {
            return $result[0];
        }
        return false;
    }

    public function getMaxId($table)
    {
        $sql = "SELECT MAX(`id`) FROM " . $table;
        $query = $this->db->query($sql);
        if ($result = $query->fetchColumn()) {
            return $result[0];
        }
        return false;
    }
}
