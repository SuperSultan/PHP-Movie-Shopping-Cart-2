<?php 

error_reporting(-1);
ini_set('display_errors', 'On');

session_start(); // Connect to the existing session
processPageRequest(); // Call the processPageRequest() function

function displaySearchForm() {
	require_once('templates/search_form.html');
}

function displaySearchResults($searchString) {
	$results = file_get_contents('http://www.omdbapi.com/?apikey=4d502d07&s='.urlencode($searchString).'&type=movie&r=json');
	$array = json_decode($results, true)["Search"];
	require_once('templates/results_form.html');
}

function processPageRequest() { 
	if ( !isset($_SESSION['displayName']) )
		header('Location: ./logon.php');
	elseif ( empty($_POST) ) 
		displaySearchForm();
	elseif ( !empty($_POST) )
		displaySearchResults($_POST['searchString']);
}

?>
