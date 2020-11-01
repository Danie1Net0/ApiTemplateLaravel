<?php

namespace App\Rules\Shared;

use App\Repositories\AccessControl\RoleRepositoryEloquent;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class CheckIfRoleExistsRule
 * @package App\Rules\Shared
 */
class CheckIfRoleExistsRule implements Rule
{
    /**
     * @var RoleRepositoryEloquent
     */
    private RoleRepositoryEloquent $roleRepository;

    /**
     * @var string
     */
    private string $value;

    /**
     * Create a new rule instance.
     * @param RoleRepositoryEloquent $roleRepository
     */
    public function __construct(RoleRepositoryEloquent $roleRepository)
    {
        $this->roleRepository = $roleRepository;
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
        foreach (explode(',', $value) as $role) {
            if (is_null($this->roleRepository->findWhere(['name' => $role])->first())) {
                $this->value = $role;
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
        return "A função {$this->value} não existe.";
    }
}
