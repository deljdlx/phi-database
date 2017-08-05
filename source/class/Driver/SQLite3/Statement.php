<?php


namespace Phi\Database\Driver\SQLite3;


/**
 * Class Statement
 * @property \SQLite3Result statement
 * @package Phi\Database\SQLite3
 */

class Statement implements \Phi\Database\Interfaces\Statement
{

    protected $statement;

    public function __construct(\SQLite3Result $statement)
    {
        $this->statement = $statement;
    }

    public function fetchAssoc()
    {
        return $this->statement->fetchArray(SQLITE3_ASSOC);
    }


}



