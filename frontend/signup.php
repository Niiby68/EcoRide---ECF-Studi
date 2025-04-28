<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>EcoRide - Inscription</title>
	<link rel="stylesheet" href="css/style.css">
</head>

<body>
<header>
    <nav>
        <ul>
            <li><a href="index.html">Accueil</a></li>
            <li><a href="signup.php">Inscription</a></li>
        </ul>
    </nav>
	
	<h1>Inscription sur EcoRide</h1>
</header>

<main>
	<form action="#" method="post">
		<label for="pseudo">Pseudo :</label>
		<input type="text" id="pseudo" name="pseudo" required><br><br>

		<label for="email">Adresse email :</label>
		<input type="email" id="email" name="email" required><br><br>

		<label for="mot_de_passe">Mot de passe :</label>
		<input type="password" id="mot_de_passe" name="mot_de_passe" required><br><br>

		<label for="role">Rôle :</label>
		<select id="role" name="role" required>
			<option value="passager">Passager</option>
			<option value="chauffeur">Chauffeur</option>
			<option value="les_deux">Les deux</option>
		</select><br><br>

		<input type="submit" value="S'inscrire">
	</form>
</main>

<footer>
	<p>&copy; 2025 EcoRide - Tous droits réservés.</p>
</footer>
</body>
</html>
