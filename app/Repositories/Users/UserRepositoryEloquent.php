<?php

namespace App\Repositories\Users;

use App\Notifications\Auth\EmailVerificationNotification;
use App\User;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Contracts\Users\UserRepository;
use Prettus\Repository\Events\RepositoryEntityCreated;
use Prettus\Repository\Events\RepositoryEntityCreating;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class UserRepositoryEloquent.
 * @package namespace App\Repositories\Users;
 */
class UserRepositoryEloquent extends BaseRepository implements UserRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }


    /**
     * Save a new entity in repository
     *
     * @param array $attributes
     * @return mixed
     * @throws RepositoryException
     */
    public function create(array $attributes)
    {
        $attributes['password'] = Hash::make($attributes['password']);
        $attributes['activation_token'] = Str::random(60);

        $model = $this->model->newInstance($attributes);
        $model->save();
        $this->resetModel();

        if (isset($attributes['telephones']))
            $model->storeTelephones($attributes['telephones']);

        Notification::send($model, new EmailVerificationNotification());

        return $this->parserResult($model);
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
