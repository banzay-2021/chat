<?php

namespace Chat\Scenario;

use \Chat\Http\Request;
use \Chat\Inject;
use \Chat\Scenario;

/**
 * Implements scenarios of login page for authorized visitors.
 */
class Login implements Scenario
{
    use Inject\HtmlRenderer;

    /**
     * Runs scenario of login page.
     *
     * @param Request $req HTTP request to login page.
     *
     * @return array       Result of ligin page scenario.
     */
    public function run(Request $req): array
    {
        // TODO: Sending temporary data to the template
        return ['toRender' => [
            'page' => $req->page,
        ]];
    }
}

