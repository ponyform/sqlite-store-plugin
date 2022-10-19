<?php

namespace PonyForm\SqliteStorePlugin;

use Exception;

class SqliteStoreException extends Exception
{
    public function __construct(
        string $contextMessage,
        public string $errorMessage,
        public readonly string $errorCode
    ) {
        parent::__construct("$contextMessage: \"$errorMessage\" (Code: $errorCode)");
    }
}
