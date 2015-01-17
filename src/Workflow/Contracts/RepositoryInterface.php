<?php
namespace Lavender\Workflow\Contracts;

interface RepositoryInterface
{

    /**
     * @param mixed $workflow
     * @return bool
     */
    public function hasWorkflow($workflow);

    /**
     * @param mixed $workflow
     * @return array
     * example: [
     *      "fields" =>[]
     *      "before" =>[]
     *      "after" =>[]
     *      "redirect" =>""
     *  ]
     */
    public function getWorkflow($workflow);


    public function getWorkflowId($workflow);

}