<?php

namespace PonyForm\SqliteStorePlugin;

use PonyForm\PluginContract\Question;
use PonyForm\PluginContract\Survey;
use Respect\Validation\Rules;
use Respect\Validation\Validatable;

class SurveyFactory
{
    private readonly Validatable $surveyValidator;

    public function __construct()
    {
        $idValidator = new Rules\AllOf(
            new Rules\StringType(),
            new Rules\Alnum('-', '_'),
        );

        $questionValidator = new Rules\AllOf(
            new Rules\ArrayType(),
            new Rules\Key('id', $idValidator),
            new Rules\Key('type', new Rules\StringType()),
            new Rules\Key('question', new Rules\StringType()),
            new Rules\Key('required', new Rules\BoolType()),
            new Rules\Key('extra', new Rules\ArrayType(), false),
        );

        $this->surveyValidator = new Rules\AllOf(
            new Rules\ArrayType(),
            new Rules\Key('id', $idValidator),
            new Rules\Key('title', new Rules\StringType()),
            new Rules\Key('description', new Rules\StringType(), false),
            new Rules\Key('secret', new Rules\StringType()),
            new Rules\Key('questions', new Rules\AllOf(
                new Rules\ArrayType(),
                new Rules\Each(
                    $questionValidator,
                ),
            )),
        );
    }

    public function getSurveyFromArray(mixed $array): Survey
    {
        $this->surveyValidator->assert($array);

        $questions = array_map(
            fn ($fieldArray) => new Question(
                id: $fieldArray['id'],
                type: $fieldArray['type'],
                question: $fieldArray['question'],
                required: $fieldArray['required'],
                extra: $fieldArray['extra'] ?? null,
            ),
            $array['questions'],
        );

        return new Survey(
            id: $array['id'],
            title: $array['title'],
            description: $array['description'] ?? "",
            secret: $array['secret'],
            questions: $questions,
        );
    }
}
