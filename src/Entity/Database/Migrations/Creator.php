<?php
namespace Lavender\Entity\Database\Migrations;

use Illuminate\Filesystem\Filesystem;

class Creator
{

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new migration creator instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem $files
     * @return \Lavender\Entity\Database\Migrations\Creator
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * Create a new migration at the given path.
     *
     * @param  string $name
     * @param  string $path
     * @param $migrations
     * @return string
     */
    public function create($name, $path, $migrations)
    {
        $path = $this->getPath($name, $path);

        $ups = [];

        $downs = [];

        if(isset($migrations['create'])){

            foreach($migrations['create'] as $table => $data){

                $ups[] = $this->populateStub('create', [
                    'table' => $table,
                    'columns' => $this->format($data['cols'])
                ]);

                $downs[] = $this->populateStub('drop', ['table' => $table]);
            }
        }

        if(isset($migrations['update'])){

            foreach($migrations['update'] as $table => $data){

                $ups[] = $this->populateStub('update', [
                    'table' => $table,
                    'columns' => isset($data['cols']) ? $this->format($data['cols']) : null,
                    'foreign_keys' => isset($data['fks']) ? $this->format($data['fks']) : null
                ]);

                $downs[] = $this->populateStub('down', [
                    'table' => $table,
                    'columns' => isset($data['cols']) ? $this->format($this->drop(array_keys($data['cols']), 'Column')) : null,
                    'foreign_keys' => isset($data['fks']) ? $this->format($this->drop(array_keys($data['fks']), 'Foreign')) : null
                ]);
            }
        }

        $ups = array_filter($ups, 'strlen');
        $downs = array_filter($downs, 'strlen');

        if($ups){

            $this->files->put($path, $this->populateStub('blank', [
                'class' => studly_case($name),
                'up' => $this->format($ups, ''),
                'down' => $this->format(array_reverse($downs), '')
            ]));

            return $path;
        }

        return false;
    }

    protected function drop($cols, $type = 'Column')
    {
        return array_map(function ($col) use ($type){ return '$table->drop' . $type . '("' . $col . '");'; }, $cols);
    }

    protected function populateStub($stub, array $data)
    {
        if(($stub == 'update' || $stub == 'down') && (!$data['columns'] && !$data['foreign_keys'])) return null;
        $stub = $this->files->get($this->getStubPath() . '/' . $stub . '.stub');

        $keys = array_map(function ($k){ return "{{" . $k . "}}"; }, array_keys($data));

        $vals = array_map([$this, 'format'], $data);

        return str_replace($keys, $vals, $stub);
    }

    /**
     * Get the full path name to the migration.
     *
     * @param  string $name
     * @param  string $path
     * @return string
     */
    protected function getPath($name, $path)
    {
        return $path . '/' . $this->getDatePrefix() . '_' . $name . '.php';
    }

    /**
     * Get the date prefix for the migration.
     *
     * @return string
     */
    protected function getDatePrefix()
    {
        return date('Y_m_d_His');
    }

    /**
     * Get the path to the stubs.
     *
     * @return string
     */
    public function getStubPath()
    {
        return __DIR__ . '/stubs';
    }

    protected function format($v, $indent = "\t\t\t")
    {
        return is_array($v) ? implode(PHP_EOL . $indent, $v) : $v;
    }
}