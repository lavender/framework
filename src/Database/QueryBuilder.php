<?php
namespace Lavender\Database;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Lavender\Events\Entity\QueryInsert;
use Lavender\Events\Entity\QuerySelect;
use Lavender\Support\Traits\AttributeTrait;
use Lavender\Support\Traits\RelationshipTrait;

class QueryBuilder extends Builder
{
    use AttributeTrait, RelationshipTrait;

    /**
     * Entity Configurations
     *
     * @var array
     */
    protected $config;

    /**
     * Create a new query builder instance.
     *
     * @param  \Illuminate\Database\ConnectionInterface $connection
     * @param  \Illuminate\Database\Query\Grammars\Grammar $grammar
     * @param  \Illuminate\Database\Query\Processors\Processor $processor
     * @param  array $config
     * @return \Lavender\Database\QueryBuilder
     */
    public function __construct(
        ConnectionInterface $connection,
        Grammar $grammar,
        Processor $processor,
        array $config = []
    )
    {
        $this->config = $config;

        parent::__construct($connection, $grammar, $processor);
    }

    /**
     * Add scope constraints before running select
     *
     * @return array
     */
    protected function runSelect()
    {
        event(new QuerySelect($this));

        return parent::runSelect();
    }

    /**
     * Insert a new record and get the value of the primary key.
     *
     * @param  array $values
     * @param  string $sequence
     * @return int
     */
    public function insertGetId(array $values, $sequence = null)
    {
        foreach(event(new QueryInsert($this, $values)) as $attributes){

            $values = array_merge($values, $attributes);

        }

        return parent::insertGetid($values, $sequence);
    }

    /**
     * @return array
     */
    public function config()
    {
        return $this->config;
    }
}