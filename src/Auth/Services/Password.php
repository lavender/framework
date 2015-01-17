<?php
namespace Lavender\Auth\Services;

use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Auth\UserInterface;
use Illuminate\Foundation\Application;

class Password
{

    /**
     * Laravel application.
     *
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
     * @param RemindableInterface $account An existent user.
     * @param string $token Password reset token.
     *
     * @return void
     */
    protected function sendEmail(UserInterface $account, $token)
    {
        $config = $this->app['config'];
        $lang = $this->app['translator'];

        $this->app['mailer']->queueOn(
            'default',
            $config->get('store.email_reset_password'),
            compact('user', 'token'),
            function ($message) use ($account, $token, $lang){
                $message
                    ->to($account->email, $account->username)
                    ->subject($lang->get('account.email.password_reset.subject'));
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
        $config = $this->app['config'];

        return $carbon->now()
            ->subHours($config->get('store.password_reset_expiration', 7))
            ->toDateTimeString();
    }

    /**
     * Generate a token for password change and saves it in
     * the Reminder table with the email of the
     * user.
     *
     * @param RemindableInterface $account An existent user.
     *
     * @return string Password reset token.
     */
    public function requestChangePassword(RemindableInterface $account)
    {
        $token = $this->generateToken();

        $values = array(
            'email' => $account->getReminderEmail(),
            'token' => $token,
            'created_at' => new \DateTime
        );

        $reminder = entity('reminder')->fill($values);

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
        $reminder = entity('reminder')
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
        return entity('reminder')
            ->where('token', '=', $token)
            ->delete();
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