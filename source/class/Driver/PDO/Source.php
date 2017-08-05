<?php


namespace Phi\Database\Driver\PDO;

use Phi\Database\Interfaces\Driver;

class Source extends \PDO implements Driver
{


    private $dsn;

    public function __construct($dsn, $login='', $password='')
    {
        $this->dsn=$dsn;
        parent::__construct($dsn, $login, $password);
    }


    public function escape($string)
    {
        return $this->quote($string);
    }

    public function query($query)
    {

        $statement = new Statement(parent::query($query));

        return $statement;
    }

    public function getLastInsertId($name = null)
    {
        return $this->lastInsertId($name);
    }





}



