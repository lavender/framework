<?php
namespace Lavender\Entity\Database;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;

class QueryBuilder extends Builder
{

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
     * @return \Lavender\Entity\Database\QueryBuilder
     */
    public function __construct(
        ConnectionInterface $connection,
        Grammar $grammar,
        Processor $processor,
        array $config = array()
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
        \Event::fire('entity.query.select', $this);

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
        $args[] = $this;

        $args[] = &$values;

        \Event::fire('entity.query.insert', $args);

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