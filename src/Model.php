<?php

namespace Chat;

use Chat\Util\DataBase;
use http\Params;

/**
 * TEST
 * Describes the behavior of an application page script.
 * Model - an entity that contains the actions of logic with the database.
 */
interface Model
{
    /**
     * Test method
     *
     * @param $data     Data to write to the database.
     *
     * @return mixed
     */
    public function setData($data);

    /**
     * Method for getting data
     *
     * @return mixed
     */
    public function getData();
}
