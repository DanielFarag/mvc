<?php
namespace Framework\Database\Connector;
use Framework\Database\Connector;
use Framework\Database\Query;

class Mysql extends Connector{
	
	protected function _isValidService($throwException = true){
		$valid = false;
		if($this->isConnected && $this->service instanceof \PDO){
			$valid = true;
		}
		
		if(!$valid && $throwException){
			throw new \Exception("Not Connected to a valid service");
		}
		return $valid;
	}
	
	public function connect(){
		if (!$this->_isValidService(false)){
			try{
				$this->service = new \PDO("mysql:host={$this->host};dbname={$this->database}",$this->_username,$this->_password);
				$this->isConnected = true;
			}catch(\PDOException $ex){
				throw new \Exception("Unable to connect to service");
			}
		}
		return $this;
	}
	
	public function query():Query{
		return new Query\Mysql(["connector"=>$this]);
	}
	
	public function execute(String $sql){
		$this->_isValidService();
		return $this->service->query($sql);
	}
	
	public function escape($value){
		$this->_isValidService();
		return $this->service->quote($value);
	}
	
	public function getLastInserted(){
		$this->_isValidService();
		return $this->service->lastInsertId();
	}
	
	public function getLastError(){
		$this->_isValidService();
		return $this->service->errorInfo();
	}

}