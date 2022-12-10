<?php

declare(strict_types = 1);

namespace App\Database;

use App\Exception\MissingArgumentException;

/**
 * AbstractConnection
 *
 * @author erikjohnson06
 */
abstract class AbstractConnection {

    protected $connection;
    protected $credentials;

    const REQUIRED_CONNECTION_KEYS = [];

    public function __construct(array $credentials) {
        $this->credentials = $credentials;

        if (!$this->credentialsHaveRequiredKeys($credentials)) {
            throw new MissingArgumentException(
                            sprintf("Database connection credentials not mapped correctly. Required keys: %s",
                                    implode(",", static::REQUIRED_CONNECTION_KEYS))
            );
        }
    }

    /**
     * Ensure the keys passed match the required keys
     *
     * @param array $credentials
     * @return bool
     */
    private function credentialsHaveRequiredKeys(array $credentials): bool {

        $matches = array_intersect(static::REQUIRED_CONNECTION_KEYS, array_keys($credentials));

        return count($matches) === count(static::REQUIRED_CONNECTION_KEYS);
    }

    abstract protected function parseCredentials(array $credentials): array;
}
