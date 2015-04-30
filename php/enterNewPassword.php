 <!-- 

 	Manuel Treutlein 

 	Auf dieser Seite wird das neue Passwort bearbeitet und in die Datenbank eingetragen
 
  -->
<?php
	include_once 'User.php';
	include_once 'Security.php';
	session_start();
	$user = $_SESSION['user'];
	$sec = new Security();
	
	// Passwörter aus Datenbank escapen und verschlüsseln
	$old_password 		= $sec->encode($sec->safeEscape($_POST["old_password"]));
	$new_password		= $sec->safeEscape($_POST["new_password"]);
	$prove_new_password = $sec->safeEscape($_POST["prove_new_password"]);
	
	
	$email = $user->valueThroughID("`e`.`EMail`", "`haupttabelle` AS `h` LEFT JOIN `e-mails` AS `e` ON `h`.`E-Mail_id` = `e`.`id`");
	if($user->checkPassword($email, $old_password))
	{
		if(strcmp($new_password, $prove_new_password) == 0)	
		{
			if($sec->isSafePassword($new_password))
			{	
				$new_password = $sec->encode($new_password); // verschlüsseln, bevor es in die Datenbank kommt
				$id = $user->valueThroughID("`E-Mail_id`", "`haupttabelle` AS `h`");
				$successful = $user->updateOneEntry("`e-mails`", "Passwort = '$new_password'", "id = $id");
			}
		}
	}
	// Wieder zurück zur Datenverwaltung, wenn alle Änderungen abgearbeitet wurden
	header('location: dataAdministration.php');
	
?>