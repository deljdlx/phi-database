<?php


namespace Phi\Database\Driver\MySQLi;


use Phi\Database\Exception;
use Phi\Database\FieldDescriptor;
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

    public function escapeField($string)
    {
        return '`' . $string . '`';
    }


    public function getDescriptor($tableName)
    {

        $fields = [];

        $query = 'DESCRIBE ' . $tableName . ';';
        $rows = $this->query($query)->fetchAll();

        foreach ($rows as $values) {
            $descriptor = new FieldDescriptor();
            $descriptor->loadFromMySQL($values);
            $fields[] = $descriptor;
        }
        return $fields;
    }


    public function query($query, $resultmode = null)
    {

        $driverStatement = parent::query($query, $resultmode);

        if ($driverStatement instanceof \MySQLi_result) {

            $statement = new Statement($driverStatement);

            return $statement;
        }
        else if ($driverStatement === true) {
            return true;
        }
        else {
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



