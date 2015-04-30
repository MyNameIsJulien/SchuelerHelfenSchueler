<?php
	/*
 	*  Manuel Treutlein
 	*  
	* Koordiniert einen Logout des Benutzers 
	*
	*/
	include_once 'User.php';
	session_start();
	$user = $_SESSION['user'];
	
	unset($user);
	$user = null;
	session_destroy();
	header('location: index.php');
?>