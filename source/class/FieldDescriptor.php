<?php

namespace Phi\Database;


use Phi\Traits\Collection;

class FieldDescriptor implements \ArrayAccess, \JsonSerializable
{

    use Collection;


    protected $name;

    protected $isPrimaryKey = false;




    public function loadFromSQLite($values)
    {

        if($values['pk'] == 1) {
            $this->isPrimaryKey = true;
        }

        $this->setVariables($values);
        $this->name = $values['name'];
        return $this;
    }

    public function loadFromMySQL($values)
    {


        if($values['Key'] == 'PRI') {
            $this->isPrimaryKey = true;
        }


        $this->setVariables($values);
        $this->name = $values['Field'];
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function isLabel()
    {
        return false;
    }

    public function isPrimaryKey()
    {

        return $this->isPrimaryKey;
    }

    public function jsonSerialize()
    {
        return $this->getVariables();
    }


}




