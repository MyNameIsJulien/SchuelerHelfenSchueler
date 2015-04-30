 <!-- 

 	Manuel Treutlein 

 	Die Seite bildet die Hauptseite mit der Programmierung. Die fertige-designte Seite kann eventuelle auch 
 	auf mehrere Seiten aufgeteilt werden. Dafür müssten hauptsächlich die Adressziele der Formulare abgeändert werden. 
 
  -->
  
<!DOCTYPE html>
<html>
<head>
<title>Schüler-helfen-Schüler</title>
</head>
<body>
	<?php 
		include_once 'User.php';
		session_start();
		$user = $_SESSION['user'];
	?>
	
	<h1>Schüler-helfen-Schüler</h1>
	<p>Eingeloggt als: <b><?php echo $user->valueThroughID("`e`.`EMail`", "`haupttabelle` AS `h` LEFT JOIN `e-mails` AS `e` ON `h`.`h_id` = `e`.`id`"); ?></b></p>
	<p><?php 
			if($user->isVictimOfBruteforce())
				echo "<b>Sie sind vielleicht Opfer eines Brutforce-Angriffs Überprüfen Sie, ob in mit Ihrem Konto alles in Ordung ist und wenden Sie sich ggf. an uns</b>";
		?>
	</p>
	<p><a href="logout.php">Ausloggen</a></p>
	<p>Diese Platform findet für dich automatisch den passenden Nachhilfeschüler.</p>
	<p>Du kannst auch deine Nachhilfe anbieten und deine E-Mail hinterlassen auf die dich dein zukünftiger Nachhilfeschüler anschreiben kann.</p>

	<p><a href="dataAdministration.php">Einstellungen</a></p>
	
	<!-- Hier beginnt der Algorithmus, der den Nachhilfeschüler, und den Schüler, der Nachhilfe geben will, zusammenbringen soll 
		 Für den Benutzer erscheint eine Liste, aufsteigend mit den am geeignetesten Nachhilfepartner -->
	<h3>Aktive Suche: (Algorithmus)</h3>
	<form action="algorithm.php" method="post">
		Suche <input type="radio" name="searchOrGive" value="search" checked="checked" />
		Gebe <input type="radio" name="searchOrGive" value="give" />
		<a href="dataAdministration.php">Daten bearbeiten</a>
		<p>Filtern: <p> <!-- zwei Möglichkeiten des Filterns bieten -->
		<p>Jahrgangsstufe:
			<select name="from_grade">
				<?php
					echo "<option value=''></option>"; // eine leere Option
					for($i = 1; $i <= 13; $i++) echo "<option value='$i'>$i</option>"; // Options von 1-13 erzeugen
				?>
			</select>
			-
			<select name="to_grade">
				<?php
					echo "<option value=''></option>"; // eine leere Option
					for($i = 1; $i <= 13; $i++) echo "<option value='$i'>$i</option>"; // Options von 1-13 erzeugen
				?>
			</select>
		</p>
		<p>Index größer: 
			<select name="index_bigger">
				<?php 
					echo "<option value=''></option>";
					for($i = 10; $i < 100; $i += 10) echo "<option value='$i'>$i%</option>"; // 10 - 90 %
				?>
			</select>
		</p>
		<input type="submit" value="Suche starten" />
	</form>
</body>
</html>