<?php

namespace App\Rules\Shared;

use App\Repositories\Implementations\AccessControl\RoleRepositoryEloquent;
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
        foreach (explode(',', $value) as $column) {
            if (!Schema::hasColumn($this->tableName, $column)) {
                $this->value = $column;
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
        return "A coluna {$this->value} não existe.";
    }
}
