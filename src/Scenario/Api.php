<?php

namespace Chat\Scenario;

use \Chat\Http\Request;
use \Chat\Inject;
use \Chat\Models\Users;
use \Chat\Scenario;
use \Chat\Util\DataBase;

/**
 * Implements api request scripts for authorized users.
 */
class Api implements Scenario
{
    /**
     * Contains the data returned from the API request.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Contains POST parameters of HTTP request.
     *
     * @var array
     */
    protected $POST;

    /**
     * Database Instance.
     *
     * @var object
     */
    private $db;

    /**
     * Creates new database connection
     */
    public function __construct()
    {
        $this->db = new DataBase();
    }

    /**
     * Runs scenario of api request.
     *
     * @param Request $req   api request.
     *
     * @return array    Result of index page scenario.
     */
    public function run(Request $req): array
    {

        $this->POST = $req->POST;

        if (!$this->POST->exists(('action'))) return $this->data;

        switch ($this->POST->String('action')) {
            case 'userinfo':
                $this->userinfo();
                break;
            case 'login':
                $this->login();
                break;
            case 'chat':
                // load too arrays messages + friends
                $this->chat();
                break;
            case 'messages':
                $this->messages();
                break;
            case 'friends':
                $this->friends();
                break;
            case 'add-friend':
                $this->addFriend();
                break;
            case 'check-messages':
                $this->checkMessages();
                break;
            case 'add-message':
                $this->addMessage();
                break;
            case 'status-message':
                $this->updateStatusMessage();
                break;
            case 'registration':
                $this->registrationUser();
                break;
            default:
        }

        return $this->data;
    }

    /**
     * @param $userId
     *
     * @return void
     */
    public function userinfo($userId = 0)
    {
        $paramsNeeded = ['user'];
        if (!$this->checkParams($paramsNeeded)) return;

        $params = [
            'id' => $userId > 0 ? $userId : $this->POST->Int('user')
        ];
        $sql = 'SELECT id, name, login FROM users';
        $sql .= ' WHERE id=:id';
        $this->data['data']['userinfo'] = $this->db->rows($sql, $params);
    }

    /**
     * @return void
     */
    public function login()
    {
        $paramsNeeded = ['login', 'pass'];
        if (!$this->checkParams($paramsNeeded)) return;

        $params = [
            'login' => $this->POST->String('login'),
            'pass' => $this->md5Encode($this->POST->Int('pass'))
        ];

        $sql = 'SELECT id, name, login FROM users';
        $sql .= ' WHERE login=:login';
        $sql .= ' AND pass=:pass';
        $this->data['data']['userinfo'] = $this->db->rows($sql, $params);
    }

    /**
     * @return void
     */
    public function chat()
    {
        $this->messages();
        $this->friends();
    }

    /**
     * @return void
     */
    public function messages()
    {
        $paramsNeeded = ['user', 'friend'];
        if (!$this->checkParams($paramsNeeded)) return;

        $params = [
            'to' => $this->POST->Int('user'),
            'from' => $this->POST->Int('friend'),
            'to1' => $this->POST->Int('user'),
            'from1' => $this->POST->Int('friend')
        ];;
        $sql = 'SELECT id, user_from, user_to, message, created, status FROM messages';
        $sql .= ' WHERE (user_to=:to';
        $sql .= ' AND user_from=:from)';
        $sql .= ' OR ';
        $sql .= '(user_from=:to1';
        $sql .= ' AND user_to=:from1)';
        $sql .= ' ORDER BY created ASC';
        $sql .= ' LIMIT 10';
        $this->data['data']['messages'] = $this->db->row($sql, $params);
    }

    /**
     * @return void
     */
    public function checkMessages()
    {
        $paramsNeeded = ['user'];
        if (!$this->checkParams($paramsNeeded)) return;

        $params = [
            'to' => $this->POST->Int('user')
        ];
        $sql = 'SELECT id, user_from, user_to, message, created FROM messages';
        $sql .= ' WHERE user_to=:to';
        $sql .= ' AND status IS NULL';
        $sql .= ' ORDER BY created ASC';
        $this->data['data']['messages'] = $this->db->row($sql, $params);
    }

