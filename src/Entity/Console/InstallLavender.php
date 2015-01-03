<?php
namespace Lavender\Entity\Console;

use Illuminate\Console\Command;

class InstallLavender extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'lavender:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Core lavender installer';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        try{

            $callbacks = $this->laravel['lavender.installer']->installs();

            foreach($callbacks as $callback) $callback($this);

            $callbacks = $this->laravel['lavender.installer']->updates();

            foreach($callbacks as $callback) $callback($this);

        } catch(\Exception $e){

            print_r($e->getMessage());
            print_r($e->getTraceAsString());
            die;
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }
}