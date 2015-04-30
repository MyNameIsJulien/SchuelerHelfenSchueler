<?php
	/*
 	* Autor: Manuel Treutlein
	* Klasse: User
	* Projekt: Schüler_helfen_Schüler
	*/

	/*
	 * Die Klasse Sql stellt die klassischen Methoden für die Datenbank zur Verfügung
	 */
	
	
	// Einige Konstaten, die benötigt werden
	
	// für den Host
	define("HOST", "localhost"); // DEFINIEREN
	// Benutzername für die Datenbank
	define("HOSTNAME", ""); // DEFINIEREN 
	// Passwort für die Datenbank
	define("HOSTPASSWORD", ""); // DEFINIEREN
	
	
	
	// Klasse
	class Sql 
	{
		private $id;
		private $connection;
		
		/* Die Funktion stellt eine Verbindung zur Datenbank, ähnlich wie der Konstruktor her */
		private function connect()
		{
			// Verbindung zur Datenbank erstellen
			$this->connection = mysqli_connect(HOST, HOSTNAME, HOSTPASSWORD);
			mysqli_select_db($this->connection, "schuelerhelfenschueler");
		}
		
		/* Schließt die Verbindung zur Datenbank */
		private function close()
		{
			mysqli_close($this->connection);
		}
		
		/* Gettter für id */
		public function id()
		{
			return $this->id;
		}
		
		public function setId($email)
		{
			$this->id = $this->search(	"h_id",
					"`haupttabelle` AS `h` LEFT JOIN `e-mails` AS `e` ON `h`.`E-Mail_id` = `e`.`id`",
					" `e`.`EMail` LIKE '$email'");
		}
		
		/* Liefert die Anzahl der Zeilen einer Tabelle zurück */
		public function countLinesOf($table)
		{
			$this->connect();
			$result =  mysqli_query($this->connection, "SELECT COUNT(*) AS numberOfRows FROM $table;");
			$lines = mysqli_fetch_array($result);
			$this->close();
			return $lines["numberOfRows"];
		}
		
		/* Sucht genau einen durch die Parameter bestimmten Datensatz aus der Datenbank heraus und liefert den dazugehörigen String */
		public function search($select, $from, $where)
		{
			$this->connect(); // Verbindung wieder aufnehmen, wenn sie zwischenzeitlich abgebrochen ist
			$result = mysqli_query($this->connection, "SELECT  $select  FROM  $from  WHERE  $where;"); // Abfrage
			$return = mysqli_fetch_row($result); // in Array speichern
			$this->close();
			return $return[0]; // ersten Index zurückgeben
		}
		
		/* Funktioniert wie die search()-Methode, liefert aber eine ganze Reihe zurück, abgespeichert in einem Array */
		public function searchColumn($select, $from, $where)
		{
			$this->connect();
			$result = mysqli_query($this->connection, "SELECT  $select  FROM  $from  WHERE  $where;"); // Abfrage
			for($i = 0;$row = mysqli_fetch_assoc($result);$i++) // In einem numerischen Array speichern
			{
				$return[$i] = $row["$select"];	// Zuweisung
			}
			$this->close();
			return $return; // liefert hier den ganzen Array zurück
		}

		/* Die Funktione trägt eine neue Dateneinheit in die Datenbank ein. Als Parameter muss die Tabelle und eine Anweisung übergeben werden*/
		public function updateOneEntry($table, $set, $where)
		{
			$this->connect();
			$update = "UPDATE $table SET $set WHERE $where;"; 
			mysqli_query($this->connection, $update);
			$return = (mysqli_affected_rows($this->connection) > 0);
			$this->close();
			return $return;
		}
		
		/*trägt eine neue Zeile in die Datenbank ein */
		public function insert($table, $instruction)
		{
			$this->connect();
			$insert = "INSERT INTO $table $instruction;";
			mysqli_query($this->connection, $insert);
			$return = (mysqli_affected_rows($this->connection) > 0);
			$this->close();
			return $return;		
		}
	}
?>