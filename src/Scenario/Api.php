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
        if(isset($_GET)){
            $this->data['get'] = $_GET;
        }

        if(isset($_POST)){
            $this->data['post'] = $_POST;
        }

        $this->db = new DataBase();
    }


    /**
     * Runs scenario of api request.
     *
     * @param Request $req      api request.
     *
     * @return array    Result of index page scenario.
     */
    public function run(Request $req): array
    {
        switch ($this->data['post']['action']) {
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
            case 'registration':
                $this->registrationUser();
                break;
            default:
        }

        $this->returnApi();
        return [];
    }

    public function chat() {
        $this->messages();
        $this->friends();
    }

    public function messages() {
        $params = [
            'to' => $this->data['post']['user'],
            'from' => $this->data['post']['user']
        ];
        $sql = 'SELECT message, created FROM messages';
        $sql .= ' WHERE user_to=:to';
        $sql .= ' OR user_from=:from';

        $this->data['data']['messages'] = $this->db->row($sql, $params);
    }

    public function friends() {
        $params = [
            'id' => $this->data['post']['user']
        ];
        $sql = 'SELECT users.id, users.name FROM friends';
        $sql .= ' INNER JOIN users ON friends.friend_id=users.id ';
        $sql .= ' WHERE friends.user_id=:id';
        $sql .= ' ORDER BY users.name';
        $this->data['data']['friends'] = $this->db->row($sql, $params);
    }

    public function registrationUser($params) {
        // TODO:


        //$sql = "INSERT INTO MyGuests (firstname, lastname, email) VALUES ('html', 'css', 'html5css@example.com')";


        $db = new DataBase();
        $this->data['data'] = $db->insertSQL($params);

        $this->returnApi($this->data);
    }

    public function returnApi() {
        header('Content-Type: application/json; charset=utf-8');
        exit(json_encode($this->data));
    }
}
