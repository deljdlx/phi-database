<?php
namespace Phi\Database\Interfaces;


interface Driver
{


    public function escape($value);

    public function escapeField($fieldName);

    public function query($query, $parameters = null);


    public function getLastInsertId();


    public function getError();



    //public function commit();
    //public function autocommit($value=null);


}




