<?php

namespace App\Repositories\Users;

use App\Criteria\Users\UpdateUserCriteria;
use App\Notifications\Auth\EmailVerificationNotification;
use App\Repositories\Contracts\Users\UserRepository;
use App\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Events\RepositoryEntityUpdated;
use Prettus\Repository\Events\RepositoryEntityUpdating;
use Prettus\Repository\Exceptions\RepositoryException;

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
        if (isset($attributes['password']))
            $attributes['password'] = Hash::make($attributes['password']);

        $attributes['activation_token'] = Str::random(60);

        $model = $this->model->newInstance($attributes);
        $model->save();
        $this->resetModel();

        $model->avatar()->create();

        if (isset($attributes['telephones']))
            $model->storeTelephones($attributes['telephones']);

        if (Auth::check() && Auth::user()->hasRole('Super Administrador|Administrador')) {
            if (isset($attributes['role']))
                $model->assignRole($attributes['role']);
            else
                $model->assignRole('Usuário');
        } else {
            $model->assignRole('Usuário');
        }

        Notification::send($model, new EmailVerificationNotification());

        return $this->parserResult($model);
    }

    /**
     * @param array $attributes
     * @param $id
     * @return LengthAwarePaginator|Collection|mixed
     * @throws RepositoryException
     */
    public function update(array $attributes, $id)
    {
        $this->applyScope();

        $temporarySkipPresenter = $this->skipPresenter;

        $this->skipPresenter(true);

        $model = $this->pushCriteria(new UpdateUserCriteria())->findOrFail($id);

        event(new RepositoryEntityUpdating($this, $model));

        if (isset($attributes['avatar']))
            $model->updateImage('avatar', $attributes['avatar']);

        if (isset($attributes['telephones']))
            $model->updateTelephones($attributes['telephones']);

        $model = $this->pushCriteria(new UpdateUserCriteria())->findOrFail($id);

        $model->fill($attributes);
        $model->save();

        $this->skipPresenter($temporarySkipPresenter);
        $this->resetModel();

        event(new RepositoryEntityUpdated($this, $model));

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
