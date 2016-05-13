<?php
/**
 * Guard - A simple Authentication Guard.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


namespace Auth;

use Core\Config;
use Helpers\Session;
use Helpers\Cookie;
use Helpers\Encrypter;
use Helpers\Password;
use Auth\Model;

use \stdClass;


class Guard
{
    /**
     * The currently authenticated User information.
     *
     * @var \stdClass
     */
    protected $user = null;

    /**
     * Indicates if the User was authenticated via a recaller Cookie.
     *
     * @var bool
     */
    protected $viaRemember = false;

    /**
     * @var \Auth\Model
     */
    protected $model = null;

    /**
     * Indicates if the logout method has been called.
     *
     * @var bool
     */
    protected $loggedOut = false;

    /**
     * Indicates if a token user retrieval has been attempted.
     *
     * @var bool
     */
    protected $tokenRetrievalAttempted = false;

    /**
     * @var string
     */
    protected $passwordField = 'password';

    /**
     * @var string
     */
    protected $rememberToken = 'remember_token';

    /**
     * Create a new Authentication Guard instance.
     *
     * @return void
     */
    public function __construct(array $config)
    {
        // Get the used Table columns from configuration.
        if(isset($config['columns']) && is_array($config['columns'])) {
            $fields = $config['columns'];

            $this->passwordField = $fields['password'];
            $this->rememberToken = $fields['rememberToken'];
        }

        // Create the configuration specified Model instance.
        $className = '\\' .ltrim($config['model'], '\\');

        $this->model = new $className($config);
    }

    /**
     * Determine if the current User is not logged in.
     *
     * This method is the inverse of the "check" method.
     *
     * @return bool
     */
    public function guest()
    {
        return ! $this->check();
    }

    /**
     * Determine if the current User is authenticated.
     *
     * @return bool
     */
    public function check()
    {
        return ! is_null($this->user());
    }

    /**
     * Get the authenticated user.
     *
     * @return \stdClass|null
     */
    public function user()
    {
        if ($this->loggedOut) {
            return null;
        }

        if (! is_null($this->user)) {
            return $this->user;
        }

        $user = null;

        $id = Session::get($this->getName());

        if (! is_null($id)) {
            $user = $this->retrieveUserById($id);
        }

        $recaller = $this->getRecaller();

        if (is_null($user) && ! is_null($recaller)) {
            $user = $this->getUserByRecaller($recaller);

            if (! is_null($user)) {
                $this->updateSession($user);
            }
        }

        return $this->user = $user;
    }


    /**
     * Get the ID for the currently authenticated User.
     *
     * @return int|null
     */
    public function id()
    {
        if (! $this->loggedOut) {
            $id = Session::get($this->getName());

            return ! is_null($id) ? $id : $this->getRecallerId();
        }
    }

    /**
     * Attempt to authenticate a User, using the given credentials.
     *
     * @param  array $credentials
     * @param  bool  $remember
     * @param  bool  $login
     * @return bool
     */
    public function attempt(array $credentials = array(), $remember = false, $login = true)
    {
        $user = $this->retrieveUser($credentials);

        if (! is_null($user) && $this->validateCredentials($user, $credentials)) {
            if ($login) {
                $this->login($user, $remember);
            }

            return true;
        }

        return false;
    }

    /**
     * Log a User in.
     *
     * @param  \stdClass $user
     * @param  bool $remember
     * @return void
     */
    public function login(stdClass $user, $remember = false)
    {
        $this->setUser($user);

        $this->updateSession($user);

        if ($remember) {
            if (empty($user->{$this->rememberToken})) {
                $this->refreshRememberToken($user);
            }

            $this->setRecallerCookie($user);
        }
    }

    /**
     * Log the user out.
     *
     * @return void
     */
    public function logout()
    {
        if (! is_null($this->user)) {
            $this->refreshRememberToken($this->user);
        }

        // Destroy the Session and Cookie variables.
        Session::destroy($this->getName());

        Cookie::destroy($this->getRecallerName());

        // Reset the instance information.
        $this->user = null;

        $this->loggedOut = true;
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return \stdClass|null
     */
    public function retrieveUser(array $credentials)
    {
        $query = $this->model->newQuery();

        foreach ($credentials as $key => $value) {
            if ($key !== $this->passwordField) {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }

    /**
     * Retrieve a user by the given id.
     *
     * @param  int $id
     * @return \stdClass|null
     */
    public function retrieveUserById($id)
    {
        $keyName = $this->model->getKeyName();

        return $this->retrieveUser(array($keyName => $id));
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \stdClass $user
     * @param  array $credentials
     * @return bool
     */
    protected function validateCredentials(stdClass $user, array $credentials)
    {
        return Password::verify($credentials['password'], $user->{$this->passwordField});
    }

    /**
     * Refresh the "Remember me" Token for the User.
     *
     * @param  \stdClass $user
     * @return void
     */
    protected function refreshRememberToken(stdClass $user)
    {
        $keyName = $this->model->getKeyName();

        // Get a new Query from Model.
        $query = $this->model->newQuery();

        // Create a new Token and update it into Database.
        $user->remember_token = str_random(100);

        $query->where($keyName, $user->{$keyName})
            ->update(array($this->rememberToken => $user->remember_token));
    }

    /**
     * Set the current user.
     *
     * @param  \stdClass $user
     * @return void
     */
    protected function setUser(stdClass $user)
    {
        $this->user = $user;
    }

    /**
     * Update the Session with the given ID.
     *
     * @param  \stdClass $user
     * @return void
     */
    protected function updateSession(stdClass $user)
    {
        $keyName = $this->model->getKeyName();

        Session::set($this->getName(), $user->{$keyName});
    }

    /**
     * Set the recaller Cookie.
     *
     * @param  \stdClass $user
     * @return void
     */
    protected function setRecallerCookie(stdClass $user)
    {
        $keyName = $this->model->getKeyName();

        // Prepare the value and set the remembering Cookie.
        $value = $user->{$keyName} .'|' .$user->remember_token;

        Cookie::set($this->getRecallerName(), Encrypter::encrypt($value));
    }

    /**
     * Get a user by its recaller ID.
     *
     * @param  string $recaller
     * @return mixed
     */
    protected function getUserByRecaller($recaller)
    {
        if ($this->validRecaller($recaller) && ! $this->tokenRetrievalAttempted) {
            $this->tokenRetrievalAttempted = true;

            list($id, $remember_token) = explode('|', $recaller, 2);

            // Prepare the requested User credentials.
            $keyName = $this->model->getKeyName();

            $credentials = array(
                $keyName => $id,
                $this->rememberToken => $remember_token
            );

            $this->viaRemember = ! is_null($user = $this->retrieveUser($credentials));

            return $user;
        }
    }

    /**
     * Determine if the recaller Cookie is in a valid format.
     *
     * @param  string $recaller
     * @return bool
     */
    protected function validRecaller($recaller)
    {
        if (is_string($recaller) && (strpos($recaller, '|') !== false)) {
            $segments = explode('|', $recaller);

            return ((count($segments) == 2) && (trim($segments[0]) !== '') && (trim($segments[1]) !== ''));
        }

        return false;
    }

    /**
     * Get the decrypted Recaller cookie.
     *
     * @return string|null
     */
    protected function getRecaller()
    {
        $cookie = Cookie::get($this->getRecallerName());

        try {
            $recaller = Encrypter::decrypt($cookie);
        } catch (\Exception $e) {
            $recaller = null;

            // That's not a valid Cookie; destroy it.
            Cookie::destroy($this->getRecallerName());
        }

        return $recaller;
    }

    /**
     * Get the user ID from the recaller Cookie.
     *
     * @return string
     */
    protected function getRecallerId()
    {
        if ($this->validRecaller($recaller = $this->getRecaller())) {
            return reset(explode('|', $recaller));
        }
    }

    /**
     * Get the name of the Cookie used to store the "recaller".
     *
     * @return string
     */
    public function getRecallerName()
    {
        return 'remember_' .md5(get_class($this));
    }

    /**
     * Get a unique identifier for the Auth session value.
     *
     * @return string
     */
    public function getName()
    {
        return 'login_' .md5(get_class($this));
    }

    /**
     * Determine if the User was authenticated via "remember me" Cookie.
     *
     * @return bool
     */
    public function viaRemember()
    {
        return $this->viaRemember;
    }
}
