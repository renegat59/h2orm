<?php

namespace H2Orm\query;

/**
 * Description of Select
 *
 * @author Mateusz P <bananq@gmail.com>
 */
class SelectQuery extends Query
{
    private $fields;
    private $havingCondition;
    private $joins = [];

    public function select(string $fields): SelectQuery
    {
        $this->fields = $fields;
        return $this;
    }

    public function from(string $table): SelectQuery
    {
        $this->table = $table;
        return $this;
    }

    public function having(string $condition, array $params): Query
    {
        $this->havingCondition = $condition;
        return $this->addParams($params);
    }

    public function join(string $joinTable): Query
    {
        $this->addJoin('INNER JOIN', $joinTable);
        return $this;
    }

    public function leftJoin(string $joinTable): Query
    {
        $this->addJoin('LEFT JOIN', $joinTable);
        return $this;
    }

    public function rightJoin(string $joinTable): Query
    {
        $this->addJoin('RIGHT JOIN', $joinTable);
        return $this;
    }

    public function fullOuterJoin(string $joinTable): Query
    {
        $this->addJoin('FULL OUTER JOIN', $joinTable);
        return $this;
    }

    /**
     * Executes the select statement and returns array of associative arrays
     */
    public function execute(): array
    {
        $pdoStatement = $this->pdoStatement();
        return $pdoStatement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Executes the select statement and returns array of objects of the given type
     * @param type $class
     */
    public function executeAs(string $class): array
    {
        $pdoStatement = $this->pdoStatement();
        return $pdoStatement->fetchAll(\PDO::FETCH_CLASS, $class);
    }

    private function pdoStatement()
    {
        $pdoStatement = $this->dbConnection->prepare($this->getQuery());
        $pdoStatement->execute($this->params);
        return $pdoStatement;
    }

    /**
     * Executes the select statement and returns value of the first column of the first row
     */
    public function executeScalar()
    {
//        $pdoQuery = $this->dbConnection->prepare($this->getQuery());
//        $pdoQuery->execute($this->params);
    }

    private function addJoin(string $joinType, string $table)
    {
        $this->joins[] = $joinType.' '.$table;
    }

    protected function buildQuery()
    {
        $query = 'SELECT '.$this->fields.' FROM '.$this->table.' ';
        $query .= $this->buildJoins();
        $query .= $this->buildWhereClause();
        $query .= $this->buildGroupBy();
        $query .= $this->buildHaving();
        $query .= $this->buildOrderBy();
        $query .= $this->buildLimit();
        return trim($query);
    }

    private function buildJoins(): string
    {
        if (!empty($this->joins)) {
            return implode(' ', $this->joins).' ';
        }
        return '';
    }

    private function buildHaving(): string
    {
        if (!empty($this->havingCondition)) {
            return 'HAVING '.$this->havingCondition.' ';
        }
        return '';
    }
}
