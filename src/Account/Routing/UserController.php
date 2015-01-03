<?php
namespace Lavender\Account\Routing;

use Illuminate\Routing\Controller;

class UserController extends Controller
{

    /**
     * Attempt to confirm account with code
     *
     * @param  string $code
     *
     * @return  \Illuminate\Http\Response
     */
    public function confirm($code)
    {
        if(\Account::user()->confirmByCode($code)){

            \Message::addSuccess(\Lang::get('account.alerts.confirmation'));
        } else{

            \Message::addError(\Lang::get('account.alerts.wrong_confirmation'));
        }

        return \Redirect::to('account/login');
    }

    /**
     * Log the user out of the application.
     *
     * @return  \Illuminate\Http\Response
     */
    public function logout()
    {
        \Account::user()->logout();

        return \View::make('account.logout');
    }

    /**
     * Send token to our reset password field
     *
     * @param  string $token
     *
     * @return  \Illuminate\Http\Response
     */
    public function resetPassword($token)
    {
        // merge the requested token into our input
        \Input::merge(['token' => $token]);

        // flash the token to the session to be used by workflow fields
        \Input::flashOnly('token');

        return \Redirect::to('account/reset_password');
    }

    /**
     * Shows the change password form with the given token
     *
     * @param  string $token
     *
     * @return  \Illuminate\Http\Response
     */
    public function doReset()
    {
        return \View::make('account.reset_password');
    }
}
