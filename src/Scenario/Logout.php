<?php

namespace Chat\Scenario;

use \Chat\Http\Request;
use \Chat\Inject;
use \Chat\Scenario;

/**
 * Implements scenarios of log out page for authorized visitors.
 */
class Logout implements Scenario
{
    use Inject\HtmlRenderer;

    /**
     * Runs scenario of log out page.
     *
     * @param Request $req HTTP request to log out page.
     *
     * @return array    Result of log out page scenario.
     */
    public function run(Request $req): array
    {
        //echo 'Log out => run<br>';
        // TODO: Sending temporary data to the template
        return ['toRender' => [
            'reg' => $req->page,
        ]];
    }
}

