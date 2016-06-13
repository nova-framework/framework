<?php

namespace Auth;

use Database\Connection;
use Hashing\HasherInterface;

use Hash;


class DatabaseUserProvider implements UserProviderInterface
{
    /**
     * The active Database Connection.
     *
     * @param  \Database\Connection
     */
    protected $connection;

    /**
     * The hasher implementation.
     *
     * @var \Hashing\HasherInterface
     */
    protected $hasher;

    /**
     * The table containing the users.
     *
     * @var string
     */
    protected $table;

    /**
     * Create a new database user provider.
     *
     * @param  \Database\Connection  $conn
     * @param  string  $table
     * @return void
     */
    public function __construct(Connection $connection, HasherInterface $hasher, $table)
    {
        $this->connection = $connection;

        $this->hasher = $hasher;

        $this->table = $table;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Auth\UserInterface|null
     */
    public function retrieveById($identifier)
    {
        $user = $this->getTable()->find($identifier);

        if (! is_null($user)) {
            return new GenericUser((array) $user);
        }
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
        $user = $this->getTable()
            ->where('id', $identifier)
            ->where('remember_token', $token)
            ->first();

        if (! is_null($user)) {
            return new GenericUser((array) $user);
        }
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
        $this->getTable()
            ->where('id', $user->getAuthIdentifier())
            ->update(array('remember_token' => $token));
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Auth\UserInterface|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        $query = $this->getTable();

        foreach ($credentials as $key => $value) {
            if (! str_contains($key, 'password')) $query->where($key, $value);
        }

        $user = $query->first();

        if (! is_null($user)) {
            return new GenericUser((array) $user);
        }
    }

    /**
     * Validate a user against the given credentials.
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
     * Begin a new database query against the table.
     *
     * @return \Database\Query\Builder
     */
    protected function getTable()
    {
        return $this->connection->table($this->table);
    }
}
