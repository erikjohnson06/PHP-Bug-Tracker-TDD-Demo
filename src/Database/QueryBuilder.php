<?php

declare(strict_types=1);

namespace App\Database;

use App\Contracts\DatabaseConnectionInterface;
use App\Exception\InvalidArgumentException;

/**
 * QueryBuilder
 *
 * @author erikjohnson06
 */
abstract class QueryBuilder {

    /**
     *
     * @var PDO|mysqli
     */
    protected $connection;
    protected $statement;
    protected string $table = "";
    protected string $fields = "";
    protected array $placeholders = [];
    protected array $bindings = [];

    /**
     * Database Manipulation Language (dml)
     * SELECT, UPDATE, INSERT, DELETE
     * @var string
     */
    protected string $operation = self::DML_TYPE_SELECT;

    const OPERATORS = ["=", ">=", ">", "<=", "<", "<>"];
    const PLACEHOLDER = "?";
    const COLUMNS = "*";
    const DML_TYPE_SELECT = "SELECT";
    const DML_TYPE_INSERT = "INSERT";
    const DML_TYPE_UPDATE = "UPDATE";
    const DML_TYPE_DELETE = "DELETE";

    use Query;

    /**
     * @param DatabaseConnectionInterface $databaseConnection
     */
    public function __construct(DatabaseConnectionInterface $databaseConnection) {
        $this->connection = $databaseConnection->getConnection();
    }

    /**
     * Set the current working table
     * 
     * @param string $table
     * @return self
     */
    public function table(string $table): self {

        $this->table = $table;

        return $this;
    }

    /**
     * Set a default value for the where clause and parse the column/value key pairs
     * 
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @return self
     * @throws InvalidArgumentException
     */
    public function where(string $column, $operator = self::OPERATORS[0], $value = null): self {

        if (!in_array($operator, self::OPERATORS)) {
            if ($value === null) {
                $value = $operator;
                $operator = self::OPERATORS[0];
            } else {
                throw new InvalidArgumentException("Operator is not valid", ["operator" => $operator]);
            }
        }

        $this->parseWhere([$column => $value], $operator);

        return $this;
    }

    /**
     * Parse an array of conditions and set the placeholders and bindings for the prepared statement
     * 
     * @param array $conditions
     * @param string $operator
     * @return self
     */
    private function parseWhere(array $conditions, string $operator): self {

        //Seperate columns from values to use in a prepared statement
        foreach ($conditions as $column => $value) {
            //column = ?
            $this->placeholders[] = sprintf("%s %s %s", $column, $operator, self::PLACEHOLDER);

            $this->bindings[] = $value;
        }

        return $this;
    }

    /**
     * Select 
     * 
     * @param string $fields
     * @return self
     */
    public function select(string $fields = self::COLUMNS): self {

        $this->operation = self::DML_TYPE_SELECT;

        $this->fields = $fields;

        return $this;
    }

    /**
     * Create/Insert
     * 
     * @param array $data
     * @return int
     */
    public function create(array $data) : int {

        $this->fields = "`" . implode("`, `", array_keys($data)) . "`";

        foreach ($data as $value) {
            $this->placeholders[] = self::PLACEHOLDER;
            $this->bindings[] = $value;
        }

        $query = $this->prepare($this->getQuery(self::DML_TYPE_INSERT));
        $this->statement = $this->execute($query);

        return (int) $this->lastInsertedId();
    }

    /**
     * Update
     * 
     * @param array $data
     * @return self
     */
    public function update(array $data): self {

        $fields = [];

        $this->operation = self::DML_TYPE_UPDATE;

        foreach ($data as $column => $value) {
            $fields[] = sprintf("%s%s%s", $column, self::OPERATORS[0], "'" . $value . "'");
        }

        if ($fields) {
            $this->fields = implode(", ", $fields);
        }

        return $this;
    }

    /**
     * Delete
     * 
     * @return self
     */
    public function delete(): self {
        $this->operation = self::DML_TYPE_DELETE;
        return $this;
    }

    /**
     * Perform a raw query
     * 
     * @param string $query
     * @return self
     */
    public function raw(string $query): self {

        $query = $this->prepare($query);

        $this->statement = $this->execute($query);

        return $this;
    }

    public function find($id) {
        return $this->where("id", $id)->runQuery()->first();
    }

    public function findOneBy(string $field, $value) {
        return $this->where($field, $value)->runQuery()->first();
    }

    public function first() {
        return $this->count() ? $this->get()[0] : null;
    }

    public function rollback(): void {
        $this->connection->rollback();
    }

    public function runQuery(): self {

        $query = $this->prepare($this->getQuery($this->operation));

        $this->statement = $this->execute($query);

        return $this;
    }

    abstract public function get();

    abstract public function count();

    abstract public function lastInsertedId();

    abstract public function prepare(string $query);

    abstract public function execute($statement);

    abstract public function fetchInto(string $className);

    abstract public function beginTransaction();

    abstract public function affectedRows();

    abstract public function turnAutoCommitOff();
}
