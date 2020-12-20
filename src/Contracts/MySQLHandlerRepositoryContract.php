<?php

namespace DDA58\MySQLHandlerForLaravelQueryBuilder\Contracts;

use Illuminate\Support\Collection;

/**
* Interface MySQLHandlerRepositoryContract
* @package DDA58\MySQLHandlerForLaravelQueryBuilder
*/
interface MySQLHandlerRepositoryContract
{
    /**
    * Get item from storage by table name 
    *
    * @param string $tableName
    * @return Collection
    */
	public function get(string $tableName) : Collection;

    /**
    * Set item to storage
    *
    * @param string $tableName
    * @param string $handlerName
    * @return Collection
    */
	public function set(string $tableName, string $handlerName = '') : Collection;

	/**
	* Remove item from storage by table name
	*
	* @param $tableName
	* @return Collection
	*/
	public function remove(string $tableName) : Collection;
}