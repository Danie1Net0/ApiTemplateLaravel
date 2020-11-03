<?php

namespace App\Rules\Shared;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Schema;

class CheckSearchParamsRule implements Rule
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
    public function passes($attribute, $value): bool
    {
        foreach (explode(';', $value) as $conditions) {
            $params = explode(':', $conditions);

            if (!Schema::hasColumn($this->tableName, $params[0])) {
                $this->value = $params[0];
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
        return "O campo {$this->value} não existe.";
    }
}
