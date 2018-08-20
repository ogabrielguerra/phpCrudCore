<?
	Class AutoObj extends ConnectionAdmin {
		private $tableTarget; // Table target, used by the Class constructor
		private $fields=false;
		protected $resource;

		function __construct(string $tableTarget='', array $fields=array()){
            if(!$this->resource)
                $this->resource = parent::getConnRes();

			//Any fields passed?
			if(!empty($fields)){
				$this->fields = $fields;
			}else{
				//If not, get the fields from table automatically
				$this->Table = new Table($tableTarget);
			}

			$this->tableTarget = $tableTarget;
			$this->resetData();
		}

		//Resets data
		function resetData(){
			$this->data = array();
		}

		//Set query string
        function setQuery(array $fields, string $tableTarget, string $filter){
            $fieldsStr = implode(',', $fields);
            $this->query = '/* MYSQLND_QC_ENABLE_SWITCH */ SELECT '.$fieldsStr. ' FROM '.$tableTarget. ' WHERE '.$filter;
        }

		//Do the query against Db
        function getDataByQuery(array $fields, string $tableTarget, string $filter="", bool $debug=false) : PDOStatement{
            $this->setQuery($fields, $tableTarget, $filter);
            if($debug)
                $log = new Log("QUERY DEBUG", $this->query);
            try{
                $queryResult = $this->resource->prepare($this->query);
                $queryResult->execute();
                $this->rowCount = $queryResult->rowCount();
                return $queryResult;
            }
            catch(PDOException $exception){
				$view = new View();
				$view->defaultError();

                $logMsg = $exception->getMessage()."\t".$this->query;
                $log = new Log("ERROR", $logMsg);
                die();
            }
        }

		//Inserts registers in an array for further use
		function populator(PDOStatement $rs){
			while($item = $rs->fetch(PDO::FETCH_OBJ))
                array_push($this->data, $item);
		}

		//Put data into an array through populator()
		function getObjData($idOrFilter) : array{
			if (is_numeric($idOrFilter)) {
		        $filter = $this->tableTarget."_id = " . $idOrFilter;
		    } else {
		        $filter = $idOrFilter;
		    }

			//If the fields array was not created yet then create it
			if(!$this->fields)
				$this->fields = $this->Table->getCampos();//array("*");

			$rs = $this->getDataByQuery($this->fields, $this->tableTarget, $filter, false);

			if($this->rowCount){
		    	$this->populator($rs);
		    	return $this->data;
			}else{
				return array();
			}
		}

		//Get num total items
		function getTotalItensObj() : int{
			$index = $this->tableTarget.'_id';
			$filter = $index.' > 0 ORDER BY '.$index.' DESC';
			$this->getDataByQuery(array($index), $this->tableTarget, $filter, false);
			return $this->rowCount;
		}

		//Adds an item to Db
		function addObjToDb(array $values, string $urlRedirect, $allRequired=true){
			//Retrive fields of this table
		    $tb = new Table($this->tableTarget);
		    $dbFields = $tb->getFieldsWithoutIndex();

		    //Set the Values from POST
		    $valuesList = array();
		    foreach($values as $item){
				array_push($valuesList, $item);
		    }

		    //Fire the update method
		    $putData = new PutData();
		    $putData->addData($this->tableTarget, $dbFields, $valuesList, '', $urlRedirect, $allRequired, false);
		}

	}
?>
