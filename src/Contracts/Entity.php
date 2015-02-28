<?php
namespace Lavender\Contracts;

use Lavender\Contracts\Entity\Model;

interface Entity extends Model
{

    /**
     * @return array config
     */
    public function getAttributeConfig();

    /**
     * @return array config
     */
    public function getRelationshipConfig();

    /**
     * @return string
     */
    public function getScope();

    /**
     * Get the model's config name
     *
     * @return string
     */
    public function getEntityName();

}