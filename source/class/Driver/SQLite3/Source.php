<?php


namespace Phi\Database\Driver\SQLite3;


use Phi\Database\Statement;
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

    public function query($query, $parameters = null)
    {
        if(is_array($parameters)) {
            $statement = $this->prepare($query);

            foreach ($parameters as $parameter => $value) {
                $statement->bindValue($parameter, $value);
            }

            $resultStatement =  new Statement(
                new \Phi\Database\Driver\SQLite3\Statement($statement->execute())
            );

            return $resultStatement;

        }
        else {
            $statement = new Statement(
                new \Phi\Database\Driver\SQLite3\Statement(parent::query($query))
            );
            return $statement;
        }
    }


    public function fetchAssoc()
    {

    }

    public function getLastInsertId()
    {

    }

    public function commit()
    {

    }

    public function autocommit($value = null)
    {

    }


}



