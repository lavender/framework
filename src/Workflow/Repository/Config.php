<?php
namespace Lavender\Workflow\Repository;

use Lavender\Workflow\Contracts\RepositoryInterface;

class Config //implements RepositoryInterface
{
    protected $config;

    /**
     * Populate the config parameter
     */
    public function __construct()
    {
        $config = \Config::get('workflow');

        foreach($config as $workflow => &$states){

            sort_children($states);

        }

        $this->config = $config;
    }

    /**
     * @param mixed $workflow
     * @return bool
     */
    public function hasWorkflow($workflow)
    {
        return is_string($workflow) && isset($this->config[$workflow]);
    }

    /**
     * @param mixed $workflow
     * @return array
     */
    public function getWorkflow($workflow)
    {
        return $this->config[$workflow];
    }

    /**
     * @param EntityInterface $workflow
     * @return string
     */
    public function getWorkflowId($workflow)
    {
        return sprintf("workflow.%s", $workflow);
    }

}