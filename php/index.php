<!-- 
	
	Manuel Treutlein 
 	
	Die Seite ist die Startseite für die Website und außerdem die Loginseite
	Sie beinhaltet nur die Programmierung keine Designelemente

 -->

<!DOCTYPE html>
<html>
<head>
<title>Startseite / Loginseite</title>
</head>
<body>

<!-- Login funktioniert im Moment nur mit einer Zahlenfolge! Dies kann bzw. sollte später noch geändert werden -->
<!-- Problem: Leute, die die Zahlenfolge kennen, können vermeintliche  Nachhilfeschüler erstellen -->
<h1>Login</h1>
<form action="login.php" method="post">
	<p>E-Mail: <input name="e-mail"/></p>
	<p>Passwort: <input name="password" type="password"/></p>
	<p><input value="Einloggen" type="submit"/></p>
</form>

<hr color="red">

<h2>Neu Registrieren</h2>
<form action="register.php" method="post">
	<table border="1">
		<tr>
			<td>E-Mail: </td>
			<td><input name="e-mail"/></td>
		</tr>
		<tr>
			<td>Jahrgangsstufe: </td>
			<td>
				<select name="grade">
					<option></option>
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
					<option value="6">6</option>
					<option value="7">7</option>
					<option value="8">8</option>
					<option value="9">9</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="12">12</option>
					<option value="13">13</option>
					
				</select>
			</td>
		</tr>
		<tr>
			<td>Schule: </td>
			<td>AvH (festgelegt)</td>
		</tr>
		<tr>
			<td>Passwort:</td>
			<td><input name="password" type="password"/></td>
		</tr>	
		<tr>
			<td>bestätige Passwort:</td>
			<td><input name="provePassword" type="password"/></td>
		</tr>
	</table>
	<?php // Captcha einfügen 
		include_once 'Security.php';
		$sec = new Security();
		$text = $sec->createCaptcha();
		echo "<input name='textCaptcha' type='hidden' value='$text'/>";
	?>
	<p>
		Text eingeben: <input name="entryCaptcha" />
		<a href="index.php"><img src="images/refresh-icon.png"></a><!-- nur provisorisch -->
	</p>
	<input value="Registrieren" type="submit"/>
</form>

</body>
</html>