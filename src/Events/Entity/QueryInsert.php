<?php
namespace Lavender\Events\Entity;

use Illuminate\Queue\SerializesModels;
use Lavender\Database\QueryBuilder;

class QueryInsert
{
	use SerializesModels;

	public $query;

	public $values;

	public function __construct(QueryBuilder $query, $values)
	{
		$this->query = $query;

		$this->values = $values;
	}


}
