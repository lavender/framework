<?php
namespace Lavender\Auth;

use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Application;

class Password
{

    /**
     * Laravel application.
     *
     * todo remove this dependency
     * @var \Illuminate\Foundation\Application
     */
    public $app;

    /**
     * Create a new PasswordService.
     *
     * @param \Illuminate\Foundation\Application $app Laravel application object
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Sends an email containing the reset password link with the
     * given token to the user.
     *
     * todo account contracts
     * @param $account An existent user.
     * @param string $token Password reset token.
     *
     * @return void
     */
    protected function sendEmail($account, $token)
    {
        $this->app['mailer']->queueOn(
            'default',
            config('store.email_reset_password'),
            compact('user', 'token'),
            function ($message) use ($account, $token){
                $message
                    ->to($account->email, $account->username)
                    ->subject(trans('account.email.password_reset.subject'));
            }
        );
    }

    /**
     * Returns a date to limit the acceptable password reset requests.
     *
     * @return string 'Y-m-d H:i:s' formated string.
     */
    protected function getOldestValidDate()
    {
        // Instantiate a carbon object (that is a dependency of 'illuminate/database')
        $carbon = $this->app['Carbon\Carbon'];

        return $carbon->now()
            ->subHours(config('store.password_reset_expiration', 7))
            ->toDateTimeString();
    }

    /**
     * Generate a token for password change and saves it in
     * the Reminder table with the email of the
     * user.
     *
     * @param CanResetPasswordContract $account An existent user.
     *
     * @return string Password reset token.
     */
    public function requestChangePassword(CanResetPasswordContract $account)
    {
        $token = $this->generateToken();

        $values = array(
            'email' => $account->getEmailForPasswordReset(),
            'token' => $token,
            'created_at' => new \DateTime
        );

        $reminder = $this->getReminder()->fill($values);

        $reminder->save();

        $this->sendEmail($account, $token);

        return $token;
    }

    /**
     * Returns the email associated with the given reset
     * password token.
     *
     * @param string $token
     *
     * @return string Email.
     */
    public function getEmailByToken($token)
    {
        $reminder = $this->getReminder()
            ->where('token', '=', $token)
            ->where('created_at', '>=', $this->getOldestValidDate())
            ->first();

        return $reminder->email;
    }

    /**
     * Delete the record of the given token from database.
     *
     * @param string $token
     *
     * @return boolean Success.
     */
    public function destroyToken($token)
    {
        return $this->getReminder()
            ->where('token', '=', $token)
            ->delete();
    }

    public function getReminder()
    {
        return entity('reminder');
    }


    /**
     * Generates a random password change token.
     *
     * @return string
     */
    protected function generateToken()
    {
        return md5(uniqid(mt_rand(), true));
    }
}