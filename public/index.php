<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<title>Pyl-One</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="style.css">
</head>	
<body>
	<header><?php include("header.php") ?></header>
	<?php
	if(empty($_SESSION['MBuser'])){
		//si post, validation, etc... Si compte en attente d'activation, le signaler
		?>
		<h1>Bienvenue sur Pyl-One !</h1>
		<form method="post" action="connect">
			<label><u>Ton pseudo :</u>&nbsp<input type="text" name="pseudo"></label></br>
			<label><u>Ton mot de passe :</u>&nbsp<input type="password" name="passwd"></label></br>
			<a href="">Mot de passe oublié</a></br></br><!-- Add link for forgotten password -->
			<button type="submit">Connexion</button>
		</form>
		<a href="signup"><button type="button">Créer un compte</button></a>
		<footer><?php include("footer.php") ?></footer>

		<?php
		if(!empty($_POST)){	//user has set at least one input, we proceed to authentification
			$valid_data = 0;
			$valid_err_msgs = array();

		}
	}
	else{

		header("Location: main.php");
	}
	
	?>
</body>
</html>
