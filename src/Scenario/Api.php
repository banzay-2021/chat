<?php

namespace Chat\Scenario;

use \Chat\Http\Request;
use \Chat\Models\Friends;
use \Chat\Models\Messages;
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
     * @var object
     */
    protected $Friends;

    /**
     * @var object
     */
    protected $Messages;

    /**
     * @var object
     */
    protected $Users;

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
        $this->Users = new Users();
        $this->Messages = new Messages();
        $this->Friends = new Friends();
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

        $this->data['data']['userinfo'] = $this->Users->getUserInfo($params);
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

        $this->data['data']['userinfo'] = $this->Users->login($params);
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
        ];

        $this->data['data']['messages'] = $this->Messages->getMessages($params);
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

        $this->data['data']['messages'] = $this->Messages->checkStatus($params);
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

        $this->data['data']['friends'] = $this->Friends->getFriends($params);
    }

    /**
     * @return string|void
     */
    public function addFriend()
    {
        // TODO: Need to transfer to Model

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
        // TODO: Need to transfer to Model

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
        // TODO: Need to transfer to Model

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
        // TODO: Need to transfer to Model

        // "ref-user" also passed to POST but not used yet
        $paramsNeeded = ['name', 'login', 'pass'];
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

        return $this->Users->checkLogin($params);
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
}
