<?php
namespace Lavender\Commands;

use Illuminate\Console\Command;
use Lavender\Database\Migrations\Creator;
use Lavender\Events\Entity\SchemaPrepare;
use Lavender\Support\Facades\Attribute;
use Lavender\Support\Facades\Relationship;
use Lavender\Support\Traits\AttributeTrait;
use Lavender\Support\Traits\RelationshipTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MigrateEntity extends Command
{
    use AttributeTrait, RelationshipTrait;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'migrate:entity';

    protected $foreign_keys = [];

    protected $pivots = [];

    protected $migrations;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create migrations for entity tables.';

    /**
     * The migration creator instance.
     *
     * @var Creator
     */
    protected $creator;

    /**
     * The path to the packages directory (vendor).
     *
     * @var string
     */
    protected $packagePath;

    /**
     * Create a new migration install command instance.
     *
     * @param Creator $creator
     * @param  string $packagePath
     */
    public function __construct(Creator $creator, $packagePath)
    {
        parent::__construct();

        $this->creator = $creator;
        $this->packagePath = $packagePath;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::OPTIONAL, 'The name of the migration'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['bench', null, InputOption::VALUE_OPTIONAL, 'The workbench the migration belongs to.', null],

            ['package', null, InputOption::VALUE_OPTIONAL, 'The package the migration belongs to.', null],

            ['path', null, InputOption::VALUE_OPTIONAL, 'Where to store the migration.', null],

            ['entity', null, InputOption::VALUE_OPTIONAL, 'The name of the entity you want to update.', null],
        ];
    }

    /**
     * Write the migration file to disk.
     *
     * @param  string $name
     * @return string
     */
    protected function writeMigration($name)
    {
        $path = $this->getMigrationPath();

        if($file = pathinfo($this->creator->create($name, $path, $this->migrations), PATHINFO_FILENAME)){

            $this->line("<info>Created Migration:</info> $file");
        } else{

            $this->error("<info>Nothing to migrate.</info>");
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $config = config('entity');

        if($entities = $this->input->getOption('entity')){

            foreach(explode(',', $entities) as $entity){

                if(isset($config[$entity])){

                    $this->updateEntity(entity($entity));
                }
            }
        } else{

            foreach($config as $entity => $values){

                $this->updateEntity(entity($entity));
            }
        }

        $this->updatePivots();

        $this->updateForeignKeys();

        $name = $this->input->getArgument('name') ?: 'migration_' . time();

        $this->writeMigration($name);
    }

    /**
     * Update the entity
     */
    protected function updateEntity($entity)
    {
        $config = $entity->getConfig();

        $action = \Schema::hasTable($entity->getTable()) ? 'update' : 'create';

        $this->migrations[$action][$entity->getTable()]['cols'] = $this->buildTable($entity, $config);

        if($action == 'create' && $entity->timestamps) $this->migrations[$action][$entity->getTable()]['cols'][] = '$table->timestamps();';
    }

    /**
     * Build our table
     *
     * @return array
     */
    protected function buildTable($entity, $config)
    {
        // Prepare pivot tables and one-to-* columns
        $config = $this->prepareRelationships($entity->getEntity(), $config);

        // Allow other services to append schema
        foreach(event(new SchemaPrepare($entity)) as $attributes){

            foreach((array)$attributes as $key => $value){

                $config['attributes'][$key] = $this->applyAttributeDefaults($value);

            }

        }

        // Append attribute columns
        return $this->addAttributes($entity->getTable(), $config['attributes']);
    }

    protected function updatePivots()
    {
        foreach($this->pivots as $pivot => $attributes){

            $action = \Schema::hasTable($pivot) ? 'update' : 'create';

            $this->migrations[$action][$pivot]['cols'] = $this->addAttributes($pivot, $attributes);
        }
    }

    protected function prepareRelationships($entity, $config)
    {
        $relationships = $config['relationships'];

        foreach($relationships as $relationship){

            $table = $relationship['table'];

            $foreignKey = $this->underscore($relationship['entity']) . '_id';

            $localKey = $this->underscore($entity) . '_id';

            $attribute = $this->applyAttributeDefaults(['parent' => $relationship['entity']]);

            if($relationship['type'] == Relationship::HAS_PIVOT &&
                !isset($this->pivots[$relationship['table']])
            ){
                $this->pivots[$table][$foreignKey] = $attribute;

                $this->pivots[$table][$localKey] = $this->applyAttributeDefaults(['parent' => $entity]);

            } elseif($relationship['type'] == Relationship::HAS_ONE){

                $config['attributes'][$foreignKey] = $attribute;

            } elseif($relationship['type'] == Relationship::BELONGS_TO){

                $config['attributes'][$foreignKey] = $attribute;

            }
        }

        return $config;
    }

    protected function addAttributes($table, $attributes)
    {
        $results = [];

        foreach($attributes as $column => $attribute){

            $parent = isset($attribute['parent']) ? $attribute['parent'] : null;

            $type = $parent ? Attribute::INDEX : $attribute['type'];

            $default = $parent ? null : $attribute['default'];

            $unique = $parent ? null : $attribute['unique'];

            $nullable = $parent ? null : $attribute['nullable'];

            if($add = $this->addColumn($table, $type, $column, $default, $parent, $unique, $nullable)){

                $results[$column] = $add;
            }
        }

        return $results;
    }

    protected function addFk($table, array $keys)
    {
        $result = [];

        foreach($keys as $fk){

            $result[$table . '_' . $fk['col'] . '_foreign'] = '$table->foreign("' . $fk['col'] . '")
                ->references("' . $fk['ref_col'] . '")
                ->on("' . entity($fk['ref_table'])->getTable() . '")
                ->onDelete("cascade");';
        }

        return $result;
    }

    protected function updateForeignKeys()
    {
        foreach($this->foreign_keys as $table => $keys){

            $this->migrations['update'][$table]['fks'] = $this->addFk($table, $keys);
        }
    }

    /**
     * @param $table
     * @param $type
     * @param $column
     * @param null $default
     * @param null $parent
     * @return array
     */
    protected function addColumn($table, $type, $column, $default = null, $parent = null, $unique = false, $nullable = true)
    {
        $result = '';

        if(!\Schema::hasColumn($table, $column)){

            switch($type){
                case Attribute::INDEX:
                    $result = '$table->integer("' . $column . '")->unsigned()->nullable();$table->index("' . $column . '");';
                    break;
                case Attribute::BOOL:
                case Attribute::INTEGER:
                case 'integer':
                    $result = '$table->integer("' . $column . '")->default(' . (integer)$default . ');';
                    break;
                case Attribute::DECIMAL:
                    $result = '$table->decimal("' . $column . '", 12, 4)->default("' . $default . '");';
                    break;
                case Attribute::DATE:
                case 'datetime':
                    $result = '$table->dateTime("' . $column . '")->default("' . $default . '");';
                    break;
                case Attribute::TEXT:
                    if($nullable) $result = '$table->longText("' . $column . '")->nullable();';
                    else $result = '$table->longText("' . $column . '");';
                    break;
                default:
                    if($unique) $result = '$table->string("' . $column . '", 150)->unique();';
                    elseif($nullable) $result = '$table->string("' . $column . '", 150)->default("' . $default . '")->nullable();';
                    else $result = '$table->string("' . $column . '", 150)->default("' . $default . '");';
                    break;
            }

            if($parent){

                $this->foreign_keys[$table][] = [
                    'col' => $column,
                    'ref_table' => $parent,
                    'ref_col' => 'id',
                ];
            }
        }

        return $result;
    }

    /**
     * Array merge recursive
     * @param array $arr1
     * @param array $arr2
     * @return array
     */
    protected function merge($arr1, $arr2)
    {
        if(!is_array($arr1) || !is_array($arr2)){
            return $arr2;
        }

        foreach($arr2 as $key => $val){

            $arr1[$key] = $this->merge(@$arr1[$key], $val);
        }

        return $arr1;
    }

    /**
     * Get the path to the migration directory.
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        $path = $this->input->getOption('path');

        // First, we will check to see if a path option has been defined. If it has
        // we will use the path relative to the root of this installation folder
        // so that migrations may be run for any path within the applications.
        if(!is_null($path)){
            return $this->laravel['path.base'] . '/' . $path;
        }

        $package = $this->input->getOption('package');

        // If the package is in the list of migration paths we received we will put
        // the migrations in that path. Otherwise, we will assume the package is
        // is in the package directories and will place them in that location.
        if(!is_null($package)){
            return $this->packagePath . '/' . $package . '/src/migrations';
        }


        return $this->laravel['path.base'] . '/database/migrations';
    }


    private function underscore($value)
    {
        return strtolower(str_replace('.', '_', $value));
    }
}