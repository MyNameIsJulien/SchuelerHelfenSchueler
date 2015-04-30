<?php
	/*
	*  Manuel Treutlein
	*
	*  Die Seite schickt die E-Mail ab 
	*/
	
	include_once 'Security.php';
	$sec = new Security();
	
	$adress = $sec->safeEscape($_POST["adress"]);
	$subject = $sec->safeEscape($_POST["subject"]);
	$text = $sec->safeEscape($_POST["text"]);
	
	if(mail($adress,$subject, $text))
		echo "E-Mail versandt!";
	else
		echo "Es ist ein Fehler aufgetreten.";
		
?>