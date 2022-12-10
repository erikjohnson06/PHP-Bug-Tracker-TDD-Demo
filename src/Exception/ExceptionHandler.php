<?php

declare(strict_types=1);

namespace App\Exception;

use App\Helpers\App;
use Throwable;
use ErrorException;

/**
 * ExceptionHandler
 *
 * @author erikjohnson06
 */
class ExceptionHandler {
    
    public function handle(Throwable $exception) : void {
        
        $application = new App;
        
        if ($application->isDebugMode()){
            var_dump($exception);
        }
        else {
            echo "An unexcepted error has occurred. Please notify an administrator";
        }
        
        exit();
    }
    
    public function convertWarningAndNoticesToException($severity, $message, $file, $line){
        throw new ErrorException($message, $severity,  $severity, $file, $line);
    }
}
