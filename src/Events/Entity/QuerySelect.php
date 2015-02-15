<?php
namespace Lavender\Events\Entity;

use Illuminate\Queue\SerializesModels;
use Lavender\Database\QueryBuilder;

class QuerySelect
{
	use SerializesModels;

	public $query;

	public function __construct(QueryBuilder $query)
	{
		$this->query = $query;
	}

}
