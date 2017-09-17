<?php

namespace H2Orm\query;

/**
 * Description of Delete
 *
 * @author Mateusz P <bananq@gmail.com>
 */
class DeleteQuery extends Query
{

    public function from(string $table): Query
    {
        $this->table = $table;
        return $this;
    }

    protected function buildQuery()
    {
        $query = 'DELETE FROM '.$this->table.' ';
        $query .= $this->buildWhereClause();
        $query .= $this->buildOrderBy();
        $query .= $this->buildLimit();
        return trim($query);
    }

    /**
     * Executes the delete query and returns number of rows affected
     */
    public function execute()
    {
        $pdoStatement = $this->dbConnection->prepare($this->getQuery());
        $pdoStatement->execute($this->params);
        return $pdoStatement->rowCount();
    }
}
