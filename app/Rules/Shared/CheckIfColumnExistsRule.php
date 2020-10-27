<?php

namespace App\Rules\Shared;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Schema;

/**
 * Class CheckIfColumnExistsRule
 * @package App\Rules\Shared
 */
class CheckIfColumnExistsRule implements Rule
{
    /**
     * @var string
     */
    private string $tableName;

    /**
     * @var string
     */
    private string $value;

    /**
     * Create a new rule instance.
     *
     * @param string $tableName
     */
    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->value = $value;
        return Schema::hasColumn($this->tableName, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "A coluna {$this->value} não existe.";
    }
}
