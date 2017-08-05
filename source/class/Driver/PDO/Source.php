<?php


namespace Phi\Database\Driver\PDO;

use Phi\Database\Interfaces\Driver;

class Source extends \PDO implements Driver
{


    private $dsn;

    public function __construct($dsn, $login = '', $password = '')
    {
        $this->dsn = $dsn;
        parent::__construct($dsn, $login, $password);
    }


    public function escape($string)
    {
        return $this->quote($string);
    }

    public function query($query, array $parameters = array())
    {
        if (empty($parameters)) {
            $statement = new Statement(parent::query($query));
            return $statement;
        }
        else {
            $statement = $this->prepare($query);
            if (!$statement) {

                $errorInfo = $this->errorInfo();
                $message = '';
                foreach ($errorInfo as $key => $value) {
                    $message .= '[' . $key . "] " . $value . "\n";
                }

                throw new \Exception(
                    'PDO error : ' . $message
                );
            }

            $result = $statement->execute($parameters);
            if ($result) {
                return new Statement($statement);
            }
            else {
                return null;
            }
        }
    }


    public function getLastInsertId($name = null)
    {
        return $this->lastInsertId($name);
    }


}



