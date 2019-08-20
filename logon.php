<?php
 
require_once '/home/common/mail.php'; // Add email functionality
require_once '/home/common/dbInterface.php'; // Add database functionality
processPageRequest();

function authenticateUser($username, $password) {
	$array = validateUser($username, $password);
	if ( is_array($array) ) {
		session_start();
		$_SESSION['userId'] = $array[0];
		$_SESSION['displayName'] = $array[1];
		$_SESSION['emailAddress'] = $array[2];
		header('Location: ./index.php'); // Redirect to index.php
		return;
	} else {
		$message = "<p style='color:red;'>Error: Inputted login credentials do not exist!</p>";
		displayLoginForm($message);
		return;
	}
}
 
function createAccount($username, $password, $displayName, $emailAddress) {
	echo "inside createAccount()";
	$userId = addUser($username, $password, $displayName, $emailAddress);
	if ( $userId !== 0 ) {
		echo $username . " " . " " . $password . " " . $displayName . " " . $emailAddress;
		sendValidationEmail($userId, $displayName, $emailAddress);
		echo "<p style='color:green;'>A validation email has been sent to your email address!</p>";
	}
	elseif ( $userId === 0 ) {
		$message = "<p style='color:red;'>Error: The provided username already exists!</p>";
		displayLoginForm($message);
	}
}
 
function displayCreateAccountForm() {
	require_once('templates/create_form.html');
}
 
function displayForgotPasswordForm() {
	require_once('templates/forgot_form.html');
}
 
function displayLoginForm($message="") {	
	echo $message;
	require_once('templates/logon_form.html');
}

function displayResetPasswordForm($userId) {
	require_once('templates/reset_form.html');
}

function processPageRequest() {
	session_destroy();	
	if ( !empty($_POST) ) {	
		if ( isset($_POST['action']) ) {
			if ( $_POST['action'] == 'create' ) 
				createAccount($_POST['username'], $_POST['password'], $_POST['displayName'], $_POST['emailAddress']);
			elseif ( $_POST['action'] == 'forgot' ) {
				sendForgotPasswordEmail($_POST['username']);
				echo "<p style='color:red;'>A password reset email has been sent to your email address!</p>";
			}
			elseif ( $_POST['action'] == 'login' ) 
				authenticateUser($_POST['username'], $_POST['password']);
			elseif ( $_POST['action'] == 'reset' ) 
				resetPassword($_POST['userId'], $_POST['password']);
		}
	}
	
	elseif ( !empty($_GET) ) {
			if ( isset($_GET['action']) )
				validateAccount($_POST['username']);	
			elseif ( isset($_GET['form']) ) {
				if ( $_GET['form'] == 'create' ) {
					displayCreateAccountForm();
					return;
				}
				elseif ( $_GET['form'] == 'forgot' ) {
					displayForgotPasswordForm();
					return;
				}
				elseif ( $_GET['form'] == 'reset' ) {
					displayResetPasswordForm($_POST['userId']);
					return;
				}
			}
	}	
	if ( empty($_POST) ||  empty($_GET) )
		displayLoginForm();
} 
 
function resetPassword($userId, $password) {

	if (resetUserPassword($userId, $password) ) {
		$message = "The user's password was succesfully updated!";
		displayLoginForm($message);
	} else {
		$message = "Error: Either the provided user ID does not exist, or the provided 'new' password is the same as the 'current' password.";
		displayLoginForm($message);
	}
	
}
 
function sendForgotPasswordEmail($username) {
	
	$user_data =  getUserData($username);
       	var_dump($user_data);	
	$message = "";
	$message .= "<h1>myMovies Xpress!</h1>";
	$message .= "Hello " . $user_data[1] . ",";
	$message .= "\n\n";
	$message .= "Upon clicking the hyperlink, enter your account email address and select 'Reset password'.\n";
	$message .= "We will send you an email with a link to create a new password. Check your spam folder in case.";	
	$message .= "\n";
	$message .= "<a href='http://139.62.210.181/~sa475897/project5/logon.php?form=reset&user_id=" . $username . "'>Reset Password</a>";
	$subject = "myMovies! Password Reset Request";
	sendMail(342376999, $user_data[2], $user_data[1], $subject, $message);
}
 
function sendValidationEmail($userId, $displayName, $emailAddress) {

	$message = "";
	$message .= "<h1>myMovies Xpress!</h1>";
	$message .= "Hello " . $displayName . ",";
	$message .= "\n\n";
	$message .= "validate your account with the link below! ";
	$message .= "<a href=http://139.62.210.181/~sa475897/project5/logon.php?action=validate&user_id=" . $username . "'>Validate Email</a>";
	$subject = "myMovies! Account Validation";	
	sendMail(342376999, $emailAddress, $displayName, $subject, $message);
}

function validateAccount($userId) {
	
	if ( activateAccount($userId) ) { 
		$message = "<p style='color:green;'>Success: Your account has been activated!</p>";
		displayLoginForm($message);
	}
	elseif ( !activateAccount($userId) ) {
		$message = "<p style='color:red;'>Error: Your account, " . $userId . " is not activated. Go to [Create Account] to activate it!!</p>";
		displayLoginForm($message);
	}
}

?>
