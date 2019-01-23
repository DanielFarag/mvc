<?php 
namespace Framework\Database;
use Framework\Database\Query;
use Framework\Base;
use Framework\Model;

abstract class Connector extends Base{
	/**
	* @readwrite
	*/
	protected $_host="localhost";
	
	/**
	* @readwrite
	*/
	protected $_database ="mvc";
	
	/**
	* @readwrite
	*/
	protected $_username ="root";
	
	/**
	* @readwrite
	*/
	protected $_password = "";
	
	/**
	* @readwrite
	*/
	protected $_schema = "";
	
	/**
	* @readwrite
	*/
	protected $_port = "3306";
	
	/**
	* @readwrite
	*/
	protected $_charset = "utf8";
	
	/**
	* @readwrite
	*/
	protected $_engine = "InnoDB";
	
	/**
	* @readwrite
	*/
	protected $_service;

	/**
	* @readwrite
	*/
	protected $_isConnected = false;
	
	
	abstract protected function _isValidService($throwException = true);
	
	
	abstract public function connect();
	
	
	public function disconnect(){
		if($this->_isValidService(false)){
			$this->isConnected =false;
			$this->_service = null;
		}
		return $this;
	}
	
	abstract public function query():Query;
	
	abstract public function execute(String $sql);
	
	abstract public function escape($value);
	
	abstract public function getLastInserted();
	
	abstract public function getLastError();
	
	public function sync(Model $model){
		$lines = [];
		$indices = [];
		$columns = $model->columns;
		$template = "CREATE TABLE %s (".PHP_EOL."%s,".PHP_EOL."%s".PHP_EOL.") ENGINE=%s DEFAULT CHARSET=%s";
		foreach($columns as $column){
			$raw = $column['raw'];
			$name = $column['name'];
			$type = $column['type'];
			$length = $column['length'];
			if($column['primary']){
				$indices[] = "PRIMARY KEY ({$name})";
			}
			if($column['index']){
				$indices[] = "KEY {$name} ({$name})";
			}
			
			switch($type){
				case 'autonumber':
					$lines[] ="{$name} int(11) NOT NULL AUTO_INCREMENT";
					break;
				case 'text':
					if($length != null && $length<=255){
						$lines[] ="{$name} varchar({$length}) DEFAULT NULL";
					}else{
						$lines[] ="{$name} text";
					}
					break;
				case 'integer':
					$lines[] = "{$name} int(11) DEFAULT NULL";
					break;
				case 'decimal':
					$lines[] = "{$name} tinyint(4) DEFAULT NULL";
					break;
				case 'datetime':
					$lines[] = "{$name} datetime DEFAULT NULL";
					break;
				case 'boolean':
					$lines[] = "{$name} tinyint(1) DEFAULT NULL";
					break;
			}
		}
		$table = new \ReflectionClass($model->table);
		$table=$table->getShortName();
		$sql = sprintf($template,$table,join(','.PHP_EOL,$lines),join(','.PHP_EOL,$indices),$this->_engine,$this->_charset);
		$path = \Framework\Path::Database('CREATE_TABLE_'.$table.'_'.date('Y_M_d_H_i_s').'.sql');
		$file = fopen($path,"w");
		fwrite($file,$sql);
		fclose($file);
		echo '<b>Created at</b>: '.$path;
	}
}

