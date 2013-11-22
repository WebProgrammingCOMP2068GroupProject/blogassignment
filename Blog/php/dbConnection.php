<?php 
	try{
		$mySQLUsername = "db200240236";
		$mySQLPassword = "68069";
		$dsn = "mysql:host=localhost;dbname=db200240236";
		
		$database = new PDO($dsn, $mySQLUsername, $mySQLPassword);
	}
	catch(PDOException $ex){
		try{
			$mySQLUsername = "root";
			$mySQLPassword = "";
			$dsn = "mysql:host=localhost;dbname=db200213257";
		
			$database = new PDO($dsn, $mySQLUsername, $mySQLPassword);
		}
		catch(PDOException $ex)
		{
			//change this error display later
			echo "OOPS there was an error connecting to the database";
		}		
	}
?>	