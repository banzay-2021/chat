<?php

namespace Chat;

use \Chat\Http\Request;


/**
 * Describes the behavior of an application page script.
 * Model - an entity that contains the actions of logic with the database.
 */
interface Model
{

    public function create($data): array;

    public function read();

    public function update($data): array;

    public function delete($data): array;
}
