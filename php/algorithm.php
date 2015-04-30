<?php
	/*
 	*  Manuel Treutlein
	*
	*  Die Seite beinhaltet den Algorithmus, um die passenden Nachhilfepartner zu finden. Die Schule wird nicht berücksichtigt. 
	*  Seite sollte bei der Designbearbeitung in die Hauptseite mit eingebunden werden 
	*/
	// Seite wurde schon einmal wegen Übersichtlichkeit von Quellcode überarbeitet, ist jetzt ok, aber nicht perfekt 

	include_once 'User.php';
	session_start();
	$user = $_SESSION['user'];
	
	echo "<h1>Geeignete Nachhilfepartner:</h1>";
	echo "<a href='main.php'>Zur Hauptseite</a><br />";
	echo "<br />";
	echo "<p>Je <b>höher</b> ein Schüler bewertet ist, desto <b>besser</b> passt er zu dir!</p>";
	
	if($_POST["searchOrGive"] == "search") // Variablen definieren, je nachdem, ob der Benutzer Nachhilfe sucht oder geben will
	{
		$user_x = "suche";
		$pupil_x = "gebe";
		$search = true;
		echo "<p>Du <b>suchst</b> Nachhilfe auf Basis dieser Daten: </p>";
	}
	else
	{
		$user_x = "gebe";
		$pupil_x = "suche";
		$search = false;
		echo "<p>Du <b>bietest</b> Nachhilfe auf Basis dieser Daten an: </p>";
	}
	$filterElements = array();
	$filterElements["from_grade"] = $_POST["from_grade"];
	$filterElements["to_grade"] = $_POST["to_grade"];
	$filterElements["index_bigger"] = $_POST["index_bigger"];
	
	
	// Informationen des Users ausgeben, damit er sich daran orientieren kann
	echo "<a href='dataAdministration.php'>Daten ändern</a>";
	echo "<table border='1'>";
	echo "<tr><th>Fächer</th><th>Wochentage</th><th>Stunden pro Woche</th></tr>";
	
	echo "<tr>";
	echo "<td>"; // Fächer ausgeben
	$informationsUser = $user->informationsOfUser($user->valueThroughID("`e`.`EMail`", "`haupttabelle` AS `h` LEFT JOIN `e-mails` AS `e` ON `h`.`E-Mail_id` = `e`.`id`"));
	$searchSubjects = $informationsUser[$user_x . "Faecher"];
	for($i = 0; $i < count($searchSubjects); $i++)
	{
		echo $user->search("Fach", "faecher", "id = $searchSubjects[$i]");
		if($i != count($searchSubjects) - 1) echo ", "; // kein Komma beim letzten
	}
	echo "</td>";
	echo "<td>"; // Wochentage ausgeben
	$searchWeekdays = $informationsUser[$user_x . "Faecher_Wochentage"];
	for($i = 0; $i < count($searchWeekdays); $i++)
	{
		echo $user->search("Wochentag", "wochentage", "id = $searchWeekdays[$i]");
		if($i != count($searchWeekdays) - 1) echo ", "; // kein Komma am Ende
	}
	echo "</td>";
	echo "<td>" . $informationsUser[$user_x . "Faecher_Stundenzahl"] . "</td>";
	echo "</tr>";
	echo "</table>";
	echo "<br />";
	echo "<hr color='red'>";
	
	// Informationen der potentiellen Nachhilfepartner
	$grade = $user->valueThroughID("Jahrgangsstufe", "haupttabelle AS `h`");
	$potentialPupils = $user->evaluationOfAll($search, $grade, $filterElements);
	arsort($potentialPupils); // Aufsteigende Auflistung
	
	if(count($potentialPupils) < 1) echo "Leider wurden keine geeigneten Partner gefunden. <a href='dataAdministration.php'>Daten ändern</a>";
	else
	{
		echo "<b>" . count($potentialPupils) . "</b> passende Nachhilfeschüler gefunden: ";
		echo "<table border='1'>";
		echo "<tr><th>Kontakt</th><th>E-Mail</th><th>Jahrgangsstufe</th><th>Fächer</th><th>Wochentage</th><th>Stunden pro Woche</th><th>Index</th></tr>";
		foreach($potentialPupils as $email=>$evaluation)
		{
			$informations = $user->informationsOfUser($email);
			echo "<tr>";
				echo "<td>";
					echo "<form action='contactForm.php' method='post'>";
						echo "<input type='submit' value='Anschreiben'/>"; // zum Anschreiben
						echo "<input type='hidden' name='email' value='" . $informations["email"] . "'/>"; // versteckt für den Nutzer die E-Mail mitschicken
					echo "</form>";
				echo "</td>";
				echo "<td>" . $informations["email"] . "</td>";
				echo "<td>" . $informations["Jahrgangsstufe"] . "</td>";
				//echo "<td>" . $informations["Schule"] . "</td>"; // Schulen werden noch ignoriert
				echo "<td>"; // Fächer ausgeben
				$searchSubjects = $informations[$pupil_x . "Faecher"];
				for($i = 0; $i < count($searchSubjects); $i++)
				{
					echo $user->search("Fach", "faecher", "id = $searchSubjects[$i]");
					if($i != count($searchSubjects) - 1) echo ", "; // kein Komma am Ende
				}
				echo "</td>";
				echo "<td>";
				$searchWeekdays = $informations[$pupil_x . "Faecher_Wochentage"];
				for($i = 0; $i < count($searchWeekdays); $i++)
				{
					echo $user->search("Wochentag", "wochentage", "id = $searchWeekdays[$i]");
					if($i != count($searchWeekdays) - 1) echo ", "; // kein Komma am Ende
				}
				echo "</td>";
				echo "<td>" . $informations[$pupil_x . "Faecher_Stundenzahl"] . "</td>";
				echo "<td><b>" . $user->indexEvaluation($search, $evaluation) . "%</b></td>";
			echo "</tr>";
		}
		echo "</table>";
	}
?>