<?php

declare(strict_types = 1);

namespace Tests\Functional;

use App\Database\QueryBuilder;
use App\Entity\BugReport;
use App\Helpers\DBQueryBuilderFactory;
use App\Helpers\HttpClient;
use App\Repository\BugReportRepository;
use PHPUnit\Framework\TestCase;

/**
 * CRUDTest
 *
 * @author erikjohnson06
 */
class CRUDTest extends TestCase {

    /**
     * @var QueryBuilder $queryBuilder
     */
    protected $queryBuilder;
    
    /**
     * @var BugReportRepository $repository
     */
    private $repository;
    
    /**
     * @var HttpClient $client
     */
    private $client;
    
    private string $url = "http://localhost/BugTrackerApp/index.php";
    
    public function setUp() : void {
                        
        $this->queryBuilder = DBQueryBuilderFactory::make();

        $this->queryBuilder->beginTransaction();

        $this->repository = new BugReportRepository($this->queryBuilder);
        
        $this->client = new HttpClient();
        
        parent::setUp();
    }
        
    private function getPostData(array $options = []) : array {
        return array_merge([
            "reportType" => "Post Test", 
            "message" => "Post Test Message", 
            "email" => "test@example.com", 
            "link" => "https://www.example.com"
            
        ], $options);
    }
    
    public function testCanCreateReportUsingPostRequest(){
        
        $postData = $this->getPostData(["create" => true]);
        
        $response = $this->client->post($this->url, $postData);
        $response = json_decode($response, true);
                
        self::assertEquals(200, $response['statusCode']);
        
        $result = $this->repository->findBy([
            ["report_type", "=", "Post Test"],
            ["email", "=", "test@example.com"],
            ["link", "=", "https://www.example.com"]
        ]);
        
        /** @var BugReport $bugReport */
        $bugReport = $result[0] ?? [];
        
        self::assertInstanceOf(BugReport::class, $bugReport);
        self::assertSame("Post Test", $bugReport->getReportType());
        self::assertSame("https://www.example.com", $bugReport->getLink());
        self::assertSame("test@example.com", $bugReport->getEmail());
        
        return $bugReport;
    }
    
    /**
     * @depends testCanCreateReportUsingPostRequest
     * @param BugReport $bugReport
     * @return BugReport
     */
    public function testCanUpdateReportUsingPostRequest(BugReport $bugReport){
        
        $postData = $this->getPostData([
            "update" => true,
            "message" => "Post Test Message update", 
            "link" => "https://www.updated.com",
            "reportId" => $bugReport->getId()
            ]);
        
        $response = $this->client->post($this->url, $postData);
        $response = json_decode($response, true);
        
        self::assertEquals(200, $response['statusCode']);
        
        /** @var BugReport $result */
        $result = $this->repository->find($bugReport->getId());
                
        self::assertInstanceOf(BugReport::class, $result);
        self::assertSame("https://www.updated.com", $result->getLink());
        self::assertSame("Post Test Message update", $result->getMessage());
        
        return $result;
    }
    
    /**
     * @depends testCanUpdateReportUsingPostRequest
     * @param BugReport $bugReport
     * @return void
     */
    public function testCanDeleteReportUsingPostRequest(BugReport $bugReport){
        
        $postData = [
            "delete" => true,
            "reportId" => $bugReport->getId()
            ];
        
        $response = $this->client->post($this->url, $postData);
        $response = json_decode($response, true);
        
        self::assertEquals(200, $response['statusCode']);
        
        /** @var BugReport $result */
        $result = $this->repository->find($bugReport->getId());
                
        self::assertNull($result);
    }
}
