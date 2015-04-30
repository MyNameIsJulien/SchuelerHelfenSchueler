 <!-- 

 	Manuel Treutlein 

 	Auf diese Seite wird nach der Datenverwaltung weitergeleitet. Neue Daten werden verwaltet. 
 	Mit Ajax könnte man das ändern von Daten auf der Seite dataAdministration.php sichtbar machen. 
 
  -->
<?php
	include_once 'User.php';
	include_once 'Security.php';
	session_start();
	$user = $_SESSION['user'];
	$sec = new Security();
	
	$new_email 						= $sec->safeEscape($_POST["new_email"]);
	$new_grade 						= $_POST["new_grade"];
//	$new_school 					= $_POST[""];
	
	$new_search 					= $_POST["new_search"];
	$new_searchSubject_weekday 		= $_POST["sucheFaecher_new_weekday"];
	$new_searchSubject_hoursPerWeek = $_POST["sucheFaecher_new_hoursPerWeek"];
	
	$new_offer 						= $_POST["new_offer"];
	$new_giveSubject_weekday 		= $_POST["gebeFaecher_new_weekday"];
	$new_giveSubject_hoursPerWeek 	= $_POST["gebeFaecher_new_hoursPerWeek"];
	
	$new_activity 					= $_POST["new_activity"];
		
	
	// E-Mail updaten
	if($new_email)
	{
		$old_email = $user->valueThroughID("`e`.`EMail`", "`haupttabelle` AS `h` LEFT JOIN `e-mails` AS `e` ON `h`.`E-Mail_id` = `e`.`id`"); // alte E-Mail, für den $where-Teil
		$successful = $user->updateOneEntry("`e-mails`", "EMail = '$new_email'", "EMail = '$old_email'");	
	}
	
	
	// Neue Jahrgangsstufe 
	if($new_grade)
	{
		$successful = $user->updateThroughID("`haupttabelle`", "Jahrgangsstufe = $new_grade");
	}
	
	
	
	
	
	
	
	
	
	// Der Benutzer sucht Nachhilfe
	$array_search = array(); // in dem Array werden die ID`s der Fächer eingetragen, die der Benutzer sucht
	// neues Fach, dass der Schüler sucht
	if(!(in_array(null, $new_search)) && $new_search) // null muss geprüft werden, da dies bei einem Nichtauswählen im Array enthalten wäre, und der Array somit "true"
	{
		for($i = 0; $i < count($new_search); $i++) // alle ausgewählten Optionen des mehrfachen Auswahlmenüs durchgehen
		{
			if(isset($new_search[$i])) // wenn dieses Fach ausgewählt wurde:
			{
				array_push($array_search, $user->search("id", "faecher", "Fach LIKE '$new_search[$i]'")); // Id des Faches in den Array aufnehmen
			}
		}
		$search = $user->arrayToString($array_search); // in String konvertieren
		$successful = $user->updateThroughID("`haupttabelle`", "sucheFaecher_id = '$search'"); // in der Datenbank abspeichern
	}
	
	
	$array_searchSubject_weekday = array();
	// neuer Wochentag
	if(!(in_array(null, $new_searchSubject_weekday)) && $new_searchSubject_weekday)
	{
		for($i = 0; $i < count($new_searchSubject_weekday); $i++) // alle ausgewählten Optionen des mehrfachen Auswahlmenüs durchgehen
		{
			if(isset($new_searchSubject_weekday[$i])) // wenn dieses Fach ausgewählt wurde:
			{
				array_push($array_searchSubject_weekday, $user->search("id", "wochentage", "Wochentag LIKE '$new_searchSubject_weekday[$i]'")); // Id des Wochentags in den Array aufnehmen
			}
		}
		$weekdays = $user->arrayToString($array_searchSubject_weekday); // in String konvertieren
		$successful = $user->updateThroughID("`haupttabelle`", "sucheFaecher_Wochentage_id = '$weekdays'"); // in der Datenbank abspeichern
	}
	
	
	if($new_searchSubject_hoursPerWeek)
	{
		$successful = $user->updateThroughID("`haupttabelle`", "sucheFaecher_Stundenzahl = $new_searchSubject_hoursPerWeek");
	}
		
	
	
	
	
	
	
	
	
	// Der Benutzer bietet Nachhilfe an
	$array_offer = array();
	// neues Fach, dass der Schüler geben will
	if(!(in_array(null, $new_offer)) && $new_offer)
	{
		for($i = 0; $i < count($new_offer); $i++) // alle ausgewählten Optionen des mehrfachen Auswahlmenüs durchgehen
		{
			if(isset($new_offer[$i])) // wenn dieses Fach ausgewählt wurde:
			{
				array_push($array_offer, $user->search("id", "faecher", "Fach LIKE '$new_offer[$i]'")); // Id des Faches in den Array aufnehmen
			}
		}
		$offer = $user->arrayToString($array_offer); // in String konvertieren
		$successful = $user->updateThroughID("`haupttabelle`", "gebeFaecher_id = '$offer'"); // in der Datenbank abspeichern
	}
	
	
	$array_weekday = array();
	// neuer Wochentag
	if(!(in_array(null, $new_giveSubject_weekday)) && $new_giveSubject_weekday)
	{
		for($i = 0; $i < count($new_giveSubject_weekday); $i++) // alle ausgewählten Optionen des mehrfachen Auswahlmenüs durchgehen
		{
			if(isset($new_giveSubject_weekday[$i])) // wenn dieses Fach ausgewählt wurde:
			{
				array_push($array_weekday, $user->search("id", "wochentage", "Wochentag LIKE '$new_giveSubject_weekday[$i]'")); // Id des Wochentags in den Array aufnehmen
			}
		}
		$weekdays = $user->arrayToString($array_weekday); // in String konvertieren
		$successful = $user->updateThroughID("`haupttabelle`", "gebeFaecher_Wochentage_id = '$weekdays'"); // in der Datenbank abspeichern
	}
	
	
	// neue Stundenanzahl
	if($new_giveSubject_hoursPerWeek)
	{
		$successful = $user->updateThroughID("`haupttabelle`", "gebeFaecher_Stundenzahl = $new_giveSubject_hoursPerWeek");	
	}
	
	
	
	
	
	
	
	
	
	// Aktivität/Status verändern, wird in Datenbank als Aktivität gespeichert, da 'status' reserviertes Wort
	if($new_activity)
	{
		$aktivitaetId = $user->search("id", "`aktivitaet`", "Aktivitaet = '$new_activity'");
		$successful = $user->updateThroughID("`haupttabelle`", "aktivitaet_id = $aktivitaetId");
	}
	
	
	// Wieder zurück zur Datenverwaltung, wenn alle Änderungen abgearbeitet wurden 
	header('location: dataAdministration.php');
	
?>