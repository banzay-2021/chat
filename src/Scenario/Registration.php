<?php

namespace Chat\Scenario;

use \Chat\Http\Request;
use \Chat\Inject;
use \Chat\Scenario;


/**
 * Implements scenarios of registration page for authorized visitors.
 */
class Registration implements Scenario
{
    use Inject\HtmlRenderer;


    /**
     * Runs scenario of index page.
     *
     * @param Request $req   HTTP request to registration page.
     *
     * @return array         Result of registration page scenario.
     */
    public function run(Request $req): array
    {
        // TODO: Sending temporary data to the template
        return ['toRender' => [
            'page' => $req->page,
        ]];
    }
}
