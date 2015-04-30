 <!-- 

 	Manuel Treutlein 

 	Die Seite dient für den Benutzer, seine Daten zu verwalten, und wenn nötig zu ändern
 
  -->

<!DOCTYPE html>
<html>
<head>
	<title>S-h-S Datenverwaltung</title>
</head>
<body>
	<?php 
		include_once 'User.php';
		session_start();
		$user = $_SESSION['user'];
	?>
	<h1>Datenverwaltung</h1>
	<p>Du kannst hier deine eingegebenen Daten verwalten und updaten, indem du die neuen Daten in die entsprechenden Felder einträgst</p>
	<p><a href="main.php">Zur Hauptseite</a></p>
<form action="enterNewData.php" method="post">
	<table border=2>
		<tr>
			<th>Datenart</th>
			<th>neuer Eintrag</th>
			<th>alter Eintrag</th>
		</tr>
		
		
		<!-- E-Mail: -->
		<tr>
			<td>E-Mail: </td>
			<td><input name = "new_email"/></td>
			<td>
				<?php 
					echo $user->valueThroughID("`e`.`EMail`", "`haupttabelle` AS `h` LEFT JOIN `e-mails` AS `e` ON `h`.`h_id` = `e`.`id`");
				?>
			</td>
		</tr>
		
		
		<!-- Jahrgangsstufe: -->
		<tr>
			<td>Jahrgangsstufe: </td>
			<td>
				<select name="new_grade">
					<?php 
						echo "<option value=''></option>"; // eine leere Option
						for($i = 1; $i <= 13; $i++) echo "<option value='$i'>$i</option>"; // Options von 1-13 erzeugen
					?>
				</select>
			</td>
			<td>
				<?php 
					echo $user->valueThroughID("Jahrgangsstufe", "haupttabelle AS `h`");
				?>
			</td>
		</tr>
		
		
		<!-- Schule: -->
		<tr>
			<td>Schule: </td>
			<td>nicht möglich</td>
			<td>AvH</td>
		</tr>
		<tr>
			<td><hr size="3" color="red"></<td>
			<td><hr size="3" color="red"></<td>
			<td><hr size="3" color="red"></<td>
			<td><hr size="3" color="red"></<td>
		</tr>
		
		
		<!-- Fächer, die der Benutzer sucht! -->
		<tr>
			<td>Suche: </td>
			<td>
				<select multiple="multiple" name="new_search[]">
					<?php 
						$subjects = $user->searchColumnThroughID("Fach", "faecher");
						echo "<option></option>"; // eine leere Option
						for($i = 0;$i < count($subjects); $i++) 
						{	
							echo "<option value='$subjects[$i]'>$subjects[$i]</option>";
						}
					?>
				</select>
			</td>
			<td>
				<?php 
					$array_search = array();
					$array_search = $user->stringToArray($user->valueThroughID("sucheFaecher_id", "`haupttabelle` as `h`")); // den suche_Facher_id-String in einen Array umwandeln
					for($i = 0; $i < count($array_search); $i++) // Array durchlaufen
					{
						echo $user->search("Fach", "faecher", "id = $array_search[$i]") . "<br />"; // Fach für die Id ausdrucken
					}
				?>	
			</td>
			<td><a href="###">Fach fehlt: Kontakt</a></td> <!-- Hier kann eine Verlinkung geschaltet werden, damit noch nicht vorhandene Fächer hinzugefügt werden können -->
		</tr>		
		<!-- Für das Fach, dass der Benutzer sucht, die Tage, an denen er kann -->
		<tr>
			<td>Zeit am:(Suche) </td> 
			<td>
				<select multiple="multiple" name="sucheFaecher_new_weekday[]">
					<option></option>
					<option value="Montag">Montag</option>
					<option value="Dienstag">Dienstag</option>
					<option value="Mittwoch">Mittwoch</option>
					<option value="Donnerstag">Donnerstag</option>
					<option value="Freitag">Freitag</option>
					<option value="Samstag">Samstag</option>
					<option value="Sonntag">Sonntag</option>
					<option value="---">---</option>
				</select>
			</td>
			<td>
				<?php 
					$array_weekday = array();
					$array_weekday = $user->stringToArray($user->valueThroughID("sucheFaecher_Wochentage_id", "`haupttabelle` as `h`"));
					for($i = 0; $i < count($array_weekday); $i++)
					{
						echo $user->search("Wochentag", "wochentage", "id = $array_weekday[$i]") . "<br />";
					}	
				?>
			</td>
		</tr>
		<!-- Für das Fach, dass der Benutzer sucht, die Stunden pro Woche, die er aufwenden will -->
		<tr>
			<td>Studen pro Woche(ca.): </td>
			<td>
				<?php 
					$oldHpW =  $user->valueThroughID("sucheFaecher_Stundenzahl", "haupttabelle AS `h` ");
					echo "<input type='number' value='$oldHpW' min='0' max='99' name='sucheFaecher_new_hoursPerWeek'/></td>";
				?>
			<td>
				<?php 
					echo $user->valueThroughID("sucheFaecher_Stundenzahl", "haupttabelle AS `h` ");
				?>
			</td>
		</tr>
		<tr>
			<td><hr size="3" color="red"></<td>
			<td><hr size="3" color="red"></<td>
			<td><hr size="3" color="red"></<td>
			<td><hr size="3" color="red"></<td>
		</tr>
		
		
		<!-- Faecher, die der Benutzer anbietet -->
		<tr>
			<td>Gebe: </td>
			<td>
				<select multiple="multiple" name="new_offer[]">
					<?php 
						$subjects = $user->searchColumnThroughID("Fach", "faecher");
						echo "<option></option>"; // eine leere Option
						for($i = 0;$i < count($subjects); $i++) 
						{	
							echo "<option value='$subjects[$i]'>$subjects[$i]</option>";
						}
					?>
				</select>
			</td>
			<td>
				<?php 
					$array_offer = array();
					$array_offer = $user->stringToArray($user->valueThroughID("gebeFaecher_id", "`haupttabelle` as `h`"));
					for($i = 0; $i < count($array_offer); $i++)
					{
						echo $user->search("Fach", "faecher", "id = $array_offer[$i]") . "<br />";
					}	
				?>	
			</td>
			<td><a href="###">Fach fehlt: Kontakt</a></td>
		</tr>
		<tr>
			<td>Zeit am:(Geben) </td> 
			<td>
				<select multiple="multiple" name="gebeFaecher_new_weekday[]">
					<option></option>
					<option value="Montag">Montag</option>
					<option value="Dienstag">Dienstag</option>
					<option value="Mittwoch">Mittwoch</option>
					<option value="Donnerstag">Donnerstag</option>
					<option value="Freitag">Freitag</option>
					<option value="Samstag">Samstag</option>
					<option value="Sonntag">Sonntag</option>
					<option value="---">---</option>
				</select>
			</td>
			<td>
				<?php 
					$array_weekday = array();
					$array_weekday = $user->stringToArray($user->valueThroughID("gebeFaecher_Wochentage_id", "`haupttabelle` as `h`"));
					for($i = 0; $i < count($array_weekday); $i++)
					{
						echo $user->search("Wochentag", "wochentage", "id = $array_weekday[$i]") . "<br />";
					}	
				?>
			</td>
		</tr>
		<tr>
			<td>Studen pro Woche(ca.): </td>
			<td>
				<?php 
					$oldHpW =  $user->valueThroughID("gebeFaecher_Stundenzahl ", "haupttabelle AS `h` ");
					echo "<input type='number' value='$oldHpW' min='0' max='99' name='gebeFaecher_new_hoursPerWeek'/></td>";
				?>
			<td>
				<?php 
					echo $user->valueThroughID("gebeFaecher_Stundenzahl ", "haupttabelle AS `h` ");
				?>
			</td>
		</tr>
		<tr>
			<td><hr size="3" color="red"></<td>
			<td><hr size="3" color="red"></<td>
			<td><hr size="3" color="red"></<td>
			<td><hr size="3" color="red"></<td>
		</tr>
		
		
		<!-- Status -->
		<tr>
			<td>Status: </td>
			<td>
				<select name="new_activity">
					<?php 
						$aktivitaet = $user->searchColumnThroughID("Aktivitaet", "aktivitaet");
						echo "<option value=''></option>"; // eine leere Option
						for($i = 0; $i < count($aktivitaet); $i++) 
						{	
							echo "<option value='$aktivitaet[$i]'>$aktivitaet[$i]</option>";
						}
					?>	
				</select>	
			</td>
			<td>
				<?php 
					echo $user->valueThroughID("`a`.`Aktivitaet`", "`haupttabelle` as `h` left join `aktivitaet` as `a` on `h`.`aktivitaet_id` = `a`.`id`");
				?>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Updaten"/></td>
		</tr>
	</table>		
</form>
<br />
<br />
<form action="enterNewPassword.php" method="post">
	<table border="1">
		<tr>
			<th>Passwort ändern</th>
		</tr>
		<tr>
			<td>Altes Passwort</td>
			<td><input type="password" name="old_password"/></td>
		</tr>
		<tr>
			<td>Neues Passwort</td>
			<td><input type="password" name="new_password" /></td>
		</tr>
		<tr>
			<td>Neues Passwort</td>
			<td><input type="password" name="prove_new_password" /></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Pw ändern"/></td>
		</tr>
	</table>
</form>
<p><a href="main.php">Zur Hauptseite</a></p>
</body>
</html>