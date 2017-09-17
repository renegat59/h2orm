<?php

namespace H2Orm\query;

/**
 * Description of INsert
 *
 * @author Mateusz P <bananq@gmail.com>
 */
class InsertQuery extends Query
{
    private $values;

    public function insertInto(string $table): Query
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Set the values to insert
     * @param array $values associative array of fields and values
     * @return \FTwo\db\Query
     */
    public function values(array $values): Query
    {
        $this->values = $values;
        return $this;
    }

    protected function buildQuery()
    {
        //insert into post (field1, field2) values (:val1, :val2);
        $query = 'INSERT INTO '.$this->table.' ';
        $query .= $this->buildValues();
        return trim($query);
    }

    private function buildValues()
    {
        $fields = array_keys($this->values);
        $values = array_map(
            function ($value) {
                return ':'.$value;
            },
            $fields
        );
        return '('.implode(', ', $fields).') VALUES ('.implode(', ', $values).')';
    }

    /**
     * Executes the Insert Statement and returns number of rows affected.
     * //TODO: Wouldn't it be better to return the ID of the inserted element?
     */
    public function execute()
    {
        $pdoStatement = $this->dbConnection->prepare($this->getQuery());
        $pdoStatement->exec($this->params);
        return $pdoStatement->lastInsertId();
    }
}
