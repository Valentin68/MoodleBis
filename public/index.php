<?php
session_start();
$sitename = "MoodleBis";
?>

<!DOCTYPE html>
<html>
<head>
	<title><?php echo $sitename ?></title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="style.css">
</head>	
<body>
	<header><?php include("header.php") ?></header>
	<?php
	if(empty($_SESSION)){
		?>
		<h1>Bienvenue sur <?php echo $sitename ?> !</h1>
		<form method="post" action="connect.php">
			<label><u>Ton pseudo :</u>&nbsp<input type="text" name="pseudo"></label></br>
			<label><u>Ton mot de passe :</u>&nbsp<input type="password" name="passwd"></label></br>
			<a href="">Mot de passe oublié</a><br><!-- Add link for forgotten password -->
			<button type="submit">Connexion</button>
		</form>
		<a href="signup"><button type="button">Créer un compte</button></a>
		<footer><?php include("footer.php") ?></footer>

		<?php
	}
	else{
		header("Location: main.php");
	}
	?>
</body>
</html>
