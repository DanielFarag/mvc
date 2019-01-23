<?php
namespace Framework;

class Model extends Base{
	
	/**
	* @readwrite
	*/
	protected $_table;
	
	/**
	* @readwrite
	*/
	protected $_connector;
	
	/**
	* @read
	*/
	protected $_types = [
		"autonumber",
		"text",
		"integer",
		"decimal",
		"boolean",
		"datetime"
	];
	
	/**
	* @column
	* @readwrite
	* @primary
	* @type autonumber
	* @validate integer,unique-users-id
	*/
	protected $_id;
	
	/**
	* @column
	* @readwrite
	* @type text
	* @length 50
	* @label FirstName	
	* @index
	*/
	protected $_f_name;
	
	/**
	* @column
	* @readwrite
	* @type text
	* @length 50
	* @label LastNAme	
	* @index
	*/
	protected $_l_name;
	
	/**
	* @column
	* @readwrite
	* @type text
	* @length 64
	* @label Email	
	* @index
	*/
	protected $_email;
	
	/**
	* @column
	* @readwrite
	* @type text
	* @length 64
	* @label Password
	*/
	protected $_password;
	
	/**
	* @column
	* @readwrite
	* @type datetime
	* @label CreatedAt
	*/
	protected $_created_at;
	
	/**
	* @column
	* @readwrite
	* @type datetime
	* @label UpdatedAt	
	*/
	protected $_updated_at;
	
	/**
	* @column
	* @readwrite
	* @type datetime
	* @label DeletedAt
	*/
	protected $_deleted_at;	
	
	/**
	* @column
	* @readwrite
	* @type boolean
	* @label Active
	*/
	protected $_active;
	
	protected $_columns;
	protected $_primary;
	
	public function getTable(){
		if(empty($this->_table)){
			$this->_table = strtolower(get_class($this));
		}
		return $this->_table;
	}
	public function getConnector(){
		if(empty($this->_connector)){
			$database= Registry::get("database");
			if(!$database) throw new \Exception("No connector available");
			$this->_connector = $database->initialize();
		}
		return $this->_connector;
	}
	
	public function getColumns(){
		if(empty($this->_columns)){
			$primaries = 0;
			$columns = [];
			
			$class = get_class($this);
			$types=  $this->types;

			$inspector = new Inspector($this);
			$properties = $inspector->getClassProperties();
			
			$first = function($array,$key){
				if(!empty($array[$key]) && count($array[$key])==1){
					return $array[$key][0];
				}
				return null;
			};
			
			foreach($properties as $property){
				$propertyMeta = $inspector->getPropertyMeta($property);
				if(!empty($propertyMeta['@column'])){
					// Gather all information needed to create and treat the column
					$name = StringMethods::replace($property,'^_','');
					$primary = !empty($propertyMeta['@primary']);
					$type = $first($propertyMeta,'@type');
					$length = $first($propertyMeta,'@length');
					$index = !empty($propertyMeta['@index']);
					$readwrite = !empty($propertyMeta['@readwrite']);
					$read = !empty($propertyMeta['@read']) || $readwrite;
					$write = !empty($propertyMeta['@write']) || $readwrite;
					$validate = !empty($propertyMeta['@validate']) ? $propertyMeta['@validate']:false;
					$label = $first($propertyMeta,'@label');
					
					if(!in_array($type,$types)){
						throw new \Exception(strtoupper($type)." is not a valid data type. choose between:( ".strtoupper(join(' - ',$this->_types)).')');
					}
					if($primary){
						$primaries++;
					}
					$columns[$name]=[
						"raw" => $property,
						"name"=> $name,
						"primary" =>$primary,
						"type"=>$type,
						"length"=>$length,
						"index"=>$index,
						"read"=>$read,
						"write"=>$write,
						"validate"=>$validate,
						"label"=>$label
					];
				}
			}
			if($primaries !==1){
				throw new \Exception("{$class} must have only one @primary column");
			}
			$this->_columns =$columns;
		}
		return $this->_columns;
	}
	
	public function getColumn($name){
		if(!empty($this->columns[$name])){
			return $this->columns[$name];
		}
		return null;
	}
	public function getPrimaryColumn(){
		if(!isset($this->_primary)){
			foreach($this->columns as $column){
				if($column["primary"]){
					$this->_primary = $column;
					break;
				}
			}
		}
		return $this->_primary;
	}
}