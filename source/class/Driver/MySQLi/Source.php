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



    public function preparedQuery($query, array $parameters = array())
    {

        $formetedParameters = [];
        foreach ($parameters as $parametersName => $value) {

            $query = preg_replace('`'.$parametersName.'`', '?', $query);
            if(is_string($value)) {
                $bindType = 's';
            }
            else if(is_float($value)) {
                $bindType = 'd';
            }
            else if(is_int($value)) {
                $bindType = 'i';
            }

            $formetedParameters[] = array(
                'type' => $bindType,
                'value' => $value
            );
        }

        $statement = $this->prepare($query);
        $bind = '';
        $values = [];

        foreach ($formetedParameters as $parameter) {

            $bind .= $parameter['type'];
            $values[] = &$parameter['value'];
        }

        $bindParameters = [];
        $bindParameters[] = $bind;
        $bindParameters = array_merge($bindParameters, $values);


        echo '<pre id="' . __FILE__ . '-' . __LINE__ . '" style="border: solid 1px rgb(255,0,0); background-color:rgb(255,255,255)">';
        echo '<div style="background-color:rgba(100,100,100,1); color: rgba(255,255,255,1)">' . __FILE__ . '@' . __LINE__ . '</div>';
        print_r($query);
        echo '</pre>';

        call_user_func_array(
            array($statement, 'bind_param'), $bindParameters
        );

        $statement->execute();

        $result = $statement->get_result();
        if($result) {
            $phiStatement = new Statement($result);
            return $phiStatement;
        }


    }


    public function query($query, $parameters = null)
    {


        if(!empty($parameters)) {
            return $this->preparedQuery($query, $parameters);
        }



        $driverStatement = parent::query($query, $parameters );

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



