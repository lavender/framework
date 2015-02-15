<?php
namespace Lavender\Auth\Account;

use Illuminate\Auth\Guard as CoreGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Lavender\Support\Contracts\EntityInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Guard extends CoreGuard
{

    protected $name;

    public function __construct(
        UserProvider  $provider,
        SessionInterface $session,
        $name,
        Request $request = null
    )
    {
        parent::__construct($provider, $session, $request);

        $this->name = $name;
    }

    /**
     * Find a user by the given email
     *
     * @param string $email The email to be used in the query
     *
     * @return User
     */
    public function findByEmail($email)
    {
        return $this->findByIdentity(['email' => $email]);
    }

    /**
     * Find a user by one of the fields given as $identity.
     * If one of the fields in the $identity array matches the user
     * will be retrieved.
     * todo interface for identity
     *
     * @param array $identity An array of attributes and values to search for
     *
     * @return UserInterface
     */
    public function findByIdentity($identity)
    {
        $account = $this->model();

        $account = $account->where(function ($query) use ($identity){

            $firstWhere = true;

            foreach($identity as $attribute => $value){

                if($firstWhere) $query->where($attribute, '=', $value);

                else $query->orWhere($attribute, '=', $value);

                $firstWhere = false;
            }
        });

        $account = $account->get()->first();

        return $account;
    }

    /**
     * todo interface for identity
     * @param UserInterface $account
     * @return UserInterface
     */
    public function refresh($account)
    {
        $identity = $this->identity($account);

        return $this->findByIdentity([$identity => $account->$identity]);
    }

    /**
     * Update the confirmation status of a user to true if a user
     * is found with the given confirmation code.
     *
     * @param string $code
     *
     * @return bool
     */
    public function confirmByCode($code)
    {
        $identity = ['confirmation_code' => $code];

        $account = $this->findByIdentity($identity);

        if($account){

            return $this->confirm($account);
        } else{

            return false;
        }
    }

    /**
     * Updated the given user in the database. Set the 'confirmed' attribute to
     * true.
     *
     * @param  $account
     *
     * @return bool
     */
    private function confirm(EntityInterface $account)
    {
        $account->confirmed = true;

        return $account->save();
    }

    /**
     * Signup a new account with the given parameters
     *
     * todo interface for identity
     * @param  array $input Array containing 'username', 'email' and 'password'.
     *
     * @return  EntityInterface User object that may or may not be saved successfully. Check the id to make sure.
     */
    public function register($input)
    {
        $account = $this->model()->fill($input);

        $account->password_confirmation = array_get($input, 'password_confirmation');

        // Generate a random confirmation code
        $account->confirmation_code = md5(uniqid(mt_rand(), true));

        // Save if valid. Password field will be hashed before save
        return $this->save($account);
    }

    /**
     * Simply saves the given instance
     *
     * @param  EntityInterface $instance
     *
     * @return  boolean Success
     */
    public function save(EntityInterface $instance)
    {
        return $instance->save();
    }

    public function identity($model = null)
    {
        if(!$model) $model = $this->model();

        $fillable = $model->getFillable();

        return in_array('username', $fillable) ? 'username' : 'email';
    }

    /**
     * Attempt to log a user into the application with password and email
     *
     * @param array $input
     *
     * @param bool $mustBeConfirmed If true, the user must have confirmed his email account in order to log-in.
     *
     * @return bool Success.
     */
    public function logAttempt(array $input, $mustBeConfirmed = true)
    {
        $remember = $this->extractRememberFromArray($input);

        $given_identity = $input[$this->identity()];

        if($this->loginThrottling($given_identity)){

            $account = $this->findByIdentity([$this->identity() => $given_identity]);

            if($account instanceof EntityInterface){

                if(!$account->confirmed && $mustBeConfirmed){
                    return false;
                }

                if(!$this->checkPassword($account, $input)){
                    return false;
                }

                $this->login($account, $remember);

                return true;
            }
        }

        return false;
    }

    /**
     * todo interface password
     * @param $account
     * @param array $input
     * @return mixed
     */
    public function checkPassword($account, array $input)
    {
        $password = isset($input['password']) ? $input['password'] : false;

        return app('hash')->check($password, $account->password);
    }

    /**
     * Extracts the value of the remember key of the given array.
     *
     * @param array $input An array containing the key 'remember'.
     *
     * @return bool
     */
    protected function extractRememberFromArray(array $input)
    {
        return isset($input['remember']) ? $input['remember'] : false;
    }

    /**
     * Calls throttleIdentity of the loginThrottler and returns false
     * if the throttleCount is grater then the 'throttle_limit' config.
     * Also sleeps a little in order to avoid dicionary attacks.
     *
     * @param mixed $identity .
     *
     * @return boolean False if the identity has reached the 'throttle_limit'.
     */
    protected function loginThrottling($identity)
    {
        $count = app('account.throttle')->throttleIdentity($identity);

        if($count >= \Config::get('store.throttle_limit')) return false;

        // Throttling delay!
        // See: http://www.codinghorror.com/blog/2009/01/dictionary-attacks-101.html
        if($count > 2) usleep(($count - 1) * 400000);

        return true;
    }

    /**
     * Checks if the given credentials correponds to a user that exists but
     * is not confirmed
     *
     * @param  array $credentials Array containing the credentials (email/username and password)
     *
     * @return  boolean Exists and is not confirmed?
     */
    public function existsButNotConfirmed($input)
    {
        $given_identity = $input[$this->identity()];

        $account = $this->findByIdentity([$this->identity() => $given_identity]);

        if($account){

            $correctPassword = $this->checkPassword($account, $input);

            return (!$account->confirmed && $correctPassword);
        }
    }

    /**
     * Resets a password of a user. The $input['token'] will tell which user.
     *
     * @param  array $input Array containing 'token', 'password' and 'password_confirmation' keys.
     *
     * @return  boolean Success
     */
    public function resetPassword($input)
    {
        $result = false;
        $account = $this->userByResetPasswordToken($input['token']);

        if($account){
            $account->password = $input['password'];
            $account->password_confirmation = $input['password_confirmation'];
            $result = $this->save($account);
        }

        // If result is positive, destroy token
        if($result) $this->destroyForgotPasswordToken($input['token']);

        return $result;
    }

    /**
     * Delete the record of the given token from 'password_reminders' table.
     *
     * @param string $token Token retrieved from a forgotPassword.
     *
     * @return boolean Success.
     */
    public function destroyForgotPasswordToken($token)
    {
        return app('account.password')->destroyToken($token);
    }

    /**
     * Returns a user that corresponds to the given reset password token or
     * false if there is no user with the given token.
     *
     * @param string $token
     *
     * todo user interface
     *
     * @return UserInterface
     */
    public function userByResetPasswordToken($token)
    {
        if($email = app('account.password')->getEmailByToken($token)){

            return $this->findByEmail($email);
        }

        return false;
    }

    /**
     * Asks the loginThrottler service if the given identity has reached the throttle_limit.
     *
     * @param mixed $identity The login identity.
     *
     * @return boolean True if the identity has reached the throttle_limit.
     */
    public function isThrottled($identity)
    {
        return app('account.throttle')->isThrottled($identity);
    }

    /**
     * If an user with the given email exists then generate a token for password
     * change and saves it in the 'password_reminders' table with the email
     * of the user.
     *
     * @param string $email
     * todo user interface
     *
     * @return string $token
     */
    public function forgotPassword($email)
    {
        $account = $this->findByEmail($email);

        //if($account instanceof UserInterface){

            return app('account.password')->requestChangePassword($account);
        //}

        return false;
    }

    /**
     * Log the user out of the application.
     */
    public function logout()
    {
        if($this->check()) parent::logout();
    }

    public function getName()
    {
        return 'login_' . $this->name . '_' . md5(get_class($this));
    }

    public function getRecallerName()
    {
        return 'remember_' . $this->name . '_' . md5(get_class($this));
    }

    public function get()
    {
        return $this->user();
    }

    public function model()
    {
        return entity($this->name);
    }
}