<?php

namespace PonyForm\SqliteStorePlugin;

use PonyForm\PluginContract\StorePluginInterface;
use PonyForm\PluginContract\SubmissionStoreInterface;
use PonyForm\PluginContract\SurveyStoreInterface;

class SqliteStorePlugin implements StorePluginInterface
{
    private readonly SqliteStore $sqliteStore;

    public function __construct(string $pathToDbFile)
    {
        $this->sqliteStore = new SqliteStore($pathToDbFile);
    }

    public function getSurveyStore(): SurveyStoreInterface
    {
        return $this->sqliteStore;
    }

    public function getSubmissionStore(): SubmissionStoreInterface
    {
        return $this->sqliteStore;
    }
}
