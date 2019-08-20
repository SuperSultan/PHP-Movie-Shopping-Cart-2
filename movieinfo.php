<?php 

error_reporting(-1);
ini_set('display_errors', 'On');

session_start(); // Connect to the existing session
require_once '/home/common/dbInterface.php'; // Add database functionality
processPageRequest(); // Call the processPageRequest() function

function createMessage($movieId) {

	$array = getMovieData($movieId);
	ob_start(); // Create an output buffer
	require_once './templates/movie_info.html';
	$message = ob_get_contents(); // Get the contents of the output buffer
	ob_end_clean(); // Clear the output buffer
	echo $message;
}

function processPageRequest() {

	if ( !$_SESSION['displayName'] ) {
		header('Location: ./logon.php');
	}
	if ( !empty($_GET) ) {
		if ( isset($_GET['movie_id']) ) {
			createMessage($_GET['movie_id']);
		}
	}
	elseif ( empty($_GET) ) {
		createMessage(0);
	}
}

?>
