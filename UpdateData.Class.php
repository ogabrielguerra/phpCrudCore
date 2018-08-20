<?
	class UpdateData extends ConnectionAdmin{
		public $resource;

		function __construct($db=""){
			if(!$this->resource){
				$this->resource = parent::getConnRes();
			}
		}

		function updateSingleData(array $arrayOfDbFields, array $arrayOfValues, string $tableTarget, string $myIdName, $myIdValue, string $redirectPage = "", string $feedbackMsg=""){
			$numFields = count($arrayOfDbFields);
			$numValues = count($arrayOfValues);

			//Builds query string
			for($i=0; $i<$numFields; $i++){
				@$values = $arrayOfValues[$i];

				if($i==0)
					$mixedVars = $arrayOfDbFields[$i]. " = '". $values."'";
				else
					$mixedVars .= ", ".$arrayOfDbFields[$i]. " = '". $values."'";
			}

			$query = "UPDATE IGNORE ".$tableTarget." SET ".$mixedVars. " WHERE ".$myIdName." = '".$myIdValue."'";

			try{
				$update = $this->resource->exec($query);
				if($redirectPage != ""){
					header("Location: ".$redirectPage);
				}
		    }
		    catch(PDOException $exception){
				$view = new View();
				$view->defaultError();

				$logMsg = $exception->getMessage()."\t".$this->query;
				$log = new Log("ERROR", $logMsg);
				die();
			}
		}
	}
?>
