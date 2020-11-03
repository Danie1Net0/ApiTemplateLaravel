<?php

namespace App\Rules\Shared;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Schema;

/**
 * Class CheckOrderParamRule
 * @package App\Rules\Shared
 */
class CheckOrderParamRule implements Rule
{
    /**
     * @var string
     */
    private string $tableName;

    /**
     * @var string
     */
    private string $message;

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
        $params = explode(':', $value);

        if ($this->checkIfColumnExists($params)) {
            return false;
        }

        if (sizeof($params) === 2 && !in_array($params[1], ['asc', 'desc'])) {
            $this->message = "O parâmetro de ordem é inválido.";
            return false;
        }

        return true;
    }

    /**
     * Check if column exists.
     *
     * @param array $params
     * @return bool
     */
    private function checkIfColumnExists(array $params): bool
    {
        if (sizeof($params) === 1 && !Schema::hasColumn($this->tableName, $params[0])) {
            $this->message = "A coluna {$params[0]} não existe.";
            return true;
        }

        if (!Schema::hasColumn($this->tableName, $params[0])) {
            $this->message = "A coluna {$params[0]} não existe.";
            return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return $this->message;
    }
}
