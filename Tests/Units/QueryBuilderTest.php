<?php

declare(strict_types = 1);

namespace Tests\Units;

//use App\Database\MySQLiConnection;
//use App\Database\MySQLiQueryBuilder;
//use App\Database\PDOConnection;
//use App\Database\PDOQueryBuilder;
use App\Database\QueryBuilder;
//use App\Helpers\Config;
use App\Helpers\DBQueryBuilderFactory;
use PHPUnit\Framework\TestCase;

/**
 * QueryBuilderTest
 *
 * @author erikjohnson06
 */
class QueryBuilderTest extends TestCase {

    /**
     *
     * @var QueryBuilder
     */
    protected $queryBuilder;

    public function setUp(): void {

        $this->queryBuilder = DBQueryBuilderFactory::make("database", "mysqli", ['db_name' => 'bug_tracker_testing']); //
        //$this->queryBuilder->turnAutoCommitOff();
        $this->queryBuilder->beginTransaction();

        parent::setUp();
    }

    public function tearDown(): void {

        //Clear table after each test
        //$this->queryBuilder->raw("DELETE FROM reports WHERE id > 0")->get();

        $this->queryBuilder->rollback();

        parent::tearDown();
    }

    private function insertIntoTable() {

        $data = [
            "report_type" => "Report Type 1",
            "message" => "Message",
            "link" => "link",
            "email" => "email",
            "created_at" => date("Y-m-d H:i:s")
        ];

        $id = $this->queryBuilder->table("reports")->create($data);

        return $id;
    }

    public function testCanCreateRecords() {

        $id = $this->insertIntoTable();
        self::assertNotNull($id);
    }

    public function testCanPerformRawQuery() {

        $id = $this->insertIntoTable();

        $result = $this->queryBuilder->raw("SELECT * FROM reports")->get();
        self::assertNotNull($result);
    }

    public function testCanPerformSelectQuery() {

        $id = $this->insertIntoTable();

        $result = $this->queryBuilder
                ->table("reports")
                ->select("*")
                ->where("id", $id)
                ->runQuery()
                ->first();

        self::assertNotNull($result);
        self::assertSame($id, (int) $result->id);
    }

    public function testCanPerformSelectQueryWithMultipleWhereClause() {

        $id = $this->insertIntoTable();
        $result = $this->queryBuilder
                ->table("reports")
                ->select("*")
                ->where("id", $id)
                ->where("report_type", "=", "Report Type 1")
                ->runQuery()
                ->first();

        self::assertNotNull($result);
        self::assertSame($id, (int) $result->id);
        self::assertSame("Report Type 1", $result->report_type);
    }

    public function testCanFindById() {

        $id = $this->insertIntoTable();

        $result = $this->queryBuilder
                ->table("reports")
                ->select("*")
                ->find($id);

        self::assertNotNull($result);
        self::assertSame($id, (int) $result->id);
        self::assertSame("Report Type 1", $result->report_type);
    }

    public function testCanFindByGivenValues() {

        $id = $this->insertIntoTable();

        $result = $this->queryBuilder
                ->table("reports")
                ->select("*")
                ->findOneBy("report_type", "Report Type 1");

        self::assertNotNull($result);
        self::assertSame($id, (int) $result->id);
        self::assertSame("Report Type 1", $result->report_type);
    }

    public function testCanUpdateByGivenRecord() {

        $id = $this->insertIntoTable();

        $count = $this->queryBuilder
                ->table("reports")
                ->update(["report_type" => "Report Type 1 updated"])
                ->where("id", $id)
                ->runQuery()
                ->affectedRows();

        self::assertEquals(1, $count);

        $result = $this->queryBuilder
                ->table("reports")
                ->select("*")
                ->findOneBy("id", $id);

        self::assertNotNull($result);
        self::assertSame($id, (int) $result->id);
        self::assertSame("Report Type 1 updated", $result->report_type);
    }

    public function testCanDeleteByGivenId() {

        $id = $this->insertIntoTable();

        $count = $this->queryBuilder
                ->table("reports")
                ->delete()
                ->where("id", $id)
                ->runQuery()
                ->affectedRows();

        self::assertEquals(1, $count);

        $result = $this->queryBuilder
                ->table("reports")
                ->select("*")
                ->find("id", $id);

        self::assertNull($result);
    }
}
