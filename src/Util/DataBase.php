<?php

namespace Chat\Util;

use PDO;
use \Chat\Conf;

/**
 * Provides a connection to a MySQL database
 * and provides some methods for fetching.
 */
class DataBase
{
    /**
     * Database connection PDO instance.
     *
     * @var object
     */
    protected $db;

    /**
     *
     */
    public function __construct()
    {
        $params = Conf::$MySQL;

        $dsn = 'mysql:host=' . $params['host'] . ';dbname=' . $params['name'];
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $this->db = new PDO($dsn, $params['user'], $params['pass'], $opt);
        $this->db->exec("set names utf8");
    }

    /**
     * This method uses prepared queries.
     *
     * @param $sql
     * @param $params
     * @return false|\PDOStatement
     */
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

    /**
     * Selecting rows from a result set
     *
     * @param $sql
     * @param $params
     * @return array|false
     */
    public function row($sql, $params = [])
    {
        $query = $this->query($sql, $params);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param $sql
     * @param $params
     * @return \$0|false|mixed|object|\stdClass|null
     */
    public function rows($sql, $params = [])
    {
        $query = $this->query($sql, $params);
        return $query->fetchObject();
    }

    /**
     * @param $sql
     * @param $params
     * @return mixed
     */
    public function column($sql, $params = [])
    {
        $query = $this->query($sql, $params);
        return $query->fetchColumn();
    }

    /**
     * @param $sql
     * @param $params
     * @return false|\PDOStatement
     */
    public function insertFromParams($sql, $params) {
        return $this->query($sql, $params);
    }

    /**
     * @param $sql
     * @param $params
     * @return false|\PDOStatement
     */
    public function updateColumn($sql, $params) {
        return $this->query($sql, $params);
    }

    /**
     * @param $table
     * @return false|mixed
     */
    public function getCountLines($table)
    {
        $sql = "SELECT COUNT(1) FROM " . $table;
        $query = $this->db->query($sql);
        if ($result = $query->fetchColumn()) {
            return $result[0];
        }
        return false;
    }

    /**
     * @param $table
     * @return false|mixed
     */
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
