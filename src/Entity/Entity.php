<?php

declare(strict_types = 1);

namespace App\Entity;

/**
 * Entity
 *
 * @author erikjohnson06
 */
abstract class Entity {
    
    abstract public function getId() : int;
    abstract public function toArray() : array;
}
