<?php
namespace Lavender\Auth;

use Lavender\Database\Entity;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class Account extends Entity implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    /**
     * Check if the passwords match
     *
     * @return bool
     */
    protected function checkPasswords()
    {
        if($this->getOriginal('password') != $this->password){

            if($this->password === $this->password_confirmation){

                // Hashes password and unset password_confirmation field
                $this->password = bcrypt($this->password);

                unset($this->password_confirmation);

            } else {

                return false;
            }

        }

        return true;
    }

    /**
     * Save the model to the database.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = array())
    {
        if(!$this->checkPasswords()) return false;

        return parent::save($options);
    }

}