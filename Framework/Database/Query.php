<?php
namespace Framework\Database;
use Framework\Base;
use Framework\StringMethods;
use Framework\ArrayMethods;

abstract class Query extends Base{
	/**
	* @readwrite
	*/
	protected $_connector;
	
	/**
	* @read
	*/
	protected $_from;
	
	/**
	* @read
	*/
	protected $_fields;
	
	/**
	* @read
	*/
	protected $_limit;
	
	/**
	* @read
	*/
	protected $_offset;
	
	/**
	* @read
	*/
	protected $_order;
	
	/**
	* @read
	*/
	protected $_direction;

	/**
	* @read
	*/
	protected $_join = [];
	
	/**
	* @read
	*/
	protected $_where = [];
	
	public function from($from,$fields = ['*']){
		if(empty($from)){
			throw new \Exception("Invalid argument");
		}
		$this->_from = $from;
		if($fields){
			$this->_fields[$from] = $fields;
		}
		return $this;
	}
	
	public function where(){
		$arguments = func_get_args();
		if(sizeof($arguments)==0){
			throw new \Exception("Invalid Argument");
		}
		$arguments[0] = StringMethods::replace($arguments[0],"[\?]","%s");

		foreach(array_slice($arguments,1,null,true) as $i=>$parameter){
			$arguments[$i] = $this->_quote($arguments[$i]);
		}
		$this->_where[] = call_user_func_array("sprintf", $arguments);
		return $this;
	}
	
	public function limit($limit,$page=1){
		if(empty($limit)){
			throw new \Exception("Invalid argument");
		}
		$this->_limit = $limit;
		$this->_offset = $this->_limit * ($page -1);
		return $this;
	}
	
	public function order($order,$direction = 'asc'){
		if(empty($order)){
			throw new \Exception("Invalid argument");
		}
		$this->_order = $order;
		$this->_direction = $direction;
		return $this;
	}
	
	public function join($join,$on,$fields = []){
		if(empty($join) || empty($on)){
			throw new \Exception("Invalid argument");
		}
		
		$this->_fields += [$join=>$fields];
		$this->_join[]="JOIN {$join} ON {$on}";
		return $this;
	}
	
	protected function _quote($value){
		if(is_string($value)){
			return $this->connector->service->quote($value);
		}
		if(is_array($value)){
			$buffer = [];
			foreach($value as $i){
				array_push($buffer,$this->_quote($i));
			}
			$buffer = join(', ',$buffer);
			return "({$buffer})";
		}
		if(is_null($value)){
			return "NULL";
		}
		if(is_bool($value)){
			return (int) $value;
		}
		return $this->connector->service->quote($value);
	}
	
	protected function _buildSelect(){
		$fields = [];
		$where = $order = $limit = $join = "";
		$template = "SELECT %s FROM %s %s %s %s %s";
		
		// Create the retrieved fields
		foreach($this->fields as $table=>$_fields){
			foreach($_fields as $field=>$alias){
				if(is_string($field)){
					$fields[]= "{$field} as $alias";
				}else{
					$fields[] = $alias;
				}
			}
		}
		$fields = join(", ",$fields);
		
		// Create Join Statement
		$_join = $this->join;
		if(!empty($_join)){
			$join = join(" ",$_join);
		}
		
		// create Where Condition
		$_where = $this->where;
		if(!empty($_where)){
			$joined = join(" AND ",$_where);
			$where = "WHERE {$joined}";
		}
		
		
		// create Order By  
		$_order = $this->order;
		if(!empty($_order)){
			$_direction = $this->direction;
			$order= "ORDER BY {$_order} {$_direction}";
		}
		
		// create Limit and apply limit&offset 
		$_limit = $this->limit;
		if(!empty($_limit)){
			$_offset = $this->offset;
			if($_offset){
				$limit = "LIMIT {$_limit},{$_offset}";
			}else{
				$limit = "LIMIT {$_limit}";
			}
		}
		
		return sprintf($template,$fields,$this->from,$join,$where,$order,$limit);
	}
	
	protected function _buildInsert($data){
		$fields = [];
		$values = [];
		$template = "INSERT INTO %s (%s) VALUES (%s)";
		foreach($data as $field=>$value){
			$fields[]=$field;
			$values[]=$this->_quote($value);
		}
		$fields = join(", ",$fields);
		$values = join(", ",$values);
		return sprintf($template,$this->from,$fields,$values);
	}
	
	protected function _buildUpdate($data){
		$parts = [];
		$where = $limit = "";
		$template = "UPDATE %s SET %s %s %s";
		foreach($data as $field => $value){
			$parts[]= "{$field}=".$this->_quote($value);
		}
		$parts = join(', ',$parts);
		$_where = $this->where;
		if(!empty($_where)){
			$joined = join('AND ',$_where);
			$where = "WHERE {$joined}";
		}
		$_limit = $this->limit;
		if(!empty($_limit)){
			$_offset = $this->offset;
			$limit = "LIMIT {$_limit} {$_offset}";
		}
		return sprintf($template,$this->from,$parts,$where,$limit);
	}
	
	protected function _buildDelete(){
		$where =$limit = "";
		$template ="DELETE from %s %s %s";
		
		$_where = $this->where;
		if(!empty($_where)){
			$joined =join("AND ",$_where);
			$where = "WHERE {$joined}";
		}
		
		$_limit= $this->limit;
		if(!empty($_limit)){
			$_offset = $this->offset;
			$limit = "LIMIT {$_limit} {$_offset}";
		}
		return sprintf($template,$this->from,$where,$limit);
	}
	
	public function save($data){
		$isInsert = count($this->where) == 0;
		if($isInsert){
			$sql = $this->_buildInsert($data);
		}else{
			$sql = $this->_buildUpdate($data);
		}
		$result =$this->connector->execute($sql);
		if($result===false){
			throw new \Exception("Can't create the record");
		}
		if($isInsert){
			return $this->connector->getLastInserted();
		}
		return true;
	}
	
	public function delete(){
		$sql = $this->_buildDelete();
		echo $sql;
		$result = $this->connector->execute($sql);
		if($result===false){
			throw new \Exception("Can't delete the record");
		}
		return $result->rowCount();
	}
	
	public function first(){
		$limit = $this->_limit;
		$offset =$this->_offset;
		$this->limit(1);
		$first = $this->all()[0];
		if($limit){
			$this->_limit =$limit;
		}
		if($offset){
			$this->_offset= $offset;
		}
		return $first;
	}
	
	public function count(){
		$limit  = $this->_limit;
		$offset = $this->_offset;
		$fields = $this->_fields;
		$this->_fields = [$this->from=>["COUNT(*)"=>"rows"]];
		$this->limit(1);
		$row = $this->first();
		$this->_fields = $fields;
		if ($fields){
			$this->_fields = $fields;
		}
		if($limit){
			$this->_limit = $limit;
		}
		if($offset){
			$this->_offset = $offset;
		}
		return $row->rows;
	}
	
	public function all(){
		$sql = $this->_buildSelect();
		$result = $this->connector->execute($sql);
		if ($result === false){
			$error = $this->connector->lastError;
			throw new \Exception("There was an error with your SQL query: {$error[2]}");
		}
		$rows = $result->fetchAll(\PDO::FETCH_OBJ);
		return $rows;
	}
}