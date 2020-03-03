<!DOCTYPE html>
<html>
<head>
	<title>Activation du compte</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<header><?php include("header.php") ?></header>
	<h1>Activation du compte</h1>
	<?php
		function clean_input($data) {
		$data = htmlspecialchars($data);
		$data = mysql_escape_string($data);
		return $data;
	}

	if(isset($_GET['pseudo']) && !empty($_GET['pseudo']) AND isset($_GET['hash']) && !empty($_GET['hash'])){
	 	include("dbconnect.php");

	 	$finduser = $bdd->prepare("SELECT * FROM students WHERE pseudo = ?");
	 	$finduser->execute(array(
	 		clean_input($_GET['pseudo']);
	 	));
	    echo '<h2 class="success">Ton compte a bien été activé, tu es désormais connecté au site !</h2>';	
	}else{
	    echo '<h2 class="fail">Une erreur s\'est produite lors de l\'activation de ton compte, contacte l\'administrateur avec le code d\'erreur suivant : ERR_ACT_MISSING_OGETFIELDS</h2>';
	}
	?>
<footer><?php include("footer.php") ?></footer>
</body>
</html>