<?php


error_reporting(-1);
ini_set('display_errors', 'On');

session_start(); // Connect to the existing session
require_once '/home/common/mail.php'; // Add email functionality to program
require_once '/home/common/dbInterface.php'; // Add database functionality
processPageRequest(); // Call the processPageRequest() function

function addMovieToCart($movieID) {	
	$ID = movieExistsInDB($movieID); // returns movieId or 0 (need to add it)
	if ( !$ID ) {
		$movie = file_get_contents("http://www.omdbapi.com/?apikey=4d502d07&i=" . $movieID . "&type=movie&r=json");
		$array = json_decode($movie, true);
		$ID = addMovie($ID, $array['Title'], $array['Year'], $array['Rated'], $array['Runtime'], $array['Genre'], $array['Actors'], $array['Director'], $array['Writer'], $array['Plot'], $array['Poster']);
	}
	addMovieToShoppingCart($_SESSION['userId'], $ID);
	displayCart();
}

function checkout($display_name, $address) {
	$message = "Hello " . $display_name . ",<br />";
	$message .= "<br />";
	$message .= "Here is your myMoviesXpress! receipt:<br />";
	$message .= createMovielist(true); 
	print($message);
	
	$result = sendMail(342376999, $address, $display_name, "Your Receipt from myMovies!", $message); 		
	switch ($result) {
		case 0:
			$_SESSION['msg'] = "<p style='color:green;'>The email message was sent to " . $address . " succesfully!</p>";
			echo $_SESSION['msg'];
			break;
		case ($result > 0 && $result < 60):
			$_SESSION['msg'] = "<p style='color:red;'>" . $result . " seconds remain before the next email can be sent.</p>";
			echo $_SESSION['msg'];
			break;
		case -1:
			$_SESSION['msg'] = "<p style='color:red;'>An error occured while sending the email message (email not sent!)</p>";
			echo $_SESSION['msg'];
			break;
		case -2:
			$_SESSION['msg'] = "<p style='color:red';>An invalid " . $address . "  was provided (email not sent!)</p>";
			echo $_SESSION['msg'];
			break;
		case -3:
			$_SESSION['msg'] = "<p style='color:red';>An error occured while accessing the database (email not sent!)</p>";
			echo $_SESSION['msg'];
			break;
	}
	return $result;	
}

function createMovielist($forEmail=false) {
	if ( !empty($_SESSION['order']) ) {
		$array = getMoviesInCart($_SESSION['userId'], $_SESSION['order']);
	}
	elseif ( empty($_SESSION['order']) ) {
		$array = getMoviesIncart($_SESSION['userId']);
	}
	ob_start(); // Create an output buffer
	require_once './templates/movie_list.html';
	$message = ob_get_contents(); // Get contents of the output buffer
	ob_end_clean(); // Clear the output buffer
	return $message;
}

function displayCart($forEmail=false) {	
	$num_movies = countMoviesInCart($_SESSION['userId']);
	$array = createMovieList($forEmail);
	require_once('./templates/cart_form.html');
	ob_start(); // Create an output buffer
	require_once('./templates/cart_form.html');
	$message = ob_get_contents(); // Get the contents of the output buffer
//	print($message);
	ob_end_clean(); // Clear the output buffer
	return $message;
}

function processPageRequest() {

	if ( !isset($_SESSION['displayName']) ) 
		header('Location: ./logon.php'); // Redirect to logon.php	
	elseif ( !empty($_GET) ) {
		if ( isset($_GET['action']) ) {
			if ( $_GET['action'] == "add" ) { 
				addMovieToCart($_GET['movie_id']);
				echo displayCart(); // The echo is very important
			}
			elseif ( $_GET['action'] == "checkout" ) { 
				$result = checkout($_SESSION['displayName'], $_SESSION['emailAddress']);
				header('Location: index.php'); // redirect to index.php after checkout
			}
			elseif ( $_GET['action'] == "remove" ) {	
				removeMovieFromCart($_GET['movie_id']);
				echo displayCart(); // The echo is very important
			}
			elseif ( $_GET['action'] == "update" ) 
				updateMovieListing($_GET['order']);
		}
	}
	elseif ( empty($_GET) ) {
		if ( isset($_SESSION['msg']) ) {
			echo $_SESSION['msg']; //display error/success msg depending on whether email was sent
			unset($_SESSION['msg']);	
		}
		displayCart();
	}
}

function removeMovieFromCart($movieID) {
	removeMovieFromShoppingCart($_SESSION['userId'], $movieID);
	displayCart();
}

function updateMovieListing($order) {
	$_SESSION['order'] = $order;
	$message = createMovieList(false);
	echo $message; 	
}

?>

