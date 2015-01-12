<?php
namespace Lavender\Catalog\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CreateCategory extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'lavender:category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a lavender category.';


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
                InputOption::VALUE_OPTIONAL,
                'Set root category for store (id).'
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
        if ($this->confirm('Would you like to create a new category? [yes|no]', 'yes')){

            $name = $this->ask('Enter a name: (Default = Default Category)', 'Default Category');

            $data = compact('name');

            if($store_id = $this->input->getOption('store')){

                $store = entity('store')->find($store_id);

                $data['store'] = $store;

            }

            $category = entity('category')->fill($data);

            $category->save();

            if($store_id) $store->update(['root_category' => $category]);

            return $category->id;
        }
    }




}