<?php


namespace Phi\Database\Driver\MySQLi;


use Phi\Database\Statement;
use Phi\Exception;
use Phi\Database\Interfaces\Driver;

class Source extends \MySQLi implements Driver
{


    public function __construct($host = null, $username = null, $password = '', $database = null, $port = null, $socket = null)
    {
        parent::__construct($host, $username, $password, $database, $port, $socket);
    }


    public function escape($string)
    {
        return parent::escape_string($string);
    }

    public function query($query)
    {

        $driverStatement = parent::query($query);

        if ($driverStatement instanceof \MySQLi_result) {

            $statement = new Statement(
                new \Phi\Database\Driver\MySQLi\Statement($driverStatement)
            );
            return $statement;
        } else if ($driverStatement === true) {
            return true;
        } else {
            throw new Exception('Query "' . $query . '" failed');
        }
    }

    public function getLastInsertId()
    {
        return $this->insert_id;
    }


    public function getError()
    {
        return $this->error;
    }


    public function autocommit($value = null)
    {
        if ($value === null) {
            $query = "
                SELECT @@autocommit as autocommitActivated;
            ";
            $data = $this->query($query)->fetchAssoc();

            return $data['autocommitActivated'];
        }
        $this->autocommit($value);
        return $this;
    }
}



