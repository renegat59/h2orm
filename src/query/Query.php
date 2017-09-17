<?php

namespace H2Orm\query;

/**
 * Description of Query
 *
 * @author Mateusz P <bananq@gmail.com>
 */
abstract class Query
{
    /**
     * @var \PDOConnection
     */
    protected $dbConnection;
    protected $table;
    protected $whereClause;
    protected $params = [];
    protected $orderBy;
    protected $groupBy;
    protected $limit;
    protected $plainQuery;

    abstract protected function buildQuery();
    /**
     * Executes a query. This function will behave diffrently and return different things depending on Query type
     */
    abstract public function execute();

    public function __construct($connection)
    {
        $this->dbConnection = $connection;
    }

    public function setDbConnection(DbConnection $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function where(string $condition, array $params = []): Query
    {
        $this->whereClause = $condition;
        return $this->addParams($params);
    }

    public function andWhere(string $condition, array $params = []): Query
    {
        return $this->addWhere($condition, 'AND')
            ->addParams($params);
    }

    public function orWhere(string $condition, array $params = []): Query
    {
        return $this->addWhere($condition, 'OR')
            ->addParams($params);
    }

    private function addWhere(string $whereClause, string $operator)
    {
        if (!empty($this->where)) {
            throw new \FTwo\core\exceptions\F2Exception('where() not called before');
        }
        if (!empty($whereClause)) {
            $this->whereClause = '('.$this->whereClause.') '.$operator.' '.$whereClause;
        }
        return $this;
    }

    public function orderBy(string $order): Query
    {
        $this->orderBy = $order;
        return $this;
    }

    public function groupBy(string $group): Query
    {
        $this->groupBy = $group;
        return $this;
    }

    public function limit(string $limit, array $params = []): Query
    {
        $this->limit = $limit;
        return $this->addParams($params);
    }

    /**
     * Gets the formed query instead of executing it.
     * @return string formed query
     */
    public function getQuery(): string
    {
        return $this->plainQuery ?? $this->buildQuery();
    }

    /**
     * Executes the query and returns one result. It applies "limit 1" explicitly.
     * Returns the result or null if result set is empty
     * @return type found element or null
     */
    public function executeOne()
    {
        $this->limit(1);
        $result = $this->execute();
        return $result[0] ?? null;
    }

    public function executeAs(string $className)
    {
    }

    /**
     * Executes the query and returns one result casted to the given class. It applies "limit 1" explicitly.
     * Returns the result or null if result set is empty
     * @return type found element casted to given object or null if result is empty
     */
    public function executeOneAs(string $className)
    {
        $this->limit(1);
        $result = $this->executeAs($className);
        return $result[0] ?? null;
    }

    protected function buildWhereClause()
    {
        if (!empty($this->whereClause)) {
            return 'WHERE '.$this->whereClause.' ';
        }
        return '';
    }

    protected function buildOrderBy()
    {
        if (!empty($this->orderBy)) {
            return 'ORDER BY '.$this->orderBy.' ';
        }
        return '';
    }

    protected function buildLimit()
    {
        if (!empty($this->limit)) {
            return 'LIMIT '.$this->limit.' ';
        }
        return '';
    }

    protected function buildGroupBy()
    {
        if (!empty($this->groupBy)) {
            return 'GROUP BY '.$this->groupBy.' ';
        }
        return '';
    }

    protected function addParams(array $params): Query
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    protected function resultToClass(array $result, string $class)
    {
        $object = new $class();
        foreach ($result as $key => $value) {
            $object->{$key} = $value;
        }
        return $object;
    }

    /**
     * If the API does not support the desired functionality, we can simply pass the SQL here
     * @param type $query
     * @return $this
     */
    public function prepare(string $query, array $params = []): Query
    {
        $this->plainQuery = $query;
        return $this->addParams($params);
    }
}
