<?
class PutData extends ConnectionAdmin{
	private $resource;

	function __construct($db=""){
		if(!$this->resource){
			$this->resource = parent::getConnRes();
		}
	}

	function checkNumFieldsAgainstValues(int $numValues, int $numFields) : bool{
		if($numValues != $numFields){
			$HandleMsg = new HandleMsgs();
			$msg = "Number of fields and values must be equal.";
			$log = new Log("ERROR", $msg);
			$messaging = $HandleMsg->showErrorMsg($msg);
			return false;
		}else{
			return true;
		}
	}

	function checkIfAllRequired(array $arrayOfValues, bool $allRequired=true) : bool{
		if($allRequired){
			if(in_array("", $arrayOfValues, true))
				return false;
			else
				return true;
		}
	}

	//Obsolete method kept just for compatibility issues
	function CheckForEmptyValues($myArray, $feedBackMsg) : bool{
		$countArray = count($myArray);
		for($i=0; $i<$countArray; $i++){
			if($myArray[$i] == ""){
				return false;
			}
		}
	}

	function setFields(int $numFields, array $arrayOfDbFields){
		$fields = '';
		for($i=0; $i<$numFields; $i++){
			if($i == 0)
				$fields = $arrayOfDbFields[$i];
			else
				$fields .= ", ".$arrayOfDbFields[$i];
		}
		$this->fields = $fields;
	}

	function setValues(int $numFields, array $arrayOfValues){
		$myString = '';
		for($i=0; $i<$numFields; $i++){
			$newString = $arrayOfValues[$i];

			if($i == 0)
				$myString = "'".$newString."' ";
			else
				$myString .= ", '".$newString."' ";
		}
		$this->values = $myString;
	}

	function addData(string $tableTarget, array $arrayOfDbFields, array $arrayOfValues, string $feedbackMsg, string $redirectPage, bool $allRequired=true){
		$numFields = count($arrayOfDbFields);
		$numValues = count($arrayOfValues);

		//Pre Validations
		if($allRequired) {
            if (!$this->checkNumFieldsAgainstValues($numValues, $numFields))
                die('All fields required.');

            if (!$this->checkIfAllRequired($arrayOfValues))
                die('Empty values not allowed.');
        }

		//Set fields
		$this->setFields($numFields, $arrayOfDbFields);

		//Set String with Values
		$this->setValues($numFields, $arrayOfValues);
		$bindableString = ':'.implode(', :', $arrayOfDbFields);//'';

		//Set QueryString
		$queryDebug = 'insert into '.$tableTarget.' ('.$this->fields.') values ('.$this->values.');';
		$query = 'insert into '.$tableTarget.' ('.$this->fields.') values ('.$bindableString.');';

		try{
			$qr = $this->resource->prepare($query);
			//Bind all params
			for($i=0; $i<$numFields; $i++){
				$qr->bindParam(':'.$arrayOfDbFields[$i], $arrayOfValues[$i]);
			}

			$qr->execute();

        	//Sets the last inserted row
			$this->lastInserted = $this->resource->lastInsertId();

			//Se houver página para redireionamento definida, faz o redirect
			if($redirectPage != ""){
				//verifica se já existem parâmetros na URL e retorna ? ou &
				$check = new HandleStrings();
				$concat = $check->switchConcat($redirectPage);
				header("Location: ".$redirectPage.$concat);
			}else{
				return true;
			}
	    }catch(PDOException $exception){
				$view = new View();
				$view->defaultError();

				$logMsg = $exception->getMessage()."\t".$this->query;
				$log = new Log("ERROR", $logMsg);
				die();
	    }

	}

}
?>
