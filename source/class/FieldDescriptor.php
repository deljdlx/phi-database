<?php

namespace Phi\Database;


use Phi\Traits\Collection;

class FieldDescriptor implements \ArrayAccess, \JsonSerializable
{

    use Collection;


    protected $name;

    protected $isPrimaryKey = false;
    protected $isLabel = false;
    protected $defaultValue = null;




    public function loadFromSQLite($values)
    {

        if($values['pk'] == 1) {
            $this->isPrimaryKey = true;
        }

        $this->defaultValue = $values['dflt_value'];

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

    public function isLabel($value = null)
    {
        if($value !== null) {
            $this->isLabel = $value;
        }
        return $this->isLabel;
    }

    public function isPrimaryKey()
    {

        return $this->isPrimaryKey;
    }

    public function jsonSerialize()
    {
        return $this->getVariables();
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }


}




