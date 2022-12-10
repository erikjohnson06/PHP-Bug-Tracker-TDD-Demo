<?php

namespace App\Logger;

use App\Contracts\LoggerInterface;
use App\Helpers\App;
use App\Exception\InvalidLogLevelArgument;
use ReflectionClass;


/**
 * Logger
 *
 * @author erikjohnson06
 */
class Logger implements LoggerInterface {
    
    
    public function alert(string $message, array $context = []) {
        $this->addRecord(LogLevel::ALERT, $message, $context);
    }

    public function critical(string $message, array $context = []) {
        $this->addRecord(LogLevel::CRITICAL, $message, $context);
    }

    public function debug(string $message, array $context = []) {
        $this->addRecord(LogLevel::DEBUG, $message, $context);
    }

    public function emergency(string $message, array $context = []) {
        $this->addRecord(LogLevel::EMERGENCY, $message, $context);
    }

    public function error(string $message, array $context = []) {
        $this->addRecord(LogLevel::ERROR, $message, $context);
    }

    public function info(string $message, array $context = []) {
        $this->addRecord(LogLevel::INFO, $message, $context);
    }

    public function notice(string $message, array $context = []) {
        $this->addRecord(LogLevel::NOTICE, $message, $context);
    }

    public function warning(string $message, array $context = []) {
        $this->addRecord(LogLevel::WARNING, $message, $context);
    }

    public function log(string $level, string $message, array $context = []) {
        
        $object = new ReflectionClass(LogLevel::class);
        
        $validLogLevelArray = $object->getConstants();
        
        if (!in_array($level, $validLogLevelArray)){
            throw new InvalidLogLevelArgument($level, $validLogLevelArray);
        }
        
        $this->addRecord($level, $message, $context);
    }
    
    private function addRecord(string $level, string $message, array $context = []){
        
        $application = new App;
        $date = $application->getServerTime()->format("Y-m-d H:i:s");
        $logPath = $application->getLogPath();
        $env = $application->getEnvironment();
        $details = sprintf(
                "%s - Level: %s - Message %s - Context: %s",
                $date, 
                $level, 
                $message, 
                json_encode($context)
                ) . PHP_EOL;
        
        $fileName = sprintf("%s/%s-%s.log", $logPath, $env, date("Y.n.j"));
        
        file_put_contents($fileName, $details, FILE_APPEND);
    }
}
