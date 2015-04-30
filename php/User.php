<?php
	/*
	 * Autor: Manuel Treutlein
	 * Klasse: User
	 * Projekt: Schüler_helfen_Schüler
	 */
	
	/*
	 * Für jeden Benutzer wird eine Instanz der Klasse User erzeugt. 
	 * Sie ist die Schnittstelle für jede Handlung des Benutzers und enthält
	 * verschiedene Attribute, die Informationen des Benutzers beinhalten und viele
	 * Methoden, die für die Interaktion mit der Datenbank bestimmt sind. 
	 * In den Methoden für die Datenbank wird die Klasse Sql benutzt
	 */
	
	include_once 'Sql.php';
	
	class User extends Sql
	{
		function __construct($email)
		{
			$this->setId($email);
		}
		
		/* macht aus einem Array einen String */
		public function arrayToString($array)
		{
			return json_encode($array);	
		}
		
		/* macht aus einem String einen Array */
		public function stringToArray($string)
		{
			return json_decode($string, true);		
		}
		
		/* Die Methode prüft, ob das eingegebene Passwort richtig war.*/
		public function checkPassword($email, $password)
		{
			return (strcmp($this->search("Passwort", "`e-mails`", "EMail LIKE '$email'"), $password) == 0); // auf richtiges Passwort prüfen
		}
		
		/* Die Methode liefert eine bestimmte Eigenschaft über den Benutzer durch die Benutzer_id */
		public function valueThroughID($select, $from)
		{
			return $this->search($select, $from, "`h`.`h_id` = " . $this->id()); // liefert die gesuchte Dateneinheit
		}
		
		/* Funktioniert wie die valueThroughID()-Methode, allerdings wird ein Array zurückgeliefert*/
		public function searchColumnThroughID($select, $from)
		{
			return $this->searchColumn($select, $from, $this->id()); // liefert den gesuchten Array
		}
		
		/* Verändert einen Datensatz mit Hilfe der Bentutzer_id*/
		public function updateThroughID($table, $instruction)
		{
			return $this->updateOneEntry($table, $instruction, "h_id = ". $this->id());
		}
		
		/* Trägt einen Datensatz mit Hilfe der Benutzer_id ein */
		public function insertLogfile($successful)
		{
			return $this->insert("`logfiles`", "(`user_id`, `point_in_time`, `successful`)
								  VALUES(" . $this->id() . ", CURRENT_TIMESTAMP, " . $successful . ")");
		}
		
		/* Die Methode liefert ein assoziatives Array mit allen wichtigen Daten des Benutzers zum ausgeben*/
		public function informationsOfUser($email)
		{
			$id = $this->search("h_id", "`haupttabelle` AS `h` LEFT JOIN `e-mails` AS `e` ON `h`.`E-Mail_id` = `e`.`id`", " `e`.`EMail` LIKE '$email'"); // id herraussuchen
			$informations = array(); // jetzt alles in den Array speichern
			$informations["email"] = $this->search("`e`.`EMail`", "`haupttabelle` AS `h` LEFT JOIN `e-mails` AS `e` ON `h`.`E-Mail_id` = `e`.`id`", "h_id = $id");
			$informations["Jahrgangsstufe"] = $this->search("Jahrgangsstufe", "haupttabelle AS `h`", "h_id = $id");
			$informations["Schule"] = $this->search("`s`.`Schulname`", "`haupttabelle` AS `h` LEFT JOIN `schulen` AS `s` ON `h`.`Schule_id` = `s`.`id`", "h_id = $id");
			$informations["sucheFaecher"] = $this->stringToArray($this->search("sucheFaecher_id", "`haupttabelle` as `h`", "h_id = $id"));
			$informations["sucheFaecher_Wochentage"] = $this->stringToArray($this->search("sucheFaecher_Wochentage_id", "`haupttabelle` as `h`", "h_id = $id"));
			$informations["sucheFaecher_Stundenzahl"] =  $this->search("sucheFaecher_Stundenzahl", "haupttabelle AS `h`", "h_id = $id");
			$informations["gebeFaecher"] =  $this->stringToArray($this->search("gebeFaecher_id", "`haupttabelle` as `h`", "h_id = $id"));
			$informations["gebeFaecher_Wochentage"] = $this->stringToArray($this->search("gebeFaecher_Wochentage_id", "`haupttabelle` as `h`", "h_id = $id"));
			$informations["gebeFaecher_Stundenzahl"] = $this->search("gebeFaecher_Stundenzahl", "haupttabelle AS `h`", "h_id = $id");
			$informations["status"] = $this->search("`a`.`Aktivitaet`", "`haupttabelle` as `h` left join `aktivitaet` as `a` on `h`.`aktivitaet_id` = `a`.`id`", "h_id = $id");
			return $informations;
		}
		
		/* Die Methode liefert einen Prozentwert, der angibt, wie gut zwei Nachhilfepartner zueinander passen*/
		public function indexEvaluation($search, $evaluation)
		{
			$index = round(($evaluation / $this->maxEvaluation($search)) * 100, 1);
			if($index < 0) return 0;
			return $index;
		}
		
		/* Die Methode gibt an, welche Bewertung maximal erzielt werden kann */
		private function maxEvaluation($search)
		{
			if($search)
				$user_x = "suche";
			else
				$user_x = "gebe";
			$subjects = $this->stringToArray($this->search($user_x . "Faecher_id", "haupttabelle", "h_id = " . $this->id()));
			$weekdays = $this->stringToArray($this->search($user_x . "Faecher_Wochentage_id", "haupttabelle", "h_id = " . $this->id()));
			return count($subjects) * 4 + count($weekdays) * 2; // Für jedes Fach sind 4 Punkte möglich, für jeden Wochentag 2 Punkte
		}
		
		/* Die Methode startet den Algorithmus zum finden von Nachhilfepartnern, und liefert alle potentiellen Nachhilfepartner in einem Array zurück */
		public function evaluationOfAll($search, $grade, $filterElements)
		{
			$evaluation = array(); // assoziatives Array erzeugen 
			for($i = 1; $i <= $this->countLinesOf("haupttabelle"); $i++) // Alle registrierten Schüler durchlaufen 
				{
					if($this->isAuthorizedForEvaluation($i, $grade, $search)) // prüfen, ob Schüler für den Vergleich(Algorithmus) zugelassen ist
					{
						$specificEvaluation = $this->evaluationOfUser($i, $search); // Punktzahl für den Schüler in Bezug auf den Benutzer
						if($this->isNotFiltered($filterElements, $i, $this->indexEvaluation($search, $specificEvaluation)))
						{
							$email = $this->search("`e`.`EMail`", "`haupttabelle` AS `h` LEFT JOIN `e-mails` AS `e` ON `h`.`E-Mail_id` = `e`.`id`", "h_id = $i"); // E-Mail ermitteln
							$evaluation["$email"] = $specificEvaluation; // mit E-Mail wird die Punktezahl in ein assoziatives Array abgespeichert
						}
					}	
				}
			return $evaluation;
		}
		
		/* Die Methode filtert die Schüler auf Basis der Eingaben des Benutzers */
		private function isNotFiltered($filterElements, $compareID, $index)
		{
			if(is_numeric(($filterElements["from_grade"])) && $filterElements["from_grade"] > $this->search("Jahrgangsstufe", "haupttabelle AS `h`", "h_id = $compareID"))	
				return false;	
			elseif(is_numeric(($filterElements["to_grade"])) && $this->search("Jahrgangsstufe", "haupttabelle AS `h`", "h_id = $compareID") > $filterElements["to_grade"])
				return false;
			elseif(is_numeric(($filterElements["index_bigger"])) && $filterElements["index_bigger"] > $index)
				return false;
			else
				return true;
		}
		
		/* Die Methode bewertet einen Schüler, je nachdem, wie er zu dem anderen Schüler passt */
		private function evaluationOfUser($compareID, $search)
		{
			$evaluation = 0;
			$userElements = $this->loadCompareElements(!($search), $this->id()); // Elemente des Nutzers laden 
			$compareElements = $this->loadCompareElements($search, $compareID); // Die Vergleichselement laden 
			$evaluation += $this->compareSubjects($userElements[0], $compareElements[0]); // Fächer berücksichtigen
			$evaluation += $this->compareWeekdays($userElements[1], $compareElements[1]); // Wochentage berücksichtigen 
			$differenz = $userElements[2] - $compareElements[2]; // Differenz der Stunden_pro_Woche berücksichtigen 
			if($differenz < 0) $differenz *= (-1); // Differenz immer positiv
			$evaluation -= $differenz; // Differenz von Gesamtpunktzahl abziehen
			return $evaluation;
		}
		
		/* Die Methode läd für die Methode evaluationOfUser() die richtigen Fächer, Wochentage und Stunden_pro_Woche für eine beliebige ID und liefert ein numerisches Array; */
		private function loadCompareElements($search, $compareID)
		{
			$compareElements = array();
			$compareElements[0] = array();
			$compareElements[1] = array();
			if($search) // wenn Benutzer Nachhilfe sucht
				$pupil_x = "gebe";
			else // geben will
				$pupil_x = "suche";
			
			$compareElements[0] = $this->stringToArray($this->search($pupil_x . "Faecher_id", "`haupttabelle` AS `h`", "h_id = $compareID"));
			$compareElements[1] = $this->stringToArray($this->search($pupil_x . "Faecher_Wochentage_id", "`haupttabelle` AS `h`", "h_id = $compareID"));
			$compareElements[2] = $this->search($pupil_x . "Faecher_Stundenzahl ", "haupttabelle AS `h` ", "h_id = $compareID");
			return $compareElements;
		}
		
		/* Die Methode vergleicht von alle Fächer von zwei Benutzern miteinander und errechnet daraus die Punktzahl für den Algorithmus */
		private function compareSubjects($subjects1, $subjects2)
		{
			$evaluation = 0; 
			for($i = 0; $i < count($subjects1); $i++) // Fächer des ersten Arrays
			{
				for($j = 0; $j < count($subjects2); $j++) // Fächer des zweiten Arrays
				{	
					if($subjects1[$i] == $subjects2[$j]) // wenn diese gleich sind
					{
						if(strcmp("---", $this->search("Fach", "faecher", "id = $subjects1[$i]")) != 0) // Fächer sollen aber nicht "---" sein, da das kein Fach ist
								$evaluation += 4; // Vier Punkte
					}
				}
			}	
			return $evaluation;
		}
		
		/* Die Methode vergleicht wie compareSubjects($, $) die Wochentage für den Algorithmus und berechnet die Punktzahl*/
		private function compareWeekdays($weekdays1, $weekdays2)
		{
			$evaluation = 0;
			for($i = 0; $i < count($weekdays1); $i++) // Wochentage des ersten Arrays
			{
				for($j = 0; $j < count($weekdays2); $j++) // Wochentage des zweiten Arrays
				{
					if($weekdays1[$i] == $weekdays2[$j]) // wenn diese gleich sind
					{
						if(strcmp("---", $this->search("Wochentag", "wochentage", "id = $weekdays1[$i]")) != 0) // "---" auslasen
							$evaluation += 2; // zwei Punkte
					}
				}
			}
			return $evaluation;	
		}
		
		/* Die Methode prüft, ob ein beliebiger Schüler für den Suchalgorithmus zugelassen wird */
		private function isAuthorizedForEvaluation($compareID, $grade, $search)
		{
			if($this->userAktiv($compareID)) // Schüler ist aktiv
			{
				if($compareID != $this->id()) // Schüler darf nicht der Benutzer sein
				{
					if($this->hasAtTheMinimumOneSubjectInCommon($compareID, $search))
					{	
						if($search) // wenn der Benutzer Nachhilfe sucht
						{
							if($this->search("Jahrgangsstufe", "haupttabelle AS `h`", "h_id = $compareID") >= $grade) // Schüler sollte in einer höheren oder in der gleichen Jahrgangsstufe sein wie der Benutzer
								return true; // zum Algorithmus zulassen
						}		
						else // wenn der Benutzer Nachhilfe anbieten will
						{
							if($this->search("Jahrgangsstufe", "haupttabelle AS `h`", "h_id = $compareID") <= $grade) // Schüler sollte in einer niedrigeren oder gleichen Jahrgangsstufe sein wie der Benutzer
								return true; // zum Algorithmus zulassen
						}
					}
				}
			}
			return false; // Hat einen der Tests nicht bestanden!
		}
		
		/* Die Methode überprüft, ob Benutzer und Schüler mindestens ein gleiches Fach haben */
		private function hasAtTheMinimumOneSubjectInCommon($compareID, $search)
		{
			if($search)
			{
				$user_x = "suche";
				$pupil_x = "gebe";
			}
			else
			{
				$user_x = "gebe";
				$pupil_x = "suche";
			}
			$compareSubjects = $this->stringToArray($this->search($pupil_x . "Faecher_id", "`haupttabelle` AS `h`", "h_id = " . $compareID));
			$userSubjects = $this->stringToArray($this->valueThroughID($user_x . "Faecher_id", "`haupttabelle` AS `h`"));
			if($this->compareSubjects($compareSubjects, $userSubjects) > 0 )
				return true;
			else
				return false;
		}
		
		/* Die Methode liefert true oder false, je nachdem, ob der Benutzer seinen Status auf aktiv geschaltet hat, oder nicht */
		private function userAktiv($id)
		{
			return (strcmp("aktiv", $this->search("`a`.`Aktivitaet`", "`haupttabelle` AS `h` left join `aktivitaet` AS `a` on `h`.`aktivitaet_id` = `a`.`id`", "h_id = $id")) == 0);
		}
		
		/* Die Methode gibt an, ob diese Objektinstanz wirklich auch ein Benutzer in der Datenbank ist */
		public function isUser()
		{
			if(is_numeric($this->id()))
				return true;
			else
				return false;
		}
		
		/* Die Methode gibt anhand der Logfiles an, ob der Benutzer ein Opfer eines Brutforce-Angriffes sein könnte */
		public function isVictimOfBruteforce()
		{
			$logins = $this->searchColumn("successful", "`logfiles`", "user_id = " . $this->id() . " ORDER BY point_in_time DESC LIMIT 32"); // Arrays mit allen Logins des Benutzers 		
			for($i = 1, $indexBrutforce = 0; $i < count($logins); $i++) // $i = 1, um den ersten Login zu überspringen, der ja immer true ist, da der Nutzer eingeloggt ist 
			{
				if($logins[$i] == 0) // falscher Login
				{
					$indexBrutforce++;
					if($indexBrutforce > 30) // hier kann die Sensibilität eingestellt werden 
						return true;
				}
				else // ein erfolgreicher Login
				{
					return false;
				}
			}
		}
	}
?>