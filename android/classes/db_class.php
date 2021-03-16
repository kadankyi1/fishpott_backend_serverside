<?php

class dbConnect {

		private $mysqli;

 
		public function connectToDatabase($set_charset, $LIVE_MODE) {

			if($LIVE_MODE === true){
				// FISHPOTT LIVE MODE
				$hosting = "localhost";
				$user = "r3dph03n_y3nfish";
				$db_connect_password = "g0tt6h6v31t";
				$database = "r3dph03n_awafishpot";
				//echo "\n LIVE MODE";
			} else {
				// FISHPOTT DEVELOPMENT MODE
				$hosting = "localhost";
				$user = "r3dph03n_fp_devu";
				$db_connect_password = "g0d6ppr06ch";
				$database = "r3dph03n_fp_devmode";
				//echo "\n DEVELOPMENT MODE";
			}
			
			$mysqli = new mysqli($hosting, $user, $db_connect_password, $database);

			if($set_charset == 1){
				$mysqli->set_charset('utf8mb4');
			}

			/* check connection */
			if ($mysqli->connect_errno) {
				// IF DATABASE CONNECTION FAILED
				return false;
			} else {
				// IF DATABASE CONNECTION WAS SUCCESSFUL.
				return $mysqli;
			}

		} // END OFconnectToDatabase

 		public function getDatabaseConnection() {		

			/* check connection */
			if ($mysqli->connect_errno) {
				// IF DATABASE CONNECTION FAILED
				return false;
			} else {
				// IF DATABASE CONNECTION WAS SUCCESSFUL.
				return $mysqli;
			}

		 } // END OF getDatabaseConnection

		public function checkDatabaseConnection() {		

			/* check connection */
			if ($mysqli->connect_errno) {
				// IF DATABASE CONNECTION FAILED
				return false;
			} else {
				// IF DATABASE CONNECTION WAS SUCCESSFUL.
				return true;
			}

		 }	// END OF checkDatabaseConnection

		 public function checkIfServerIsAlive(){

			/* check if server is alive */
			if ($mysqli->ping()) {
				return true;
			} else {
				return false;
			}

		 }	// end of checkIfServerIsAlive

		 public function closeDatabaseConnection($mysqli){

		 	$mysqli->close();

		 } // END OF closeDatabaseConnection
 
}	