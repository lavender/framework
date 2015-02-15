<?php
namespace Lavender\Events\Entity;

use Illuminate\Queue\SerializesModels;

class SchemaPrepare
{
	use SerializesModels;

	public $entity;

	public function __construct($entity)
	{
		$this->entity = $entity;
	}

}
