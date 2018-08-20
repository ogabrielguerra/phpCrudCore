<?
	class Table extends ConnectionAdmin{
		private $fields;
		private $types;
		protected $resource;

		function __construct(string $table=""){
			if(!$this->resource)
				$this->resource = parent::getConnRes();

			if(!empty($table)){
				$this->table = $table;
				$this->setCampos($table);
			}
		}

		//Gets the connection resource
		// function getRes() : PDO{
		// 	return $this->resource;
		// }

		//Excludes the key field from data
		function getFieldsWithoutIndex() : array {
			$newArray = array();
			$countFields = count($this->fields);
			for($i=1; $i<$countFields; $i++){
				array_push($newArray, $this->fields[$i]);
			}
			return $newArray;
		}

		//Set fields
		function setCampos(string $table){
			$query = "/* MYSQLND_QC_ENABLE_SWITCH */ SHOW COLUMNS FROM ".$table;
			$result = $this->resource->prepare($query);
			$result->execute();

			if ($result->rowCount()) {
				$fields = array();
				$types = array();

				while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
					array_push($fields, $row["Field"]);
					array_push($types, $row["Type"]);
				}
				$this->fields = $fields;
				$this->types = $types;
			}else{
				echo "Failed gathering fields info...";
			}
		}

		function printFields() : string{
			$f = "";
			for($i=0; $i<count($this->fields);$i++){
				$f .= "'".$this->fields[$i]."',";
			}
			echo $f;
		}

		//Get fields
		function getCampos() : array{
			return $this->fields;
		}

		//Get types
		function getTipos() : array{
			return $this->types;
		}
	}
?>
