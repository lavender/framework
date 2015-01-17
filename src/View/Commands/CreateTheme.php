<?php
namespace Lavender\View\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class CreateTheme extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'lavender:theme';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a lavender theme.';

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'store',
                null,
                InputOption::VALUE_REQUIRED,
                'Store Id.'
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
        if($this->confirm('Would you like to create a new theme? [yes|no]', 'yes')){

            $code = $this->ask('Enter a code: (Default = default)', 'default');

            $name = $this->ask('Enter a name: (Default = Default Theme)', 'Default Theme');

            $data = compact('code', 'name');

            if($store_id = $this->input->getOption('store')){

                $store = entity('store')->find($store_id);

                $data['store'] = $store;
            }

            $theme = entity('theme')->fill($data);

            $theme->save();

            if($store_id) $store->update(['theme' => $theme]);

            return $theme->id;
        }
    }
}