<?php
	/*
	 * Autor: Manuel Treutlein
	 * Klasse: Security
	 * Projekt: Sch�ler_helfen_Sch�ler
	 */
	
	/*
	 * Die Klasse beinhaltet alle sicherheitsrelevanten Schutzmechanismen
	 */

	/*
	 * Die Klasse bietet (theoretisch) Schutz gegen: 
	 * 		- SQl-Injections : safeEscape()
	 * 		- Cross-Site-Scripting: safeEscape()
	 * 		- falsche E-Mails : isEmail()
	 * 		- schlecht gew�hlte Passw�rter : isSafePassword()
	 * 		- maschinell erzeugte Registrierungen : createCaptcha()
	 * 
	 * 		--> in User-Class: 
	 * 			- zeigt dem Benutzer, ob er Opfer eines m�glichen Bruteforce-Angriffs wurde : isVictimOfBruteforce()
	 * 		
	 * 		--> Quellcode:
	 * 			- bei fehgeschlagenen Login wird eine 0,5 sek�ndige Pause eingelegt, macht Bruteforce schwierig		 
	 */
	
	
	class Security
	{
		/* Verhindert eine Sql-Injection und Cross-Site-Scripting, nach einer Eingabe durch einen Benutzer */
		public function safeEscape($value)
		{
			$string = mysql_real_escape_string($value); // gegen mysql-Injections
			$string = str_replace('<', '&lt;', $string); // gegen html-Injections
			return    str_replace('>', '&gt;', $string);
		}
		
		/* Die Methode verschl�sselt Daten */
		public function encode($string)
		{
			$salt = "ab93S/*1BBsl)'l3!;AlsI39,sc";	
			return md5($string.$salt); // mit Salt verschl�sseln	
		}
		
		
		/* Die Methode untersucht E-Mails auf ihre G�ltigkeit (untersucht den String)*/
		public function isEmail($email)
		{
			return filter_var($email, FILTER_VALIDATE_EMAIL);
		}
		
		/* Die Methode soll ein Captcha erzeugen */
		public function createCaptcha()
		{
			$image = imagecreate(300, 60);
			$lightGrey = imagecolorallocate($image, 215, 215, 215);
			imagefill($image, 0, 0, $lightGrey); // Hintergrundfarbe 
			$text = ""; // hier wird der Text gespeichert 
			
			$black = imagecolorallocate($image, 0, 0, 0); // Schwarz als Schriftfarbe
			for($i = 1; $i <= 6; $i++) // sechs Zeichen sollen im Captcha enthalten sein 
			{
				$character = $this->createRandomLetter();
				$components = $this->createRandomCaptchaComponents();
				imagettftext($image, $components["size"], $components["angle"], $i * 45, $components["y"], $black, "fontfiles/" . $components["fontfile"], $character);
				$text .= $character;
			}
			for($i = 1; $i <= 6; $i++) // zuf�llige Linien erzeugen 
			{
				$color = imagecolorallocate($image, $this->randomNumber(0, 256), $this->randomNumber(0, 256), $this->randomNumber(0, 256));
				imageline($image, $this->randomNumber(0, 300), $this->randomNumber(0, 60), $this->randomNumber(0, 300), $this->randomNumber(0, 60), $color);
			}
			
			imagepng($image, "captcha.png");
			echo "<br /><img src='captcha.png'> <br />";
			imagedestroy($image);
			return $this->encode($text); // Text als R�ckgabe	
		}
		
		/* Die Methode generiert eine zuf�llige Zahl */
		private function randomNumber($min, $max)
		{
			return round(rand($min, $max));	
		}
		
		/* Die Methode generiert eine zuf�llige Gr��e, Winkel und Schriftart */
		private function createRandomCaptchaComponents()
		{
			$captchaComponents = array();
			$fontfiles = array("arial.ttf","Calibri.ttf", "Candara.ttf", "Verdana.ttf", "Georgia.ttf", "times.ttf", "comic.ttf");
			$captchaComponents["size"] = $this->randomNumber(15, 25); // Zufall
			$captchaComponents["angle"] =  $this->randomNumber(0, 1) == 0 ?  $this->randomNumber(0, 60) :  $this->randomNumber(0, -60); // doppelter Zufall
			$captchaComponents["y"] = 25 +  $this->randomNumber(0, 10); // Zufall
			$captchaComponents["fontfile"] = $fontfiles[$this->randomNumber(0, count($fontfiles) - 1)];	// Zufall	
			return $captchaComponents;
		}
		
		/* Generiert ein zuf�lliges Zeichen(Buchstabe oder Zahl) */
		private function createRandomLetter()
		{
			$characters = $this->loadCharacters(); // Zeichen laden
			$charactersArea = $characters[round(rand(0, 2))]; // Zufallszahl f�r die Zeichengruppe
			return $charactersArea[round(rand(0, count($charactersArea) - 1))]; // Zufallszahl f�r das Zeichen innerhalb einer Zeichengruppe
		}
		
		/* Die Methode soll dem Benutzer zeigen, wie sicher sein gew�hltes Passwort ist, bzw. ob es zugelassen wird */
		public function isSafePassword($password)
		{
			if(strlen($password) < 8) // Passwort mindestens acht Zeichen	
				return false;
			if(is_numeric($password)) // nicht nur Nummern(w�rde sp�ter auch rausgefunden werden, kostet so aber weniger Zeit)
				return false;
			$characters = $this->loadCharacters(); // Array mit m�glichen Buchstaben/Zeichen
			$charactersPassword = str_split($password, 1); // Passwort in verschiedene Buchstaben unterteilen
			$isPartOfCharacterGroup = array(false, false, false, false); // Array speichert, ob eines der Buchstaben einer bestimmten Zeichengruppe angeh�rt, z.B. Zahlen
			for($i = 0; $i < count($characters); $i++) // f�r alle m�glichen Zeichengruppen
			{
				for($j = 0; $j < count($characters[$i]); $j++) // f�r alle m�glichen Zeichen in einer Zeichengruppe
				{
					for($k = 0; $k < count($charactersPassword); $k++) // f�r alle Zeichen eines Passwortes
					{
						if(strcmp($charactersPassword[$k], $characters[$i][$j]) == 0) // wenn Zeichen gleich sind
							$isPartOfCharacterGroup[$i] = true; // Zeichen, und damit der ganze String, ist Bestandteil dieser Gruppe
					}
				}
			}	
			for($i = 0, $useDifferentCharacters = 0; $i < count($isPartOfCharacterGroup); $i++)
			{
				if($isPartOfCharacterGroup[$i]) // wenn Zeichen des Passworts in dieser Zeichengruppe enhalten 
					$useDifferentCharacters++; // Z�hler, der angibt wie viele Zeichen aus verschiedenen Zeichengruppen enthalten sind, ++
			}
			if($useDifferentCharacters < 2) // mindestens zwei verschiedene Zeichengruppen verwenden (kann auch versch�rft werden!)
				return false;
			
			return true; // Passworttest bestanden!
		}
		
		/* die Methode liefert ein mehrdimensionales Array, bei denen die Arrays jeweils verschiedene Zeichengruppen beinhalten, z.B. Gro�buchstaben*/
		private function loadCharacters()
		{
			$characters = array();
			$characters[0] = $lowerCase = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"); // Gro�buchstaben
			$characters[1] = $upperCase = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"); // Kleinbuchstaben
			$characters[2] = $numbers = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0"); // Zahlen
			$characters[3] = $special = array("�", "�", "�", "�", "�", "�","�", "^", "�", "!", "\"", "�", "�", "�", "$", "%", "&", "/", "{", "(", "[", ")", "]", "=", "}", "?", "\\", "�", "`", "+", "*", "~", "#", "'", "-", "_", ".", ":", ",", ";", "@", "�", "�"); // sonstiges 
			return $characters;
		}
	}

?>