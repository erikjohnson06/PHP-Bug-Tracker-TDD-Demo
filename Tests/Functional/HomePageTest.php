<?php

declare(strict_types = 1);

namespace Tests\Functional;

use App\Helpers\HttpClient;
use PHPUnit\Framework\TestCase;

/**
 * HomePageTest
 *
 * @author erikjohnson06
 */
class HomePageTest extends TestCase {

    public function testCanVisitHomePageAndViewRelevantData(){
        
        $client = new HttpClient();
        
        $response = $client->get("http://localhost/BugTrackerApp/index.php");
        $response = json_decode($response, true);
        
        self::assertEquals(200, $response['statusCode']);
        self::assertStringContainsString("Bug Tracker Application", $response['content']);
        self::assertStringContainsString("<span>Add Report</span>", $response['content']);
    }
}