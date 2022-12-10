<?php

namespace App\Database;

use App\Exception\InvalidArgumentException;

/**
 * MySQLiQueryBuilder
 *
 * @author erikjohnson06
 */
class MySQLiQueryBuilder extends QueryBuilder {

    private $resultSet;
    private array $results = [];

    const PARAM_TYPE_INT = "i";
    const PARAM_TYPE_STRING = "s";
    const PARAM_TYPE_DOUBLE = "d";

    public function count(): int {

        if (!$this->resultSet) {
            $this->get();
        }

        return $this->resultSet ? (int) $this->resultSet->num_rows : 0;
    }

    public function execute($statement) {

        if (!$statement) {
            throw new InvalidArgumentException("MySQLi statement not found.");
        }

        if ($this->bindings) {
            $bindings = $this->parseBindings($this->bindings);
            $reflectionObj = new \ReflectionClass("mysqli_stmt");
            $method = $reflectionObj->getMethod("bind_param");

            $method->invokeArgs($statement, $bindings);
        }

        $statement->execute();

        $this->bindings = [];
        $this->placeholders = [];

        return $statement;
    }

    private function parseBindings(array $params): array {

        $bindings = [];

        $cnt = count($params);

        if (!$cnt) {
            return $this->bindings;
        }

        $bindingTypes = $this->parseBindingTypes();

        $bindings[] = &$bindingTypes;

        for ($i = 0; $i < $cnt; $i++) {
            $bindings[] = &$params[$i];
        }

        return $bindings;
    }

    /**
     * Convert binding array in to string for MySQLi mapping
     * Ex: ["s", "i", "d", "d"] => "sidd"
     *
     * @return string
     */
    private function parseBindingTypes(): string {

        $bindingTypes = [];

        foreach ($this->bindings as $binding) {

            if (is_int($binding)) {
                $bindingTypes[] = self::PARAM_TYPE_INT;
            }
            if (is_string($binding)) {
                $bindingTypes[] = self::PARAM_TYPE_STRING;
            }
            if (is_float($binding)) {
                $bindingTypes[] = self::PARAM_TYPE_DOUBLE;
            }
        }

        return implode("", $bindingTypes);
    }

    public function fetchInto(string $className): array {

        if (!$className) {
            throw new InvalidArgumentException("Class name not found.");
        }

        $rows = [];

        $this->resultSet = $this->statement->get_result();

        while ($obj = $this->resultSet->fetch_object($className)) {
            $rows[] = $obj;
        }

        return $this->results = $rows;
    }

    public function get(): array {

        $results = [];

        if (!$this->resultSet) {
            
            $this->resultSet = $this->statement->get_result();

            if ($this->resultSet) {
                while ($obj = $this->resultSet->fetch_object()) {
                    $results[] = $obj;
                }
            }

            $this->results = $results;
        }

        return $this->results;
    }

    public function lastInsertedId() {
        return $this->connection->insert_id;
    }

    public function prepare(string $query) {
        return $this->connection->prepare($query);
    }

    public function beginTransaction() {
        $this->connection->begin_transaction();
    }

    public function affectedRows() {

        $this->statement->store_result();

        return $this->statement->affected_rows;
    }

    public function turnAutoCommitOff(): void {
        $this->connection->autocommit(false);
    }
}
