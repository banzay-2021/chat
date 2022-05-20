<?php

namespace Chat\Scenario;

use \Chat\Http\Request;
use \Chat\Inject;
use \Chat\Model\Users;
use \Chat\Scenario;
use \Chat\Util\DataBase;

/**
 * Implements api request scripts for authorized visitors.
 */
class Api implements Scenario
{
    protected $data = [];

    private $db;

    public function __construct()
    {
        if (isset($_GET)) {
            $this->data['get'] = $_GET;
        }

        if (isset($_POST)) {
            $this->data['post'] = $_POST;
        }

        $this->db = new DataBase();
    }


    /**
     * Runs scenario of api request.
     *
     * @param Request $req api request.
     *
     * @return array    Result of index page scenario.
     */
    public function run(Request $req): array
    {
        switch ($this->data['post']['action']) {
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

        $this->returnApi();
        return [];
    }

    public function userinfo($userId = 0)
    {
        $params = [
            'id' => $userId > 0 ? $userId : $this->data['post']['user']
        ];
        $sql = 'SELECT id, name, login FROM users';
        $sql .= ' WHERE id=:id';
        $this->data['data']['userinfo'] = $this->db->rows($sql, $params);
    }

    public function login()
    {
        $params = [
            'login' => $this->data['post']['login'],
            'pass' => $this->md5Encode($this->data['post']['pass'])
        ];

        $sql = 'SELECT id, name, login FROM users';
        $sql .= ' WHERE login=:login';
        $sql .= ' AND pass=:pass';
        $this->data['data']['userinfo'] = $this->db->rows($sql, $params);
    }

    public function chat()
    {
        $this->messages();
        $this->friends();
    }

    public function messages()
    {
        $params = [
            'to' => $this->data['post']['user'],
            'from' => $this->data['post']['friend']
        ];
        $sql = 'SELECT id, user_from, user_to, message, created, status FROM messages';
        $sql .= ' WHERE (user_to=:to';
        $sql .= ' AND user_from=:from)';
        $sql .= ' OR ';
        $sql .= '(user_from=:to';
        $sql .= ' AND user_to=:from)';
        $sql .= ' ORDER BY created ASC';
        $sql .= ' LIMIT 10';
        $this->data['data']['messages'] = $this->db->row($sql, $params);
    }

    public function checkMessages()
    {
        $params = [
            'to' => (int) $this->data['post']['user']
        ];
        $sql = 'SELECT id, user_from, user_to, message, created FROM messages';
        $sql .= ' WHERE user_to=:to';
        $sql .= ' AND status IS NULL';
        $sql .= ' ORDER BY created ASC';
        $this->data['data']['messages'] = $this->db->row($sql, $params);
    }

    public function friends()
    {
        $params = [
            'id' => $this->data['post']['user']
        ];
        $sql = 'SELECT users.id, users.name FROM friends';
        $sql .= ' INNER JOIN users ON friends.friend_id=users.id ';
        $sql .= ' WHERE friends.user_id=:id';
        $sql .= ' ORDER BY users.name';
        $this->data['data']['friends'] = $this->db->row($sql, $params);
    }

    public function addFriend()
    {
        $params = [
            'friend' => $this->data['post']['friend'],
            'user' => $this->data['post']['user']
        ];

        $myFrieds = $this->friends();
        if ($myFrieds && count($myFrieds) > 0) {
            foreach ($myFrieds as $fried) {
                if ($fried->id == $params['friend']) {
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

    public function addMessage()
    {
        $params = [
            'from' => $this->data['post']['user'],
            'to' => $this->data['post']['friend'],
            'message' => $this->data['post']['message'],
            'created_by' => $this->data['post']['user']
        ];

        $sql = 'INSERT INTO messages (message, user_from, user_to, created_by) VALUES ';
        $sql .= '(:message, :from, :to, :created_by)';

        $this->data['data']['message'] = $this->db->insertFromParams($sql, $params);

    }

    public function updateStatusMessage()
    {
        if (isset($this->data['post']['messages']) && count($this->data['post']['messages']) > 0) {
            foreach ($this->data['post']['messages'] as $messagesId) {
                $params = ['id' => $messagesId];
                $sql = 'UPDATE messages SET status = 1 WHERE id = :id';
                $this->db->updateColumn($sql, $params);
            }
            $this->data['data']['result'] = true;
        } else {
            $this->data['data']['result'] = false;
        }
    }

    public function registrationUser()
    {
        // "ref-user" also passed to POST but not used yet
        $params = [
            'name' => $this->data['post']['name'],
            'login' => $this->data['post']['login'],
            'pass' => $this->md5Encode($this->data['post']['pass'])
        ];

        $checkColumn = $this->checkLogin();
        if ($checkColumn) return $this->data['data']['user'] = 'login';

        $sql = 'INSERT INTO users (name, login, pass) VALUES ';
        $sql .= '(:name, :login, :pass)';

        $this->db->insertFromParams($sql, $params);

        $userId = $this->checkLogin();
        $this->data['data']['user'] = $userId;
        $this->userinfo($userId);
    }

    public function checkLogin()
    {
        $params = [
            'login' => $this->data['post']['login']
        ];
        $sql = 'SELECT id FROM users';
        $sql .= ' WHERE login=:login';

        return $this->db->column($sql, $params);
    }

    public function md5Encode($text)
    {
        return md5($text);
    }

    public function returnApi()
    {
        header('Content-Type: application/json; charset=utf-8');
        exit(json_encode($this->data, JSON_UNESCAPED_UNICODE));
    }
}
