<?php
	/*
	*  Manuel Treutlein
	*
	*  Die Seite stellt ein Formular zur Verfügung, mit dem ein erster Mailkontakt ermöglich wird
	*/
		
	$lengthInputAdress =  strlen($_POST["email"]) + 3;
	
	echo "<h1>Kontaktformular</h1>";
	echo "<p>Kontaktiere deinen ausgewählten Nachhilfepartner per E-Mail.</p>";
	echo "<a href='main.php'>Zurück zur Hauptseite</a>";
	echo "<form action='sendEmail.php' method='post'>";
		echo "<table border='1'>";
		echo "<tr><td>E-Mail-Adresse:</td> <td><input name='adress' readonly='readonly' size='" . $lengthInputAdress .  "' value='" . $_POST["email"] . "'/></td></tr>";
		echo "<tr><td>Betreff:</td> <td><input name='subject' size='30' value='Schüler-helfen-Schüler '></td></tr>";
		echo "<tr><td>E-Mail-Text:</td> <td><textarea name='text' cols='100' rows='10'></textarea></td></tr>"; 
		echo "<tr><td><input type='submit' value='E-Mail senden'/></td></tr>";
		echo "</table>";
	echo "</form>";
?>