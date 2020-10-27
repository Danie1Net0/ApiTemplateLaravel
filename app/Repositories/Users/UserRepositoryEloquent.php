<?php

namespace App\Repositories\Users;

use App\Criteria\Users\UpdateUserCriteria;
use App\Notifications\Auth\EmailVerificationNotification;
use App\Repositories\Contracts\Users\UserRepository;
use App\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Events\RepositoryEntityDeleted;
use Prettus\Repository\Events\RepositoryEntityDeleting;
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
     */
    public function create(array $attributes)
    {
        return DB::transaction(function () use ($attributes) {
            if (isset($attributes['password'])) {
                $attributes['password'] = Hash::make($attributes['password']);
            }

            $attributes['confirmation_token'] = confirmationTokenGenerate($this);

            $model = $this->model->newInstance($attributes);
            $model->save();
            $this->resetModel();

            $model->avatar()->create();
            $model->storeTelephones($attributes['telephones'] ?? []);

            if (Auth::check() && Auth::user()->hasRole('Super Administrador|Administrador')) {
                $model->assignRole($attributes['roles'] ?? 'Usuário');
                $model->syncPermissions($attributes['permissions'] ?? []);
            } else {
                $model->assignRole('Usuário');
            }

            Notification::send($model, new EmailVerificationNotification());

            return $this->parserResult($model);
        });
    }

    /**
     * @param array $attributes
     * @param $id
     * @return LengthAwarePaginator|Collection|mixed
     */
    public function update(array $attributes, $id)
    {
        return DB::transaction(function () use ($attributes, $id) {
            $this->applyScope();

            $temporarySkipPresenter = $this->skipPresenter;

            $this->skipPresenter(true);

            $model = $this->pushCriteria(new UpdateUserCriteria())->findOrFail($id);

            event(new RepositoryEntityUpdating($this, $model));

            if (isset($attributes['avatar'])) {
                $model->updateImage('avatar', $attributes['avatar']);
            }

            if (isset($attributes['telephones'])) {
                $model->updateTelephones($attributes['telephones']);
            }

            $model = $this->pushCriteria(new UpdateUserCriteria())->findOrFail($id);

            if (isset($attributes['password'])) {
                $attributes['password'] = Hash::make($attributes['password']);
            }

            if (Auth::check() && !Auth::user()->hasRole('Super Administrador|Administrador') && isset($attributes['is_active'])) {
                unset($attributes['is_active']);
            }

            $model->fill($attributes);
            $model->save();

            if (Auth::check() && Auth::user()->hasRole('Super Administrador|Administrador')) {
                if (isset($attributes['roles'])) {
                    $model->syncRoles($attributes['roles']);
                }

                if (isset($attributes['permissions'])) {
                    $model->syncPermissions($attributes['permissions']);
                }
            }

            $this->skipPresenter($temporarySkipPresenter);
            $this->resetModel();

            event(new RepositoryEntityUpdated($this, $model));

            return $this->parserResult($model);
        });
    }

    /**
     * @param $id
     * @return int
     * @throws RepositoryException
     */
    public function delete($id)
    {
        $this->applyScope();

        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);

        $model = $this->find($id);
        $originalModel = clone $model;

        $this->skipPresenter($temporarySkipPresenter);
        $this->resetModel();

        event(new RepositoryEntityDeleting($this, $model));

        $model->deleteImage('avatar');
        $model->telephones()->delete();
        $deleted = $model->delete();

        event(new RepositoryEntityDeleted($this, $originalModel));

        return $deleted;
    }

    /**
     * Boot up the repository, pushing criteria
     *
     * @throws RepositoryException
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
