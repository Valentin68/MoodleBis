<?php
	$mdp = "abcd";
	echo "<p>voici le hash du mot de passe \"".$mdp."\" : ".password_hash($mdp, PASSWORD_DEFAULT)."<p>";
	$hash = '$2y$10$2C0vCfaLGEEXNe.UMkozYuOr9/zZ4N/fTvr8Ov9KrtINOh6KQOfk2';
	if(password_verify($mdp, $hash)){
		$str = "true";
	} else{
		$str = "false";
	}
	echo "<p>Match : ". $str ."</p>";
?>