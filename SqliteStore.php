<?php

namespace PonyForm\SqliteStorePlugin;

use PDO;
use PDOException;
use PonyForm\PluginContract\SubmissionStoreInterface;
use PonyForm\PluginContract\Survey;
use PonyForm\PluginContract\SurveyStoreInterface;

class SqliteStore implements SurveyStoreInterface, SubmissionStoreInterface
{
    private readonly SurveyFactory $surveyFactory;

    private PDO $sqlite3;

    public function __construct(string $pathToDbFile)
    {
        $this->sqlite3 = new PDO('sqlite:' . $pathToDbFile);
        $this->surveyFactory = new SurveyFactory();
    }

    public function readSurveyById(string $id): Survey | null
    {
        $statement = $this->query(
            'SELECT
                id,
                title,
                description,
                secret,
                questions
            FROM survey
            WHERE id = ?;',
            [$id],
        );

        try {
            $resultArray = $statement->fetch();
        } catch (PDOException $e) {
            throw $this->createError($e, 'Error while reading SQL result row');
        }

        $statement->closeCursor();

        if ($resultArray === false) {
            return null;
        }

        $resultArray['questions'] = json_decode($resultArray['questions'], true);

        return $this->surveyFactory->getSurveyFromArray($resultArray);
    }

    public function createSubmission(string $surveyId, array $replies): string
    {
        $this->query(
            'INSERT INTO submission (survey, replies) VALUES (?, ?)',
            [
                $surveyId,
                json_encode($replies),
            ],
        );

        return $this->sqlite3->lastInsertId();
    }

    private function query(string $query, array $bindings)
    {
        try {
            $statement = $this->sqlite3->prepare($query);
        } catch (PDOException $e) {
            throw $this->createError($e, 'Error while preparing SQL statement');
        }

        try {
            $statement->execute($bindings);
        } catch (PDOException $e) {
            throw $this->createError($e, 'Error while executing SQL statement');
        }

        $statement->setFetchMode(PDO::FETCH_ASSOC);

        return $statement;
    }

    private function createError(PDOException $e, string $message)
    {
        return new SqliteStoreException(
            $message,
            $e->getMessage(),
            $e->getCode(),
        );
    }
}
