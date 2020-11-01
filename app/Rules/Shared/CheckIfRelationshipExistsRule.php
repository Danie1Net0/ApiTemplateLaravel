<?php

namespace App\Rules\Shared;

use BadMethodCallException;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class CheckIfRelationshipExistsRule implements Rule
{
    /**
     * @var Model
     */
    private Model $model;

    /**
     * @var string
     */
    private string $value;

    /**
     * Create a new rule instance.
     *
     * @param string $model
     */
    public function __construct(string $model)
    {
        $this->model = app($model);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        foreach (explode(',', $value) as $relationship) {
            try {
                $this->model->has($relationship);
            } catch (BadMethodCallException $exception) {
                $this->value = $relationship;
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return "O relacionamento $this->value não existe.";
    }
}
