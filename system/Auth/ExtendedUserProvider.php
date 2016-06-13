<?php

namespace Auth;

use Hashing\HasherInterface;


class ExtendedUserProvider implements UserProviderInterface
{
    /**
     * The hasher implementation.
     *
     * @var \Hashing\HasherInterface
     */
    protected $hasher;

    /**
     * The ORM User Model.
     *
     * @var string
     */
    protected $model;


    /**
     * Create a new Database User Provider.
     *
     * @param  \Hashing\HasherInterface  $hasher
     * @param  string  $model
     * @return void
     */
    public function __construct(HasherInterface $hasher, $model)
    {
        $this->hasher = $hasher;

        $this->model = $model;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Auth\UserInterface|null
     */
    public function retrieveById($identifier)
    {
        return $this->createModel()->newQuery()->find($identifier);
    }

    /**
     * Retrieve a user by by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string  $token
     * @return \Auth\UserInterface|null
     */
    public function retrieveByToken($identifier, $token)
    {
        $model = $this->createModel();

        return $model->newQuery()
            ->where($model->getKeyName(), $identifier)
            ->where($model->getRememberTokenName(), $token)
            ->first();
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Auth\UserInterface  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(UserInterface $user, $token)
    {
        $user->setAttribute($user->getRememberTokenName(), $token);

        $user->save();
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Auth\UserInterface|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        $query = $this->createModel()->newQuery();

        foreach ($credentials as $key => $value) {
            if (! str_contains($key, 'password')) $query->where($key, $value);
        }

        return $query->first();
    }

    /**
     * Validate a User against the given credentials.
     *
     * @param  \Auth\UserInterface  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(UserInterface $user, array $credentials)
    {
        $plain = $credentials['password'];

        return $this->hasher->check($plain, $user->getAuthPassword());
    }

    /**
     * Create a new instance of the Model.
     *
     * @return \Database\ORM\Model
     */
    public function createModel()
    {
        $className = '\\'.ltrim($this->model, '\\');

        return new $className();
    }

}
