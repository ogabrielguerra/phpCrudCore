<?
	class ConnectionAdmin{
		private static $resource=false;

		private static function initialize(){
			if(!self::$resource){
				$con = new PDO("mysql:host=".HOST.";dbname=".DATABASE.";charset=utf8mb4", USERNAME, PASSWORD);
				$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$con->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
				self::$resource = $con;
			}
		}

		public static function getConnRes() : PDO{
			self::initialize();
			return self::$resource;
		}
	}
?>
