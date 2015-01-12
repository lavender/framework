<?php
namespace Lavender\Store\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CreateStore extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'lavender:store';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a lavender store.';

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'theme',
                null,
                InputOption::VALUE_OPTIONAL,
                'Set the theme.'
            ],
            [
                'default',
                null,
                InputOption::VALUE_OPTIONAL,
                'Set as default store.'
            ],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        if($this->confirm('Would you like to create a new store? [yes|no]', 'yes')){

            if(!$default = $this->input->getOption('default')){

                $default = $this->confirm('Use as default store: [yes|no]', 'yes');
            }

            $data = compact('default');

            if($theme_id = $this->input->getOption('theme')){

                $theme = entity('theme')->find($theme_id);

                $data['theme'] = $theme;
            }

            $store = entity('store')->fill($data);

            $store->save();

            return $store->id;
        }
    }
}