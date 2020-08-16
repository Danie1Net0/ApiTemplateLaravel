<?php

namespace App\Rules\Shared;

use BadMethodCallException;
use Illuminate\Contracts\Validation\Rule;

class CheckIfRelationshipExistsRule implements Rule
{
    /**
     * @var string
     */
    private $model;

    /**
     * @var string
     */
    private $value;

    /**
     * Create a new rule instance.
     *
     * @param object $model
     */
    public function __construct(object $model)
    {
        $this->model = $model;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            $this->model->has("{$value}");
        } catch (BadMethodCallException $exception) {
            $this->value = $value;
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "O relacionamento $this->value não existe.";
    }
}
