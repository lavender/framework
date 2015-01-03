<?php
namespace Lavender\Account\Services;

use Illuminate\Auth\UserInterface;

class Validator
{

    /**
     * Validates the given user.
     *
     * @param UserInterface $account Instance to be tested.
     *
     * @return boolean True if the $account is valid.
     */
    public function validate(UserInterface $account, $ruleset = 'create')
    {
        return $this->validateAttributes($account, $ruleset) &&
        $this->validatePassword($account) &&
        $this->validateRuleset($account, $ruleset);
    }

    /**
     * If creating, validate the given account is unique.
     * If updating, validate the given account exists.
     *
     * @param UserInterface $account
     * @return bool True if user is unique.
     *
     */
    public function validateRuleset(UserInterface $account, $ruleset = 'create')
    {
        $exists = \Account::user()->refresh($account);

        if($ruleset == 'create' && $exists){

            \Message::addError(\Lang::get('account.alerts.duplicated_credentials'));

            return false;
        }

        if($ruleset == 'update' && !$exists){

            \Message::addError(\Lang::get('account.alerts.does_not_exist'));

            return false;
        }

        return true;
    }

    /**
     * Uses Laravel Validator in order to check if the attributes of the
     * $account object are valid for the given $ruleset.
     *
     * @param UserInterface $account
     * @param string $ruleset The name of the key in the UserValidator->$rules array
     *
     * @return boolean True if the attributes are valid.
     */
    public function validateAttributes(UserInterface $account, $ruleset = 'create')
    {
        $attributes = $account->toArray();

        // Force getting password since it may be hidden from array form
        $attributes['password'] = $account->getAuthPassword();

        $rules = $account->rules[$ruleset];

        $validator = \App::make('validator')->make($attributes, $rules);

        // Validate and attach errors
        if($validator->fails()){

            // todo handle errors
            $account->errors = $validator->errors();

            return false;
        } else{

            return true;
        }
    }

    /**
     * Validates the password and password_confirmation of the given user.
     *
     * @param UserInterface $account
     *
     * @return boolean True if password is valid.
     */
    public function validatePassword(UserInterface $account)
    {
        $hash = \App::make('hash');

        if($account->getOriginal('password') != $account->password){

            if($account->password === $account->password_confirmation){

                // Hashes password and unset password_confirmation field
                $account->password = $hash->make($account->password);

                unset($account->password_confirmation);

                return true;
            } else{

                // todo handle errors
                $account->errors = \Lang::get('account.alerts.wrong_confirmation');

                return false;
            }
        }

        return true;
    }
}
