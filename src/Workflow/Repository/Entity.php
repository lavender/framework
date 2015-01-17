<?php
namespace Lavender\Workflow\Repository;

use Lavender\Entity\Contracts\EntityInterface;
use Lavender\Workflow\Contracts\RepositoryInterface;

class Entity //implements RepositoryInterface
{

    /**
     * @param mixed $workflow
     * @return bool
     */
    public function hasWorkflow($workflow)
    {
        return $workflow instanceof EntityInterface;
    }

    /**
     * @param EntityInterface $workflow
     * @return string
     */
    public function getWorkflowId($workflow)
    {
        return sprintf("entity.%s", $workflow->getEntity());
    }

    /**
     * @param EntityInterface $workflow
     * @return array
     */
    public function getWorkflow($workflow)
    {
        $config = $workflow->getConfig();

        return [

            'default' => [

                'fields' => $config['fields'],
                'before' => $config['before'],
                'after' => $config['after'],
                'redirect' => null,

            ]

        ];
    }



}