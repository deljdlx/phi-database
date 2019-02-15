<?php


namespace Phi\Database;


use Phi\Database\Interfaces\Driver;


/**
 * Class Source
 * @property Driver source
 * @package Phi\Database
 */
class Source
{

    protected $source = null;

    public function __construct(Driver $source)
    {
        $this->source = $source;
    }


    public function getDriver()
    {
        return $this->source;
    }

    public function getError()
    {
        return $this->source->getError();
    }


    public function escape($string, $type = null)
    {
        return $this->source->escape($string);
    }

    public function escapeField($string, $type = null)
    {
        return $this->source->escapeField($string);
    }

    public function query($query, $parameters = null)
    {
        $statement = $this->source->query($query, $parameters);
        return $statement;
    }


    public function queryAndFetch($query, $parameters = null)
    {

        $statement = $this->query($query, $parameters);


        $returnValues = array();
        if ($statement) {
            while ($row = $statement->fetchAssoc()) {
                $returnValues[] = $row;
            }
        }
        return $returnValues;
    }

    public function queryAndFetchOne($query, $parameters = null)
    {

        $returnValues = array();
        $statement = $this->query($query, $parameters);
        if ($statement) {
            $returnValues = $statement->fetchAssoc();
        }
        return $returnValues;
    }

    public function queryAndFetchValue($query, $parameters = null)
    {
        $row = $this->queryAndFetchOne($query, $parameters);
        if (!empty($row)) {
            return reset($row);
        } else {
            return false;
        }
    }

    public function getLastInsertId($name = null)
    {
        return $this->source->lastInsertId($name);
    }




}




