<?php

declare(strict_types=1);

namespace App\Repository;

use App\Contracts\RepositoryInterface;
use App\Database\QueryBuilder;
use App\Entity\Entity;

/**
 * Repository
 *
 * @author erikjohnson06
 */
abstract class Repository implements RepositoryInterface {

    private QueryBuilder $queryBuilder;
    
    protected static string $table;
    protected static string $className;
    
    public function __construct(QueryBuilder $queryBuilder){
        
        $this->queryBuilder = $queryBuilder;
    }
    
    public function find(int $id): ?object {
        
        return $this->findOneBy("id", $id);
    }

    public function findAll(): ?array {
        
        /*
        return $this->queryBuilder
                ->table(static::$table)
                ->select()
                ->runQuery()
                ->fetchInto(static::$className);
        */
        
        return $this->queryBuilder
                ->raw("SELECT * FROM " . static::$table)
                ->fetchInto(static::$className);
    }

    public function findBy(array $criteria) {
        
        $this->queryBuilder->table(static::$table)->select();
        
        foreach ($criteria as $criterion){
            $this->queryBuilder->where(...$criterion);
        }
        
        return $this->queryBuilder->runQuery()->fetchInto(static::$className);
    }

    public function findOneBy(string $field, $value): ?object {
        
        $result = $this->queryBuilder
                ->table(static::$table)
                ->select()
                ->where($field, $value)
                ->runQuery()
                ->fetchInto(static::$className);
        
        return ($result) ? $result[0] : null;
    }

    public function sql(string $query) {
        return $this->queryBuilder
                ->raw($query)
                ->fetchInto(static::$className);
    }

    public function create(Entity $entity): object {
        
        $id = $this->queryBuilder
                ->table(static::$table)
                ->create($entity->toArray());
        
        return $this->find($id);
    }
    
    public function update(Entity $entity, array $conditions = []): object {
        
        $id = $this->queryBuilder
                ->table(static::$table)
                ->update($entity->toArray());
        
        if ($conditions){
            foreach ($conditions as $condition){
                $this->queryBuilder->where(...$condition);
            }
        }
        
        $this->queryBuilder->where("id", $entity->getId())->runQuery();
        
        return $this->find($entity->getId());
    }

    public function delete(Entity $entity, array $conditions = []): void {
        
        $id = $this->queryBuilder
                ->table(static::$table)
                ->delete($entity->toArray());
        
        if ($conditions){
            foreach ($conditions as $condition){
                $this->queryBuilder->where(...$condition);
            }
        }
        
        $this->queryBuilder->where("id", $entity->getId())->runQuery();
    }
}
