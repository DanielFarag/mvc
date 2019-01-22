<?php 
namespace Framework\Database;
use Framework\Database\Query;
use Framework\Base;

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