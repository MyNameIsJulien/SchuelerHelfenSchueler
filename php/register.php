<?php
	/*
 	*  Manuel Treutlein
	*
	* Die Seite Bearbeitet eine neue Registrierung und trägt je nachdem, den neuen Benutzer ein, oder weißt die Person darauf hin, dass bei der Registrierung etwas schiefgelaufen ist
	*/

	// Hier sollte Ajax verwendet werden, damit die Person, die sich registrieren will, merkt was sie falsch gemacht hat 
	
	include_once "User.php";
	include_once 'Security.php';
	$sec = new Security();
	
	$email = $sec->safeEscape($_POST["e-mail"]);
	$grade = $sec->safeEscape($_POST["grade"]);
	$password = $sec->safeEscape($_POST["password"]);
	$provePassword = $sec->safeEscape($_POST["provePassword"]);
	
	if($email == "" || $password == "" || $provePassword == "") // wenn etwas nicht aufgefüllt wurde
		header('location: index.php');
	
	if(!($sec->isEmail($email))) // Eingegebene E-Mail ist nicht E-Mail-ähnlich
		header('location: index.php'); 
	elseif(strcmp($password, $provePassword) != 0) // Passworter stimmen nicht überein 
		header('location: index.php');
	elseif(!($sec->isSafePassword($password))) // das vom Benutzer gewählte Passwort ist nicht sicher
		header('location: index.php');
	elseif(strcmp($_POST["textCaptcha"], $sec->encode($sec->safeEscape($_POST["entryCaptcha"]))) != 0) // Captcha wurde nicht richtig eingegeben 
		header('location: index.php');
	else 
	{	
		$user = new User($email);
		if($user->isUser()) // prüfen, ob die E-Mail schon von einem anderen Benutzer belegt ist 
		{
			unset($user);
			$user = null;
			session_destroy();
			header('location: index.php');
		}
		elseif($user->insert("`haupttabelle`", "(`h_id`, `E-Mail_id`, `Jahrgangsstufe`, `Schule_id`, `sucheFaecher_id`, `sucheFaecher_Wochentage_id`, `sucheFaecher_Stundenzahl`, `gebeFaecher_id`, `gebeFaecher_Wochentage_id`, `gebeFaecher_Stundenzahl`, `aktivitaet_id`)
					   VALUES           (  null,           0,           $grade,           1,        '[\"15\"]',                    '[\"8\"]', 						   0,       '[\"15\"]', 				 '[\"8\"]', 						 0, 			  1)
			"))
		{
			$password = $sec->encode($password);
			if($user->insert("`e-mails`", "(`id`, `EMail`, `Passwort`) VALUES (null, '$email', '$password')"))
			{
				$user->updateOneEntry("`haupttabelle`", "`E-Mail_id` = `h_id`","1"); // Notlösung, E-Mail_id eigentlich überflüssig
				session_start();
				$user->setId($email); // id setzen
				$user->insertLogfile(1); // erstes Logfile speichern
				$_SESSION['user'] = $user;
				header('location: main.php'); // Weiterleitung zur Hauptseite, hier sollte der neue Benutzer eine Begrüßung erhalten, und ggf. Instruktionen 
			}	
		}
		else
		{
			echo "Registrierung fehlgeschlagen <br />";
			echo "<a href='index.php'>zur Startseite</a>";
		}
	}
	
?>