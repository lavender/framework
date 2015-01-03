<?php
namespace Lavender\Account\Database;

use Lavender\Entity\Database\Model as Entity;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class Model extends Entity implements UserInterface, RemindableInterface
{

    public $timestamps = true;

    /**
     * Overwrites the original save method in order to perform
     * validation before actually saving the object.
     *
     * @param array $options
     *
     * @return bool
     */
    public function save(array $options = array())
    {
        if($this->isValid()) return parent::save($options);

        return false;
    }

    /**
     * Checks if the current user is valid using the Validator.
     *
     * @return bool
     */
    public function isValid()
    {
        // Instantiate the Validator and calls the
        // validate method. Feel free to use your own validation
        // class.
        $validator = \App::make('account.validator');

        // If the model already exists in the database we call validate with
        // the update ruleset
        if($this->exists){
            return $validator->validate($this, 'update');
        }

        return $validator->validate($this);
    }

    /**
     * Get the unique identifier for the user.
     *
     * @see \Illuminate\Auth\UserInterface
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        // Get the value of the model's primary key.
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @see \Illuminate\Auth\UserInterface
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @see \Illuminate\Auth\UserInterface
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->{$this->getRememberTokenName()};
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @see \Illuminate\Auth\UserInterface
     *
     * @param string $value
     */
    public function setRememberToken($value)
    {
        $this->{$this->getRememberTokenName()} = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @see \Illuminate\Auth\UserInterface
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @see \Illuminate\Auth\Reminders\RemindableInterface
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }
}