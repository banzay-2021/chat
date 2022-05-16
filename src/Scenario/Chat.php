<?php

namespace Chat\Scenario;

use \Chat\Http\Request;
use \Chat\Inject;
use \Chat\Scenario;

/**
 * Implements scenarios of chat page for authorized visitors.
 */
class Chat implements Scenario
{
    use Inject\HtmlRenderer;

    /**
     * Runs scenario of index page.
     *
     * @param Request $req HTTP request to chat page.
     *
     * @return array    Result of chat page scenario.
     */
    public function run(Request $req): array
    {
        // TODO: Sending temporary data to the template
        return ['toRender' => [
            'reg' => $req->page,
            'user' => 1,
            'friends' => ['Vasia', 'Petia'],
            'messages' => [
                [
                    'message' => 'Message from 1 to 2',
                    'user_from' => 1,
                    'user_to' => 2,
                    'created' => '2022-05-13 14:47:00'
                ],
                [
                    'message' => 'Message from 2 to 1',
                    'user_from' => 2,
                    'user_to' => 1,
                    'created' => '2022-05-13 14:47:00'
                ]
            ]
        ]];
    }
}
