<?php
namespace H2Orm\tests\query;

use H2Orm\query\DeleteQuery;
use PHPUnit\Framework\TestCase;

/**
 * Description of DeleteQueryTest
 *
 * @author Mateusz P <bananq@gmail.com>
 */
class DeleteQueryTest extends TestCase
{
    /**
     * @var DeleteQuery
     */
    private $deleteQuery;

    public function setUp()
    {
        parent::setUp();
        //todo: replace null with dbconnection
        $this->deleteQuery = new DeleteQuery(null);
    }

    public function testFrom()
    {
        $this->assertNotNull($this->deleteQuery->from('table1'));
        $this->assertEquals('DELETE FROM table1', $this->deleteQuery->getQuery());
        $this->deleteQuery->from('table2');
        $this->assertEquals('DELETE FROM table2', $this->deleteQuery->getQuery());
    }

    public function testWhere()
    {
        $this->assertNotNull($this->deleteQuery->from('table1')->where('a=:a', [':a'=>1]));
        $this->assertEquals('DELETE FROM table1 WHERE a=:a', $this->deleteQuery->getQuery());
    }

    public function testWhereWithParams()
    {
        $query = $this->deleteQuery->from('table1')
            ->where('a=:a', [':a'=>1])
            ->orderBy('b')
            ->limit(1)
            ->getQuery();
        $this->assertEquals('DELETE FROM table1 WHERE a=:a ORDER BY b LIMIT 1', $query);
    }
}
