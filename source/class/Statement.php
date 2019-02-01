<?php


namespace Phi\Database;

use \Phi\Database\Interfaces\Statement as PhiStatement;

/**
 * Class Statement
 * @property \Phi\Database\Interfaces\Statement driver
 * @package Phi\Database
 */
abstract class Statement
{

    public function __construct()
    {

    }


    abstract public function fetchAssoc();


    public function fetchAll()
    {
        $returnValues = array();

        while ($row = $this->fetchAssoc()) {
            $returnValues[] = $row;
        }
        return $returnValues;
    }



}