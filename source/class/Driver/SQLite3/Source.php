<?php


namespace Phi\Database\Driver\SQLite3;

use Phi\Database\Exception;
use Phi\Database\Interfaces\Driver;

class Source extends \SQLite3 implements Driver
{


    public function __construct($filename)
    {
        parent::__construct($filename);
    }


    public function escape($string)
    {
        return $this->escapeString($string);
    }

    public function escapeField($string)
    {
        return '`'.$string.'`';
    }


    public function query($query, $parameters = null)
    {
        if(is_array($parameters)) {
            $statement = $this->prepare($query);

            foreach ($parameters as $parameter => $value) {
                $statement->bindValue($parameter, $value);
            }

            $resultStatement =  new \Phi\Database\Driver\SQLite3\Statement($statement->execute());


            return $resultStatement;

        }
        else {
            $statement = new \Phi\Database\Driver\SQLite3\Statement(parent::query($query));
            return $statement;
        }
    }


    public function fetchAssoc()
    {

    }

    public function getLastInsertId()
    {
        $data = $this->query('SELECT last_insert_rowid() as id')->fetchAssoc();
        if(array_key_exists('id', $data)) {
            return $data['id'];
        }
        else {
            throw new Exception("Can not access last insert id");
        }
    }

    public function commit()
    {

    }

    public function autocommit($value = null)
    {

    }

    public function getError()
    {

    }


}



