<?php

declare(strict_types = 1);

namespace Tests\Units;

use App\Database\QueryBuilder;
use App\Entity\BugReport;
use App\Helpers\DBQueryBuilderFactory;
use App\Repository\BugReportRepository;
use PHPUnit\Framework\TestCase;

/**
 * RepositoryTest
 *
 * @author erikjohnson06
 */
class RepositoryTest extends TestCase {

    /**
     * @var QueryBuilder $queryBuilder
     */
    protected $queryBuilder;

    /**
     * @var BugReportRepository $bugReportRepository
     */
    protected $bugReportRepository;

    public function setUp(): void {

        $this->queryBuilder = DBQueryBuilderFactory::make("database", "pdo", ['db_name' => 'bug_tracker_testing']); //

        $this->queryBuilder->beginTransaction();

        $this->bugReportRepository = new BugReportRepository($this->queryBuilder);

        parent::setUp();
    }

    public function tearDown(): void {

        $this->queryBuilder->rollback();

        parent::tearDown();
    }

    private function createBugReport(): BugReport {

        $bugReport = new BugReport();
        $bugReport->setReportType("Type 2")
                ->setLink("https://testing-link.com")
                ->setMessage("Dummy message")
                ->setEmail("email@test.com");

        return $this->bugReportRepository->create($bugReport);
    }

    public function testCanCreateRecordWithEntity() {

        $newBugReport = $this->createBugReport();

        self::assertInstanceOf(BugReport::class, $newBugReport);
        self::assertSame("Type 2", $newBugReport->getReportType());
        self::assertSame("https://testing-link.com", $newBugReport->getLink());
        self::assertSame("Dummy message", $newBugReport->getMessage());
        self::assertSame("email@test.com", $newBugReport->getEmail());
        self::assertNotNull($newBugReport->getId());
    }

    public function testCanUpdateRecordWithEntity() {

        $newBugReport = $this->createBugReport();

        $bugReport = $this->bugReportRepository->find($newBugReport->getId());
        $bugReport->setMessage("Updated message")->setLink("https://testing-link-new.com");

        $updatedReport = $this->bugReportRepository->update($bugReport);

        self::assertInstanceOf(BugReport::class, $updatedReport);
        self::assertSame("https://testing-link-new.com", $updatedReport->getLink());
        self::assertSame("Updated message", $updatedReport->getMessage());
    }

    public function testCanDeleteRecordWithEntity() {

        $newBugReport = $this->createBugReport();

        $this->bugReportRepository->delete($newBugReport);

        $bugReport = $this->bugReportRepository->find($newBugReport->getId());

        self::assertNull($bugReport);
    }
    
    public function testCanFindRecordByCriteria() {

        $this->createBugReport();

        $report = $this->bugReportRepository->findBy([
            ["report_type", "=", "Type 2"],
            ["email", "=", "email@test.com"]
        ]);
        

        self::assertIsArray($report);
        self::assertIsArray($report);
        
        /**
         * @var BugReport $bugReport
         */
        $bugReport = $report[0];
        self::assertSame("Type 2", $bugReport->getReportType());
        self::assertSame("email@test.com", $bugReport->getEmail());
    }

}
