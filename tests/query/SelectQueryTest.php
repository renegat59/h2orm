<?php
namespace H2Orm\tests\query;

use H2Orm\query\SelectQuery;
use PHPUnit\Framework\TestCase;

/**
 * Description of SelectQueryTest
 *
 * @author Mateusz P <bananq@gmail.com>
 */
class SelectQueryTest extends TestCase
{
    /**
     * @var SelectQuery
     */
    private $selectQuery;

    public function setUp()
    {
        parent::setUp();
        //todo: replace null with dbconnection
        $this->selectQuery = new SelectQuery(null);
    }

    public function testSelect()
    {
        $this->assertNotNull($this->selectQuery->select('field1, field2'));
        $this->assertEquals('SELECT field1, field2 FROM', $this->selectQuery->getQuery());
        $this->selectQuery->select('field3');
        $this->assertEquals('SELECT field3 FROM', $this->selectQuery->getQuery());
    }

    public function testFrom()
    {
        $this->assertNotNull($this->selectQuery->select('field1, field2')->from('table1'));
        $this->assertEquals('SELECT field1, field2 FROM table1', $this->selectQuery->getQuery());
        $this->selectQuery->from('table2');
        $this->assertEquals('SELECT field1, field2 FROM table2', $this->selectQuery->getQuery());
    }

    public function testGetQueryOrderByAndLimit()
    {
        $query = $this->selectQuery
            ->select('field1, field2')
            ->from('table1')
            ->where('a=b')
            ->orderBy('field3')
            ->limit(3)
            ->groupBy('field2')
            ->getQuery();
        $this->assertEquals(
            'SELECT field1, field2 FROM table1 WHERE a=b GROUP BY field2 ORDER BY field3 LIMIT 3',
            $query
        );
    }

    public function testGetQueryWithParams()
    {
        $query = $this->selectQuery
            ->select('field1, field2')
            ->from('table1')
            ->where('a=:a', [':a'=>1])
            ->orderBy('field3')
            ->limit(3)
            ->groupBy('field2')
            ->getQuery();
        $this->assertEquals(
            'SELECT field1, field2 FROM table1 WHERE a=:a GROUP BY field2 ORDER BY field3 LIMIT 3',
            $query
        );
    }

    public function testGroupByHaving()
    {
        $query = $this->selectQuery
            ->select('count(field1)')
            ->from('table1')
            ->groupBy('field2')
            ->having('field2>:val1', [':val1'=>0])
            ->getQuery();
        $this->assertEquals('SELECT count(field1) FROM table1 GROUP BY field2 HAVING field2>:val1', $query);
    }

    public function testSelectJoins()
    {
        $query = $this->selectQuery
            ->select('field1, field2')
            ->from('table1 t1')
            ->leftJoin('table2 t2 ON t1.id=t2.t_id')
            ->rightJoin('table3 t3 ON t1.id=t3.t_id')
            ->where('a=:a', [':a'=>1])
            ->orderBy('field3')
            ->getQuery();
        $this->assertEquals(
            'SELECT field1, field2 FROM table1 t1 LEFT JOIN table2 t2 ON t1.id=t2.t_id '
            . 'RIGHT JOIN table3 t3 ON t1.id=t3.t_id WHERE a=:a ORDER BY field3',
            $query
        );
    }

    public function testPrepare()
    {
        $this->selectQuery->prepare('SELECT * FROM table');
        $this->assertEquals('SELECT * FROM table', $this->selectQuery->getQuery());
    }
}
