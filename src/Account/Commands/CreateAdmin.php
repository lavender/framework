<?php
namespace Lavender\Account\Commands;

use Illuminate\Console\Command;

class CreateAdmin extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'lavender:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin account.';

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [ ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        if($this->confirm('Would you like to create a new admin account? [yes|no]', 'yes')){

            $success = false;

            while(!$success){

                $admin = entity('admin');

                $admin->username = $this->ask('Enter a username: (Default = admin)', 'admin');

                $admin->email = $this->ask('Enter an email address: (required)');

                $admin->password = $this->secret('Enter a password: (required)');

                $admin->password_confirmation = $this->secret('Confirm your password: (required)');

                $success = $admin->save();

                if(!$success) $this->error($admin->errors);

            }

            $this->info("Admin account '{$admin->username}' successfully created!");

            return $admin->id;
        }
    }
}