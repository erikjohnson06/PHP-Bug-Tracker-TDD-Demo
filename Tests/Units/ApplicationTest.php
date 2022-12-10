<?php

namespace Tests\Units;

use App\Helpers\App;
use PHPUnit\Framework\TestCase;

/**
 * ApplicationTest
 *
 * @author erikjohnson06
 */
class ApplicationTest extends TestCase {
    
    public function testInstanceOfApplicationCanBeLoaded(){
        self::assertInstanceOf(App::class, new App);
    }
    
    public function testBasicApplicationDatasetCanBeLoaded(){
        
        $application = new App;
        
        self::assertTrue($application->isRunningFromConsole(), true);
        self::assertSame("test", $application->getEnvironment());
        self::assertNotNull($application->getLogPath());
        self::assertInstanceOf(\DateTime::class, $application->getServerTime());
    }
}
