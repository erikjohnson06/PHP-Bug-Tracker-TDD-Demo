<?php

namespace App\Contracts;

/**
 * DatabaseConnectionInterface
 *
 * @author erikjohnson06
 */
interface DatabaseConnectionInterface {
    
    public function connect();
    
    public function getConnection();
}
