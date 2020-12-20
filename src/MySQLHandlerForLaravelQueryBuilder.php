<?php

namespace DDA58\MySQLHandlerForLaravelQueryBuilder;

use \DDA58\MySQLHandlerForLaravelQueryBuilder\Contracts\MySQLHandlerRepositoryContract;

use \Illuminate\Database\Query\Builder as Builder;
use Illuminate\Support\Collection;

use \Exception as Exception;
use \PDO as PDO;
use \ReflectionMethod as ReflectionMethod;

/**
* class MySQLHandlerForLaravelQueryBuilder
* @package DDA58\MySQLHandlerForLaravelQueryBuilder
*/
class MySQLHandlerForLaravelQueryBuilder
{
	/**
	* @var MySQLHandlerRepositoryContract      
	*/  
	private $repository;

	/**
	* @var array      
	*/     
	private $allowedComparisonSymbols = ['=', '<=', '>=', '<', '>'];

	/**
	* @var array      
	*/   
	private $allowedKeyWordsForIndex = ['FIRST', 'NEXT', 'PREV', 'LAST'];

	/**
	* @var array      
	*/    
	private $allowedKeyWordsForHandler = ['FIRST', 'NEXT'];   

	/**      
	* MySQLHandlerForLaravelQueryBuilder constructor.      
	*      
	* @param MySQLHandlerRepositoryContract $repository      
	*/  
	public function __construct(MySQLHandlerRepositoryContract $repository)
	{
		$this->repository = $repository;
	}

