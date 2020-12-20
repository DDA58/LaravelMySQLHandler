<?php   

namespace DDA58\MySQLHandlerForLaravelQueryBuilder;   

use DDA58\MySQLHandlerForLaravelQueryBuilder\Contracts\MySQLHandlerRepositoryContract;
use Illuminate\Support\Collection;

/**
* class MySQLHandlerRepository
* @package DDA58\MySQLHandlerForLaravelQueryBuilder
*/
class MySQLHandlerRepository implements MySQLHandlerRepositoryContract 
{     
    /**      
     * @var Collection      
     */     
    protected $storage;       

    /**      
     * MySQLHandlerRepository constructor.      
     *      
     * @param Collection $storage      
     */  
    public function __construct(Collection $storage)     
    {         
        $this->storage = $storage;
    }
 
    /**
    * Get item from storage by table name 
    *
    * @param string $tableName
    * @return Collection
    */
    public function get(string $tableName) : Collection
    {
        return $this->storage->where('tableName', $tableName);
    }

    /**
    * Set item to storage
    *
    * @param string $tableName
    * @param string $handlerName
    * @return Collection
    */
    public function set(string $tableName, string $handlerName = '') : Collection
    {
        $item = new \stdClass();
        $item->tableName = $tableName;
        $item->handlerName = $handlerName ? $handlerName : $tableName;
        return $this->storage->push($item);
    }
 
    /**
    * Remove item from storage by table name
    *
    * @param $tableName
    * @return Collection
    */
    public function remove(string $tableName) : Collection
    {
        foreach ($this->get($tableName) as $key => $oItem) {
            $this->storage->forget($key);
        }
        return $this->storage;
    }
}