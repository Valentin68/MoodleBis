<!DOCTYPE html>

<html>
<head>
	<title>TEST</title>
	<meta charset="utf-8">
</head>
<body>
	<?php
		include("dbconnect.php");
		$req = $bdd->prepare('SELECT pseudo FROM students');
		$req->execute();
		while($res = $req->fetch()){
			echo $res['pseudo'];
		}

	?>
</body>
</html
