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

        $formatedParameters = [];
        foreach ($parameters as $parametersName => $value) {

            if(is_string($value)) {
                $bindType = 's';
            }
            else if(is_float($value)) {
                $bindType = 'd';
            }
            else if(is_int($value)) {
                $bindType = 'i';
            }
            else {
                throw new Exception('Can no determine parameter bind type (Provided value : "'.$value.'") (Query : "'.$query.'"');
            }


            $formatedParameters[$parametersName] = array(
                //'key' => $parametersName,
                'type' => $bindType,
                'value' => $value
            );
        }

        $parametersForBind = [];

        $query = preg_replace_callback('`(:\w+)`', function($matches) use($formatedParameters, &$parametersForBind) {

            $key = $matches[1];

            if(isset($formatedParameters[$key])) {
                $parametersForBind[] = $formatedParameters[$key];
            }

            return '?';


        }, $query);

        $statement = $this->prepare($query);

        if(!$statement) {
            throw new Exception(
               $this->getError()
            );
        }


        $bind = '';
        $values = [];

        foreach ($parametersForBind as $parameter) {

            $bind .= $parameter['type'];
            $values[] = &$parameter['value'];
        }

        $bindParameters = [];
        $bindParameters[] = $bind;
        $bindParameters = array_merge($bindParameters, $values);


        $parametersByReference = [];
        foreach ($bindParameters as &$parameter) {
            $parametersByReference[] = &$parameter;
        }


        call_user_func_array(
            array($statement, 'bind_param'), $parametersByReference
        );

        $statement->execute();

        if($statement->error) {

            throw new Exception($statement->error);
        }

        $result = $statement->get_result();
        if($result) {
            $phiStatement = new Statement($result);
            return $phiStatement;
        }
    }

    public function getCompiledQuery($query, $parameters = null)
    {
        if(empty($parameters)) {
            return $query;
        }
        else {
            $compiledQuery = $query;
            foreach ($parameters as $parameterName => $value) {
                $compiledQuery = preg_replace('`'.$parameterName.'`', $value, $compiledQuery);
            }
            return $compiledQuery;

        }
    }




    public function query($query, $parameters = null)
    {

        if(!empty($parameters)) {
            return $this->preparedQuery($query, $parameters);
        }


        $driverStatement = parent::query($query );

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



