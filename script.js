
function addMovie(movieID) {
	window.location.replace("./index.php?action=add&movie_id=" + movieID);
	return true;
}

function confirmCancel(form) {
	if (form === document.getElementsByName("Cancel") ) 
		return false;
	else if ( form === ( document.getElementById("create") || document.getElementById("forgot") || document.getElementById("reset") || document.getElementById("search") ) ) 
		window.location.replace("./logon.php");
	else if ( form === document.getElementById("search") )
		window.location.replace("./index.php");
	return true;
}

function changeMovieDisplay() {
	var e = document.getElementById("select_order");
	var selected_value = e.options[e.selectedIndex].value;
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		document.getElementById("shopping_cart").innerHTML = this.responseText;	
	}
	xhttp.open("GET", "./index.php?action=update&order=" + selected_value, true);
	xhttp.send();
}

function confirmCheckout() {
	if(confirm("Are you sure you want to checkout from myMovies Xpress?")) {
		window.location.replace("./index.php?action=checkout");	
		return true;
	}
	else
		return false;
}

function confirmLogout() {
	if (confirm("Do you wish to logout of myMovies Xpress?")) {
		window.location.replace("./logon.php?action=logoff");
		return true;
	}
	else
		return false;
}

function confirmRemove(title, movieID) {
	if (confirm(`Do you wish to remove ${title}?`)) {
		window.location.replace("./index.php?action=remove&movie_id=" + movieID);
		return true;	
	}
	else
		return false;
}

function displayMovieInformation(movie_id) {
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		document.getElementById("modalWindowContent").innerHTML = this.responseText;
		showModalWindow();
	}
	xhttp.open("GET", "./movieinfo.php?movie_id=" + movie_id, true);
	xhttp.send();
}

function forgotPassword() {
	window.location.replace("./logon.php?action=forgot");
	return true;
}

function showModalWindow() {
	var modal = document.getElementById('modalWindow');
	var span = document.getElementsByClassName("close")[0];

	span.onclick = function() {
		modal.style.display = "none";
	}

	window.onclick = function(event) {
		if (event.target == modal) {
			modal.style.display = "none";
		}
	}
	modal.style.display = "block";
}

function validateCreateAccountForm() {
	var email = document.getElementById("emailAddress").value;
	var email2 = document.getElementById("emailAddress2").value;
	var username = document.getElementById("username").value;
	var password = document.getElementById("password").value;
	var password2 = document.getElementById("password2").value;
	if (((email.indexOf(' ') >= 0) && (email2.indexOf(' ') >= 0)) && (username.indexOf(' ') >= 0) && ((password.indexOf(' ') >= 0) && (password2.indexOf(' ') >= 0))) {
		alert("Error: None of the form fields may contain one or more spaces!");
		return false;
	}
	if (email !== email2) {
		alert("Error: Emails do not match!");
		return false;
	}
	if (password !== password2) {
		alert("Error: Passwords do not match!");
		return false;
	}
	return true;
}

function validateResetPasswordForm() {
	var password = document.getElementById("password").value;
        var password2 = document.getElementById("password2").value;
	if (password.indexOf(' ') >= 0 || password2.indexOf(' ') >= 0) {
		alert("Error: Passwords may not contain spaces!");
		return false;
	}
	if (password !== password2) {
		alert("Error: Passwords must match!");
		return false;
	}
}
