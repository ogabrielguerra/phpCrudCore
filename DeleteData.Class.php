<?
class DeleteData extends ConnectionAdmin{
	private $resource;
	private $msg;

	function __construct(){
		if(!$this->resource){
			$this->resource = parent::getConnRes();
		}
	}

	function deleteById(string $myIdName, int $myIdValue, string $tableTarget, string $redirectPage="", string $feedbackMsg=""){
		
		$query = 'DELETE FROM '.$tableTarget.' WHERE '.$myIdName.' = \''.$myIdValue.'\'';

		try{
			$this->resource->exec($query);

			//Implements messaging system
			if(!empty($feedbackMsg)){
				//Do something someday
			}

			//Redirect if necessary
			if(!empty($redirectPage)){
				header("Location: ".$redirectPage);
				die();
			}

		}catch(PDOException $exception){
			$view = new View();
			$view->defaultError();

			$logMsg = $exception->getMessage()."\t".$this->query;
			$log = new Log("ERROR", $logMsg);
			return false;
	    }
	}
}
?>
