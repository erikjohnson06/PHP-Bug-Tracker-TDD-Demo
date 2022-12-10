<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Exception\NotFoundException;

/**
 * Config Helper class
 *
 * @author erikjohnson06
 */
class Config {
    
    public static function get(string $filename, ?string $key = null) {
        
        $fileContents = self::getFileContents($filename);
        
        if ($key === null){
            return $fileContents;
        }
        
        return isset($fileContents[$key]) ? $fileContents[$key] : [];
    }
    
    public static function getFileContents(string $filename) : array {
        
        $fileContents = [];
        
        try {
            
            $file = realpath(sprintf(__DIR__ . "/../Config/%s.php", $filename));
            
            if (file_exists($file)){
                $fileContents = require $file;
            }
        } 
        catch (\Throwable $ex) {
            throw new NotFoundException(sprintf("The specified file was not found: %s", $filename), ["file not found", "data is passed"]);
        }
        
        return $fileContents;
    }
}
