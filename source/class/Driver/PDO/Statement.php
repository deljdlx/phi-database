<?php


namespace Phi\Database\Driver\PDO;
use Phi\Database\Statement as PhiStatement;

class Statement extends PhiStatement implements \Phi\Database\Interfaces\Statement
{

    protected $statement;

    public function getStatement() {
        return $this->statement;
    }

    public function __construct(\PDOStatement $statement)
    {
        $this->statement = $statement;
    }

    public function fetchAssoc()
    {
        return $this->statement->fetch(\PDO::FETCH_ASSOC);
    }

    public function getError()
    {
        return $this->statement->errorInfo ();
    }

}