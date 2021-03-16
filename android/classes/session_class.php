<?php

class sessionsActions {

	public function startSession($session_id, $inactive_time){
		ini_set('session.gc_maxlifetime', $inactive_time); // set the session max lifetime to 5 seconds
		session_id($session_id);
		session_start();
		$_SESSION['started'] = 1;
		if (!isset($_SESSION['session_time'])) {
			$_SESSION['session_time'] = time();
		}

	}  // END OF startSession

	public function sessionIsNoLongerValid($maximum_allowed_session_time) {
		if (isset($_SESSION['session_time'])) {
			if(time() - $_SESSION['session_time'] > $maximum_allowed_session_time) {
			    // last request was more than 5 seconds ago
			    session_unset();     // unset $_SESSION variable for this page
			    session_destroy();   // destroy session data
			    return true;
			} else {
				return false;
			}

		} else {
			return true;
		}

	} // END OF sessionIsNoLongerValid

	public function destroySession() {

			    session_unset();     // unset $_SESSION variable for this page
			    session_destroy();   // destroy session data

	} // END OF sessionIsNoLongerValid


}