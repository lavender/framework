<?php
namespace Lavender\Contracts\Entity;

use Lavender\Contracts\Entity;

interface Attribute
{

    /**
     * This class is instantiated with a clone of the active
     * Entity object and key that represents our attribute.
     * @param Entity $entity
     * @param $key
     */
    public function __construct(Entity $entity, $key);

    /**
     * Literal representation of the current value
     * @return mixed
     */
    public function value();

    /**
     * Value used in the backend
     * @return mixed
     */
    public function backend();

    /**
     * Handle value before it's saved
     * @param $value
     * @return mixed
     */
    public function before_save($value);

    /**
     * This object may be cast to string
     * @return mixed
     */
    public function __toString();

}