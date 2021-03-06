<?php

namespace H2Orm\connection;

use H2Orm\exceptions\H2OrmException;
use H2Orm\query\DeleteQuery;
use H2Orm\query\InsertQuery;
use H2Orm\query\SelectQuery;
use H2Orm\query\UpdateQuery;
use PDO;
use PDOStatement;

/**
 * Description of DbConnection
 *
 * @author mateusz
 */
class DbConnection
{
    private $databaseHandler;

    public function __construct($config)
    {
        $this->validateConfig($config);

        $host   = $config['host'];
        $dbName = $config['schema'];
        $port   = $config['port'] ?? 3306;

        $connectionString      = "mysql:host=$host;port=$port;dbname=$dbName";
        $this->databaseHandler = new PDO($connectionString, $config['user'], $config['password']);
    }

    public function close()
    {
        $this->databaseHandler = null;
    }

    public function select(string $fields): SelectQuery
    {
        return (new SelectQuery($this))->select($fields);
    }

    public function insertInto(string $table): InsertQuery
    {
        return (new InsertQuery($this))->insertInto($table);
    }

    public function update(string $table): UpdateQuery
    {
        return (new UpdateQuery($this))->update($table);
    }

    public function delete(string $table): DeleteQuery
    {
        return (new DeleteQuery($this))->delete($table);
    }

    public function prepare(string $query): PDOStatement
    {
        return $this->databaseHandler->prepare($query);
    }

    public function transaction()
    {
        //create and return transaction
    }

    public function commit()
    {
        //commits transaction
    }

    public function rollback()
    {
        //rollbacks transaction
    }

    private function validateConfig($config)
    {
        if (!isset($config['host'])) {
            throw new H2OrmException('Database host not provided');
        }

        if (!isset($config['user'])) {
            throw new H2OrmException('Database user not provided');
        }

        if (!isset($config['password'])) {
            throw new H2OrmException('Database password not provided');
        }

        if (!isset($config['schema'])) {
            throw new H2OrmException('Database schema not provided');
        }
    }
}