    /**
     * @return void
     */
    public function friends()
    {
        $paramsNeeded = ['user'];
        if (!$this->checkParams($paramsNeeded)) return;

        $params = [
            'id' => $this->POST->Int('user')
        ];
        $sql = 'SELECT users.id, users.name FROM friends';
        $sql .= ' INNER JOIN users ON friends.friend_id=users.id ';
        $sql .= ' WHERE friends.user_id=:id';
        $sql .= ' ORDER BY users.name';
        $this->data['data']['friends'] = $this->db->row($sql, $params);
    }

    /**
     * @return string|void
     */
    public function addFriend()
    {
        $paramsNeeded = ['friend', 'user'];
        if (!$this->checkParams($paramsNeeded)) return;

        $params = [
            'friend' => $this->POST->Int('friend'),
            'user' => $this->POST->Int('user')
        ];

        $friends = $this->friends();
        if ($friends && count($friends) > 0) {
            foreach ($friends as $friend) {
                if ($friend->id === $params['friend']) {
                    return $this->data['data']['friend'] = 'isfried';
                }
            }
        }


        $sql = 'INSERT INTO friends ( user_id, friend_id) VALUES ';
        $sql .= '(:user, :friend)';
        $this->db->insertFromParams($sql, $params);

        $sql = 'INSERT INTO friends ( user_id, friend_id) VALUES ';
        $sql .= '(:friend, :user)';
        $this->data['data']['friend'] = $this->db->insertFromParams($sql, $params);
    }

    /**
     * @return void
     */
    public function addMessage()
    {
        $paramsNeeded = ['user', 'friend', 'message'];
        if (!$this->checkParams($paramsNeeded)) return;

        $params = [
            'from' => $this->POST->Int('user'),
            'to' => $this->POST->Int('friend'),
            'message' => $this->POST->String('message'),
            'created_by' => $this->POST->Int('user')
        ];

        $sql = 'INSERT INTO messages (message, user_from, user_to, created_by) VALUES ';
        $sql .= '(:message, :from, :to, :created_by)';

        $this->data['data']['message'] = $this->db->insertFromParams($sql, $params);

    }

    /**
     * @return void
     */
    public function updateStatusMessage()
    {
        $paramsNeeded = ['messages'];
        if (!$this->checkParams($paramsNeeded)) return;

        $messages = $this->POST->Arr('messages');

        if ($messages && count($messages) > 0) {
            foreach ($messages as $messagesId) {
                $params = ['id' => $messagesId];
                $sql = 'UPDATE messages SET status = 1 WHERE id = :id';
                $this->db->updateColumn($sql, $params);
            }
            $this->data['data']['result'] = true;
        } else {
            $this->data['data']['result'] = false;
        }
    }

    /**
     * @return string|void
     */
    public function registrationUser()
    {
        // "ref-user" also passed to POST but not used yet
        $paramsNeeded = ['user', 'login', 'pass'];
        if (!$this->checkParams($paramsNeeded)) return;

        $params = [
            'name' => $this->POST->String('name'),
            'login' => $this->POST->String('login'),
            'pass' => $this->md5Encode($this->POST->String('pass'))
        ];

        if ($this->checkLogin()) return $this->data['data']['user'] = 'login';

        $sql = 'INSERT INTO users (name, login, pass) VALUES ';
        $sql .= '(:name, :login, :pass)';

        $this->db->insertFromParams($sql, $params);

        $userId = $this->checkLogin();
        $this->data['data']['user'] = $userId;
        $this->userinfo($userId);
    }

    /**
     * @return mixed
     */
    public function checkLogin()
    {
        $params = [
            'login' => $this->POST->String('login')
        ];
        $sql = 'SELECT id FROM users';
        $sql .= ' WHERE login=:login';

        return $this->db->column($sql, $params);
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    public function checkParams(array $params): bool
    {
        foreach ($params as $param) {
            if (!$this->POST->exists($param)) return false;
        }
        return true;
    }

    /**
     * @param $text
     *
     * @return string
     */
    public function md5Encode($text)
    {
        return md5($text);
    }

    public function loadModel()
    {

    }
}
