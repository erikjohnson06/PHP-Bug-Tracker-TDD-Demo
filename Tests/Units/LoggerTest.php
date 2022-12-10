<?php

namespace Tests\Units;

use App\Contracts\LoggerInterface;
use App\Exception\InvalidLogLevelArgument;
use App\Helpers\App;
use App\Logger\Logger;
use App\Logger\LogLevel;
use PHPUnit\Framework\TestCase;

/**
 * Description of LoggerTest
 *
 * @author erikjohnson06
 */
class LoggerTest extends TestCase {
    
    /**
     * @var Logger $Logger
     */
    private Logger $Logger;
    
    public function setUp(): void {
        
        $this->Logger = new Logger;
        parent::setUp();
    }
    
    public function testImplementsLoggerInterface(){
        self::assertInstanceOf(LoggerInterface::class, new Logger);
    }
    
    public function testLoggerCanCreateDifferentTypesOfLogLevels(){
        
        $this->Logger->info("Testing Info Logs");
        $this->Logger->error("Testing Error Logs");
        $this->Logger->log(LogLevel::ALERT, "Testing Alert Logs");
        
        $application = new App;
        
        $fileName = sprintf("%s/%s-%s.log", $application->getLogPath(), $application->getEnvironment(), date("Y.n.j"));
        
        self::assertFileExists($fileName);
        
        $contentOfLogFile = file_get_contents($fileName);
        
        self::assertStringContainsString("Testing Info Logs", $contentOfLogFile);
        self::assertStringContainsString("Testing Error Logs", $contentOfLogFile);
        self::assertStringContainsString(LogLevel::ALERT, $contentOfLogFile);
        
        unlink($fileName);
        
        self::assertFileNotExists($fileName);
    }
    
    public function testLoggerThrowsInvalidLogLevelArgumentWhenGivenInvalidLogLevel(){
        
        self::expectException(InvalidLogLevelArgument::class);
        
        $this->Logger->log("Invalid Log Level", "Testing Invalid Log Level");
    }
}
