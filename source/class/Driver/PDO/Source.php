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

    public function query($query, $parameters = array())
    {
        if (empty($parameters)) {

            $returnValue = parent::query($query);
            if($returnValue) {
                $statement = new Statement($returnValue);
                return $statement;
            }

            $errorInfo = $this->errorInfo();
            $message = '';
            foreach ($errorInfo as $key => $value) {
                $message .= '[' . $key . "] " . $value . "\n";
            }

            throw new \Exception(
                'PDO error : ' . $message
            );

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
                if($statement->errorInfo()) {
                    $errorInfo = $statement->errorInfo();
                    $message = '';
                    foreach ($errorInfo as $key => $value) {
                        $message .= '[' . $key . "] " . $value . "\n";
                    }

                    throw new \Exception(
                        'PDO error : ' . $message
                    );
                }
                return new Statement($statement);;
            }
        }
    }


    public function getLastInsertId($name = null)
    {
        return $this->lastInsertId($name);
    }

    public function getError()
    {
        return $this->errorInfo ();
    }


}



