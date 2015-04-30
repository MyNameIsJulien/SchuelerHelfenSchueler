<?php	
	/* 
	 *  Manuel Treutlein
	 *  
	 *  Leitet bei richtiger Passworteingabe den Nutzer an die Hauptseite weiter, bei falschem Passwort, zurück zur Startseite
	 *  
	 */
	
	include_once 'User.php';
	include_once 'Security.php';
	$sec = new Security();
	
	if($sec->safeEscape($_POST["e-mail"]) == "" || $sec->safeEscape($_POST["password"]) == "") // Felder müssen ausgefüllt sein 
	{
		header('location: index.php');
	}
	else // Passwort prüfen 
	{
		$email = $sec->safeEscape($_POST["e-mail"]);
		$password = $sec->safeEscape($_POST["password"]);

		$user = new User($email); // den User als Schnittstelle erstellen
		if($user->checkPassword($email, $sec->encode($password))) // Abfragen
		{
			session_start();
			$_SESSION['user'] = $user;
			$user->insertLogfile(1); // Login speichern
			header('location: main.php'); // Passwort richtig: Hauptseite
		}
		else
		{
			$user->insertLogfile(0); // Login speichern
			unset($user);
			$user = null;
			session_destroy();
			usleep(500000); // halbe Sekunde warten: macht Brutforce schwieriger
			header('location: index.php'); // Passwort falsch: Startseite
		}
	}
?>