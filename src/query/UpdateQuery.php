<?php

namespace H2Orm\query;

/**
 * Description of Update
 *
 * @author Mateusz P <bananq@gmail.com>
 */
class UpdateQuery extends Query
{
    private $values;

    public function update($table): Query
    {
        $this->table = $table;
        return $this;
    }

    protected function buildQuery()
    {
        $query = 'UPDATE '.$this->table.' ';
        $query .= $this->buildSetValues();
        $query .= $this->buildWhereClause();
        $query .= $this->buildOrderBy();
        $query .= $this->buildLimit();
        return trim($query);
    }

    public function set(array $values): Query
    {
        $this->values = $values;
        return $this;
    }

    private function buildSetValues()
    {
        return 'SET '.implode(
            ', ',
            array_map(
                function ($field) {
                    return $field.'=:'.$field;
                },
                array_keys($this->values)
            )
        ).' ';
    }

    /**
     * Executes the update query and returns number of rows affected
     */
    public function execute()
    {
        $pdoStatement = $this->dbConnection->prepare($this->getQuery());
        $pdoStatement->execute($this->params);
        return $pdoStatement->rowCount();
    }
}
