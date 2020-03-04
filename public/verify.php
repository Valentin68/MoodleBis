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
	require_once("clean_input.php");

	if(isset($_GET['token']) && !empty($_GET['token']) AND isset($_GET['hash']) && !empty($_GET['hash'])){
		require_once("dbconnect.php");
		$finduser = $bdd->prepare("SELECT students.ID AS stuID, activation_tokens.hash AS hash FROM students INNER JOIN activation_tokens ON students.ID = activation_tokens.student_ID WHERE activation_tokens.ID = ?");
		$finduser->execute(array(
			clean_input($_GET['token'])
		));
		$res = $finduser->fetch();
		if(empty($res)){
			echo '<h2 class="fail">Une erreur s\'est produite lors de l\'activation de ton compte</h2><p class="fail">Contacte l\'administrateur avec le code d\'erreur suivant : ERR_ACT_ACCNOTFOUND</p>';
		}
		else{
			if($_GET['hash']==$res['hash']){
				$FindExpiryDate = $bdd->prepare("SELECT * FROM activation_tokens WHERE ID = ? AND DAexpiry_date<");
				$FindExpiryDate->execute(array(
					clean_input($_GET['token'])
				));
				$resp = $FindExpiryDate->fetch();
				$ExpiryDate = $resp['expiry_date'];

				if($ExpiryDate>=date('m/d/Y h:i:s a', time())){
					$ActivateAccount = $bdd->prepare('UPDATE students SET active = 1 WHERE ID = ?');
					$ActivateAccount->execute(array(
						$res['stuID']
					));
					echo '<h2 class="success">Ton compte a été activé avec succès</h2><p class="success">Connecte toi avec tes identifiants en cliquant <a href="index">ici</a></p>';
				}else{
					echo '<h2 class="fail">Une erreur s\'est produite lors de l\'activation de ton compte</h2><p class="fail">Le lien d\'activation est expiré. Clique <a href="signup"><b>ici</b></a> pour recommencer ton inscription.</p>';
				}


			}else{
				echo '<h2 class="fail">Une erreur s\'est produite lors de l\'activation de ton compte</h2><p class="fail">Contacte l\'administrateur avec le code d\'erreur suivant : ERR_ACT_ACCNOTFOUND</p>';
			}
		}
	}else{
		echo '<h2 class="fail">Une erreur s\'est produite lors de l\'activation de ton compte</h2><p class="fail">Contacte l\'administrateur avec le code d\'erreur suivant : ERR_ACT_MISSINGGETFIELDS</p>';
	}
	?>
	<footer><?php include("footer.php") ?></footer>
</body>
</html>