    /**
     * Add handler methods to QueryBuilder 
     *
     * @return void
     */
    public function initHandlerMethodsForQueryBuilder() : void {
		$handler = $this;

		Builder::macro('openHandler', function (string $sAlias = '') use($handler) : Builder
		{
			$sTableName = $this->from;
			$sHandlerName = $sAlias ? $sAlias : $sTableName;
			$this->handlerName = $sHandlerName;

			if( $handler->getRepository()->get($sTableName)->isEmpty() ) {
				$sCommand = 'HANDLER `'.$sTableName.'` OPEN AS `'.$sHandlerName.'`;';
				$oPDO = $this->getConnection()->getPdo();
				$oPDO->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
				$oPDO->query($sCommand);
				$handler->getRepository()->set($sTableName, $sHandlerName);
			}

			return $this;
		});

		Builder::macro('getHandler', function () use($handler) : Builder
		{
			$sTableName = $this->from;

			if( $handler->getRepository()->get($sTableName)->isNotEmpty() )
				$this->handlerName = $handler->getRepository()->get($sTableName)->get(0)->handlerName;
			else
				return $this->openHandler($sTableName);

			return $this;
		});

		Builder::macro('closeHandler', function () use($handler) : Builder
		{
			$sTableName = $this->from;

			if( $handler->getRepository()->get($sTableName)->isNotEmpty() ) {
				$sCommand = 'HANDLER `'.$sTableName.' CLOSE;';
				$oPDO = $this->getConnection()->getPdo();
				$oPDO->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
				$oPDO->query($sCommand);				
				$this->handlerName = $handler->getRepository()->remove($sTableName);
			}

			return $this;
		});

        Builder::macro('read', function (?string $sIndexName, $mIndexValue,
        	?string $sKeyWord = '=') use($handler) : Collection
        {
			if(!$this->handlerName)
                throw new Exception('This \Illuminate\Database\Query\Builder have not handlerName');

            $sWhere = '';
            $sLimit = '';
            $sOffset = '';
            $aBindedValues = [];

            $sWhere = $handler->compileQueryCondition($this, 'compileWheres');

            if($this->limit)
            	$sLimit = $handler->compileQueryCondition($this, 'compileLimit', $this->limit);

            if($this->limit && $this->offset)
                $sOffset = $handler->compileQueryCondition($this, 'compileOffset', $this->offset);

            $sConditions = $sWhere.' '.$sLimit.' '.$sOffset;

            //HANDLER `{handler_name}` READ
            $sCommand = 'HANDLER `'.$this->handlerName.'` READ ';

			if($sIndexName) {
				//HANDLER `{handler_name}` READ `{index_name}` 
				$sCommand .= '`'.$sIndexName.'` ';
				if( in_array( $sKeyWord, $handler->getAllowedComparisonSymbols() ) ) {
					//HANDLER `{handler_name}` READ `{index_name}` { = | <= | >= | < | > } (
					$sCommand .= $sKeyWord.' (';
					//HANDLER `{handler_name}` READ `{index_name}` { = | <= | >= | < | > } (?
					if(is_array($mIndexValue))
						foreach ($mIndexValue as $mVal) {
							$sCommand .= '?,';
							$aBindedValues[] = $mVal;
						}
					else {
						$sCommand .= '?';
						$aBindedValues[] = $mIndexValue;
					}
					//HANDLER `{handler_name}` READ `{index_name}` { = | <= | >= | < | > } (?)
					$sCommand = trim($sCommand, ',').') ';				
				} elseif ( in_array( $sKeyWord, $handler->getAllowedKeyWordsForIndex() ) )
					//HANDLER `{handler_name}` READ `{index_name}` { FIRST | NEXT | PREV | LAST }
					$sCommand .= $sKeyWord.' ';
				else
					throw new Exception('Keyword '.(string)$sKeyWord.' not allowed for MySQL handler');
            }else {
            	if(!in_array($sKeyWord, $handler->getAllowedKeyWordsForHandler()))
					throw new Exception('Keyword '.(string)$sKeyWord.' not allowed for MySQL handler');
				//HANDLER `{handler_name}` READ { FIRST | NEXT }
				$sCommand .= $sKeyWord.' ';
            }

			foreach ($this->wheres as $aWhere) {
				$aBindedValues[] = $aWhere['value'];
			}
			//HANDLER `{handler_name}` READ ... WHERE ... LIMIT ... OFFSET
            $sCommand .= $sConditions;

            $oConnection = $this->getConnection()->getPdo();
            $oQuery = $oConnection->prepare($sCommand);
            $oQuery->execute($aBindedValues);
            $aResult = $oQuery->fetchAll(PDO::FETCH_OBJ);

            $collection = new Collection();
            foreach($aResult as $item){
				$collection->push($item);
            }

            return $collection;
        });

		Builder::macro('readFirst', function (?string $sIndexName = null) : Collection
		{
			return $this->read($sIndexName, null, 'FIRST');
		});

		Builder::macro('readLast', function (string $sIndexName) : Collection
		{
			return $this->read($sIndexName, null, 'LAST');
		});

		Builder::macro('readNext', function (?string $sIndexName = null) : Collection
		{
			return $this->read($sIndexName, null, 'NEXT');
		});

		Builder::macro('readPrev', function (string $sIndexName) : Collection
		{
			return $this->read($sIndexName, null, 'PREV');
		});

		Builder::macro('readPrimary', function ($mIndexValue, string $sKeyWord = '=') : Collection
		{
			return $this->read('PRIMARY', $mIndexValue, $sKeyWord);
		});
    }

	/**
	* Complile where, limit and offset condition strings by laravel methods
	*
	* @param Builder $builder
	* @param string $methodName
	* @param $params
	* @return string
	*/
	public function compileQueryCondition(Builder $builder, string $methodName, ...$params) : string {
        $reflectionMethod = new ReflectionMethod($builder->grammar, $methodName);
        $reflectionMethod->setAccessible(true);
        return $reflectionMethod->invoke($builder->grammar, $builder, ...$params);
	}

	/**
	* Get repository with opened handlers
	*
	* @return MySQLHandlerRepositoryContract
	*/
	public function getRepository() : MySQLHandlerRepositoryContract {
		return $this->repository;
	}

	/**
	* Get allowed comparison symbols for handler when it os using with index name 
	*
	* @return array
	*/
	public function getAllowedComparisonSymbols() : array {
		return $this->allowedComparisonSymbols;
	}

	/**
	* Get allowed keywords for handler when it is using without index name
	*
	* @return array
	*/
	public function getAllowedKeyWordsForHandler() : array {
		return $this->allowedKeyWordsForHandler;
	}

	/**
	* Get allowed keywords for handler when it is using with index name
	*
	* @return array
	*/
	public function getAllowedKeyWordsForIndex() : array {
		return $this->allowedKeyWordsForIndex;
	}
}