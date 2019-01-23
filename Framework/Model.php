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
	
	protected $_columns;
	protected $_primary;
	
	public function __construct($options = []){
		parent::__construct($options);
		$this->connector = Registry::get('database');
		$this->load();
	}
	public function load(){
		$primary = $this->primaryColumn;
		$raw = $primary['raw'];
		$name = $primary['name'];
		if(!empty($this->$raw)){
			$previous = $this->connector->query()->from($this->table)->where("{$name}=?",$this->$raw)->first();
			if($previous==null){
				throw new \Exception("Primary key value invalid");
			}
			foreach($previous as $key=>$value){
				$prop = "_{$key}";
				if(!empty($previous->$key) && !isset($this->$prop)){
					$this->$key = $previous->$key;
				}
			}
		}
	}
	
	public static function all($where = [],$fields=['*'],$order=null,$direction=null,$limit=null,$page=null){
		$model = new static();
		return $model->_all($where,$fields,$order,$direction,$limit,$page);
	}
	protected function _all($where=[],$fields=['*'],$order=null,$direction=null,$limit=null,$page=null){
		$query= $this->connector->query()->from($this->table,$fields);
		foreach($where as $clause=>$value){
			$query->where($class,$value);
		}
		if($order!=null){
			$query->order($order,$direction);
		}
		if($limit!=null){
			$query->limit($limit,$page);
		}
		$rows = [];
		$class = get_class($this);
		foreach($query->all() as $row){
			$rows[] = new $class($row);
		}
		return $rows;
	}
	
	public static function count($where = []){
		$model = new static();
		return $model->_count($where);
	}
	protected function _count($where =[]){
		$query = $this->connector->query()->from($this->table);
		foreach($where as $clause=>$value){
			$query->where($clause,$value);
		}
		return $query->count();
	}
	public static function first($where = [],$fields=['*'],$order=null,$direction=null,$limit=null,$page=null){
		$model = new static();
		return $model->_first($where,$fields,$order,$direction,$limit,$page);
	}
	protected function _first($where=[],$fields=['*'],$order=null,$direction=null,$limit=null,$page=null){
		$query = $this->connector->query()->from($this->table,$fields);
		foreach($where as $clause => $value){
			$query->where($clause,$value);
		}
		if($order!=null){
			$query->order($order,$direction);
		}
		$first=$query->first();
		$class = get_class($this);
		if($first){
			return new $class($first);
		}
		return null;
	}
	public function save(){
		$primary = $this->primaryColumn;
		$raw = $primary['raw'];
		$name = $primary['name'];
		$query = $this->connector->query()->from($this->table);
		// If is not empty, update the current record
		if(!empty($this->$raw)){
			$query->where("{$name}=?",$this->$raw);
		}
		$data =[];
		foreach($this->columns as $key=>$column){
			// Read Only Property can't be accessed using getProperty function
			if(!$column['read']){
				$prop = $column["raw"];
				$data[$key] = $this->$prop;
				continue;
			}
			if($column!=$this->primaryColumn && $column){
				$method = "get".ucfirst($key);
				$data[$key]= $this->$method();
				continue;
			}
		}
		$result = $query->save($data);
		if($result>0){
			$this->$raw=$result;
		}
		return $result;
	}
	public function delete(){
		$primary = $this->primaryColumn;
		$raw = $primary["raw"];
		$name = $primary["name"];
		if(!empty($this->$raw)){
			return $this->connector->query()->from($this->table)->where("{$name}=?",$this->$raw)->delete();
		}
	}
	public static function deleteAll($where=[]){
		$instance = new static([
			'connector'=>Registry::get('database')
		]);
		$query = $instance->connector->query()->from($instance->table);
		foreach($where as $clause=>$value){
			$query->where($clause,$value);
		}
		return $query->delete();
	}
	public function getTable(){
		if(empty($this->_table)){
			$table = new \ReflectionClass(get_class($this));
			$this->_table=strtolower($table->getShortName());
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