PHP Basic User
===========

A simple user class that uses the $_SESSION and mysql database for storage and verification

Features
--------

* Simple
* Plug-n-play

How to Use
----------

	
	// USER AUTH the constant check
	if($_SESSION['user_password']){
		$user = new User($_SESSION['user_email'],$_SESSION['user_password']);
	
	// to login
	// check if password is posted, but not password_confirm
	} else if ($_POST['password'] && !$_POST['password_confirm']){
		$user = new User($_POST['email'],$_POST['password']);
	
	// the check to register
	} elseif ($_POST['password_confirm']) {
	
		// this will auto create the user with because it looks for post
		$user = new User();
	
	
	} 
	