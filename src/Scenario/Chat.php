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
     * Runs scenario of chat page.
     *
     * @param Request $req HTTP request to chat page.
     *
     * @return array       Result of chat page scenario.
     */
    public function run(Request $req): array
    {
        //echo 'Chat => run<br>';
        // TODO: Sending temporary data to the template
        return ['toRender' => [
            'reg' => $req->page,
        ]];
    }
}
