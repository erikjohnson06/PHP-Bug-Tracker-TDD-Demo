<?php

declare(strict_types = 1);

namespace App\Database;

use App\Exception\InvalidArgumentException;
use PDO;

/**
 * PDOQueryBuilder
 *
 * @author erikjohnson06
 */
class PDOQueryBuilder extends QueryBuilder {

    public function get() : array {
        return $this->statement && $this->statement->rowCount() ? $this->statement->fetchAll() : [];
    }
    
    public function count() : int {
        return $this->statement && $this->statement->rowCount() ? $this->statement->rowCount() : 0;
    }
    
    public function lastInsertedId(){
        return $this->connection->lastInsertId();
    }
    
    public function prepare(string $query) {
        return $this->connection->prepare($query);
    }
    
    public function execute($statement) {
        
        if (!$statement){
            throw new InvalidArgumentException("PDO statement not found.");
        }
        
        $statement->execute($this->bindings);
        
        $this->bindings = [];
        $this->placeholders = [];
        
        return $statement;
    }

    public function fetchInto(string $className) {
        return $this->statement->fetchAll(PDO::FETCH_CLASS, $className);
    }
    
    public function beginTransaction() {
        $this->connection->beginTransaction();
    }

    public function affectedRows() {
        return $this->count();
    }

    public function turnAutoCommitOff() {
        
    }
}
