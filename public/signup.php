<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<title>Créer un compte</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="style.css">
	<script src="https://www.google.com/recaptcha/api.js?hl=fr" async defer>
	</script>
</head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"  ></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('#department').change(function(){
			var dpt_id=$('#department').val();
			var mjr_sel = document.getElementById("major");
			var mjr_lbl = document.getElementById("major_sel");
			mjr_lbl.style.display="inline-block";
			mjr_sel.required = true;
			if(dpt_id.localeCompare("1")==0){
				mjr_lbl.style.display="none";
				mjr_sel.required = false;
			}
			$('#major').empty();
			$.get('populate_majors',{'dpt_id':dpt_id},function(return_data){
				$("#major").append("<option value=''>Sélectionne ta filière</option><option value='no_major'>Pas encore de filière</option>");
				if(return_data.data.length>0){
					$.each(return_data.data, function(key,value){
						$("#major").append("<option value='" + value.ID +"'>"+value.code +" | "+value.name+"</option>");
					});
				}else{
				}	

			}, "json");

		});
	});

	function change(){
		var e = document.getElementById("promo");
		var selectedPromo = e.options[e.selectedIndex].text;
		console.log(selectedPromo);
		var frame = document.getElementById("promo_img");
		frame.setAttribute("src","promo_logos/promo_"+selectedPromo+".png");
		frame.style.visibility = "visible";
	}

	function enableSubBtn(){
		var Btn = document.getElementById("sub");
		var Checkbox1 = document.getElementById("accept_help");
		var Checkbox2 = document.getElementById("accept_tos");
		if(Checkbox1.checked==true && Checkbox2.checked==true){
			Btn.disabled = false;
		}else{
			Btn.disabled = true;
		}
	}

	function ShowMailField(){
		var Chkbox = document.getElementById("notifs_on_utbm_adress");
		var field = document.getElementById("mail_field");
		if(Chkbox.checked){
			field.style.display="none";
		}else{
			field.style.display="block";
		}
	}

	window.onload = function(){
		ShowMailField();
		enableSubBtn();
		var recaptcha = document.forms["signup"]["g-recaptcha-response"];
		recaptcha.required = true;
		recaptcha.oninvalid = function(e) {
			alert("Tu n'as pas complété le captcha !");
		}
	}

	function validatePassword(){
		var passwd = document.getElementById("passwd");
		var passwd_conf = document.getElementById("passwd_conf");
		if(passwd.value != passwd_conf.value){
			passwd_conf.setCustomValidity("Les mots de passe saisis sont différents");
		}else{
			passwd_conf.setCustomValidity("");
		}
	}

</script>

<body>
	<header><?php include("header.php") ?></header>
	<h1>Créer un compte</h1>
	<?php
	require_once("dbconnect.php");
	require_once("clean_input.php");

	if(empty($_SESSION['MBuser'])){
		$valid_err_msgs = array();
		require_once('recaptchalib.php');
		$recap_key_file = parse_ini_file('../private/recaptcha_key.ini');
		$recap_key = $recap_key_file['key'];
		if(!empty($_POST)){	//user has set at least one input, we proceed to server-side validation
			if(preg_match("#[A-Za-z0-9]{5,25}$#", $_POST['pseudo'])!=1){
				array_push($valid_err_msgs, "Ton pseudo est vide ou ne respecte pas le format imposé (entre 5 et 25 lettres/chiffres)");
			}
			//validate uniqueness of pseudo
			if(isset($_POST['pseudo'])){
				$req_pseudo = $bdd->prepare("SELECT * FROM students WHERE pseudo = ?");
				$req_pseudo->execute(array(
					clean_input($_POST['pseudo'])
				));
				if(!empty($req_pseudo->fetch())){
					array_push($valid_err_msgs, "Le pseudo que tu as choisi est déjà utilisé par un autre étudiant sur cette plateforme");
				}
			}
			if(preg_match("#^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\d]){1,})(?=(.*[\W]){0,})(?!.*\s).{5,20}$#", $_POST['passwd'])!=1){
				array_push($valid_err_msgs, "Ton mot de passe est vide ou ne respecte pas le format imposé (entre 5 et 20 caractères dont au moins 1 chiffre, 1 minuscule et 1 majuscule)");
			}
			if($_POST['passwd_conf']!=$_POST['passwd']){
				array_push($valid_err_msgs, "La confirmation de ton mot de passe a échoué");
			}
			if(preg_match("#^[a-z-]+\.[a-z-]+[0-9-]*@utbm\.fr$#", $_POST['utbm_mail'])!=1){
				array_push($valid_err_msgs, "Tu n'as pas saisi ton adresse mail UTBM correctement");	
			}
			//let's check that the utbm address has not already been used to activate another account
			if(isset($_POST['utbm_mail'])){
				$reqmail = $bdd->prepare('SELECT * FROM activation_used_addresses WHERE utbm_address = ?');
				$reqmail->execute(array(
					clean_input($_POST['utbm_mail'])
				));
				if(!empty($reqmail->fetch())){
					array_push($valid_err_msgs, "Ton adresse mail UTBM a déjà servi à activer un compte. Contacte l'<a href=\"mailto:\">administrateur</a> de la plateforme pour forcer la création du compte.");	//SET ADMIN ADDRESS
				}
			}
			//iff it was entered, validate optional notification address is a general email address
			if(isset($_POST['notif_mail']) AND !empty($_POST['notif_mail'])){
				if(!filter_var($_POST['notif_mail'], FILTER_VALIDATE_EMAIL)){
					array_push($valid_err_msgs, "L'adresse mail de notification que tu as saisie n'est pas valide");	
				}
			}
			//valide promo is a number between 1 and the current promotion
			$year = date('Y', time());
			$month = date('m', time());
			$nb_prom_to_show = 6;
			$outgoing_prom = $year-$nb_prom_to_show+boolval($month>=2); 	//if we're after September, then we increase the outgoing promotion
			$outgoing_prom%=100;
			$last_prom = $outgoing_prom+$nb_prom_to_show;
			if(!isset($_POST['promo']) OR !(($_POST['promo'])>=1 AND $_POST['promo']<=$last_prom)){
				array_push($valid_err_msgs, "Tu n'as pas sélectionné une promo valide");
			}
			if(isset($_POST['department'])){
				$selected_dpt = $_POST['department'];
			}else{
				array_push($valid_err_msgs, "Tu n'as pas sélectionné de département");
			}
			$dpt_req = $bdd->prepare('SELECT ID FROM departments');
			$dpt_req->execute();
			$existing_dpts = array();
			while($result = $dpt_req->fetch()){
				array_push($existing_dpts, $result['ID']);
			}
			if(!in_array($selected_dpt, $existing_dpts)){
				array_push($valid_err_msgs, "Tu as sélectionné un département vide ou inexistant");
			}
			if(isset($_POST['department']) AND $selected_dpt!=1){	//selected department is different from TC, thus we must validate the selected major
				if(!isset($_POST['major'])){
					array_push($valid_err_msgs, "Tu n'as pas sélectionné de filière");
				}else{
					$selected_major = $_POST['major'];
					if($selected_major!='none'){
						$existing_majors = array();
						$mjr_req = $bdd->prepare('SELECT ID FROM majors WHERE department_ID = ?');
						$mjr_req->execute(array(
							clean_input($selected_dpt)
						));
						while($result = $mjr_req->fetch()){
							array_push($existing_majors, $result['ID']);
						}
						if(!in_array($selected_major, $existing_majors)){
							array_push($valid_err_msgs, "Tu as sélectionné une filière vide ou inexistante");
						}
					}
				}
			}
			if($_POST['accept_help']!='on'){
				array_push($valid_err_msgs, "Tu n'as pas lu le manuel d'utilisation de la plateforme");
			}
			if($_POST['accept_tos']!='on'){
				array_push($valid_err_msgs, "Tu n'as pas lu les Conditions Générales d'Utilisation");
			}
			if(isset($_POST['g-recaptcha-response'])){
				$captcha=$_POST['g-recaptcha-response'];
			}
			if(!$captcha){
				array_push($valid_err_msgs, "Tu as mal complété le captcha");
			}
			$secretKey = $recap_key;
			$ip = $_SERVER['REMOTE_ADDR'];
        // post request to server
			$url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) .  '&response=' . urlencode($captcha);
			$response = file_get_contents($url);
			$responseKeys = json_decode($response,true);
        // should return JSON with success as true
			if(!$responseKeys["success"]) {
				array_push($valid_err_msgs, "La validation du captcha a échoué");
			}
			if(empty($valid_err_msgs)){
				//proceed to database insertions with cleaned data
				//insertion of activation address
				$insertaddress = $bdd->prepare("INSERT INTO activation_used_addresses VALUES(?)");
				$insertaddress->execute(array(
					clean_input($_POST['utbm_mail'])
				));
				//insertion of student
				$insertuser = $bdd->prepare("INSERT INTO students(active,upload_pts,pseudo,notif_email_address,passwd_hash,department_ID,major_ID,promo,creation_date,deletion_date) VALUES(0,0,:pseudo,:notif_mail,:passwd_hash,:department_ID,:major_ID,:promo,NOW(),0)");
				if($_POST['notifs_on_utbm_adress']=='on'){
					$nmail = $_POST['utbm_mail'];
				}else{
					$nmail = $_POST['notif_mail'];
				}
				if($selected_dpt==1){
					$selected_major = 0;
				}
				if($selected_major=="none" OR $selected_major==""){
					$selected_major = 0;
				}
				$passhash = password_hash($_POST['passwd'], PASSWORD_DEFAULT);
				$insertuser->execute(array(
					"pseudo" => clean_input($_POST['pseudo']),
					"notif_mail" => clean_input($nmail),
					"passwd_hash" => clean_input($passhash),
					"department_ID" => clean_input($selected_dpt),
					"major_ID" => clean_input($selected_major),
					"promo" => clean_input($_POST['promo'])
				));
				$NewStudentID = $bdd->lastInsertId();
				$tokhash = md5( rand(0,1000));
				$ExpiryDate = new DateTime();
				$ExpiryDate->add(new DateInterval('P10D'));
				$insertToken = $bdd->prepare("INSERT INTO activation_tokens(student_ID, hash, expiry_date) VALUES (:newstudentid, :tokhash, :expdate)");
				$insertToken->execute(array(
					"newstudentid" => $NewStudentID,
					"tokhash" => $tokhash,
					"expdate" => $ExpiryDate->format("Y-m-d H:i:s")
				));
				$tokenid = $bdd->lastInsertId();
				$conf = parse_ini_file("../private/config.ini");
				$mailaddress = $conf['from'];
				
				//**** PROCEED TO ACCOUNT ACTIVATION EMAIL SENDING
				$to = $_POST['utbm_mail'];
				$subject = "Pyl-One | Activation de ton compte";
				$message = "<html><h1>Bienvenue sur Pyl-One !</h1></br><br>
				Ton compte a été créé avec succès avec le pseudo suivant : <b>".$_POST['pseudo']."</b><br><br>
				--------------------------<br>
				Pour activer ton compte, clique <a href=\"http://localhost/PylOne/public/verify?token=".$tokenid."&hash=".$tokhash."\"><b>ici</b></a> ou copie-colle le lien suivant dans ton navigateur : <br>
				http://localhost/PylOne/public/verify?token=".$tokenid."&hash=".$tokhash."<br>
				--------------------------<br>
				<br>
				<b>Attention, ce lien expirera dans 10 minutes !</b><br>
				<u>P.S. :</u> Si tu as décoché l'option \"<i>Je souhaite recevoir mes notifications sur cette adresse</i>\", ton adresse mail UTBM sera détruite une fois ton compte activé.</br></br>
				</html>";

				$headers = 'From:'. $mailaddress . "\r\n"; // Set from headers

				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

				mail($to, $subject, $message, $headers); // Send our email

				echo '<h2 class="success">Ton compte a bien été créé</h2>
				<p class="success">
				Il ne te reste plus qu\'à l\'activer dans les 10 prochaines minutes grâce au lien contenu dans le mail que tu viens de recevoir sur ta messagerie UTBM ! Si tu n\'as pas reçu le mail d\'activation, assure toi qu\'il n\'est pas dans un dossier d\'indésirables, et clique <a href="#">ici</a> pour en renvoyer un.</p>';	
			}
		}
		else{
			if(!empty($valid_err_msgs)){
				?>
				<?php
				echo "<h2 class=\"warn\">Ton inscription a échoué pour la/les raison(s) suivante(s) :</h2><ul class=\"warn\">";		
				foreach ($valid_err_msgs as $index => $message) {
					echo "<li>".$message."</li>";
				}
				echo "</ul>";
				echo "<h3 class=\"warn\"><u>Recommence ton inscription ci-dessous :</u></h3>";
			}
				?>
				<p><u>Attention :</u> Ce site est réservé aux étudiants de <a href="https://www.utbm.fr/" target="_blank">l'Université de Technologie de Belfort-Montbéliard</a>. Une vérification de l'adresse mail sera effectuée pour l'inscription. Tout compte qui n'appartiendrait pas à un étudiant de l'UTBM pourra être supprimé sans préavis.</p>
				<form id="signup" method="post" action="<?php echo htmlspecialchars(pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME)); ?>">
					<fieldset>
						<label><b>*Pseudo :</b>&nbsp
							<div style="display: inline;" data-tip="Ton pseudo doit contenir entre 5 et 25 lettres/chiffres">
								<input type="text" name="pseudo" pattern="[A-Za-z0-9]{5,25}" placeholder="Saisis ton pseudo ici" required>
							</div>
						</label>
						<p style="margin-left: 30px;" data-tip="Ton pseudo sera ta seule identité sur le site. Les autres membres du site pourront le voir, mais ils ne pourront pas voir tes notes."><u>Qui pourra voir mon pseudo ?</u></p>
						<p style="margin-left: 30px;" data-tip="Si tu souhaites rester anonyme vis-à-vis des autres étudiants et de l'administrateur, choisis un pseudo qui ne permette pas de t'identifier. Sinon, tu es libre de choisir n'importe quel pseudo (si tu en as un, n'hésite pas à utiliser ton surnom UTBM !)"><u>Comment choisir mon pseudo ?</u></p>
						<label data-tip="Ton mot de passe doit contenir entre 5 et 20 caractères dont au moins 1 chiffre, 1 minuscule et 1 majuscule"><b>*Mot de passe :</b>&nbsp<input type="password" id="passwd" name="passwd" pattern="^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\d]){1,})(?=(.*[\W]){0,})(?!.*\s).{5,20}$" placeholder="Saisis ton mot de passe ici" onchange="validatePassword();" required></label><br>
						<label><b>*Confirmation du mot de passe :</b>&nbsp<input type="password" id="passwd_conf" name="passwd_conf" placeholder="Confirme ton mot de passe ici" onkeyup="validatePassword();" required></label><br><br>
					</fieldset>

					<fieldset>
						<label><b>*Adresse e-mail UTBM :</b>&nbsp
							<input type="text" name="utbm_mail" pattern="^[a-z-]+\.[a-z-]+[0-9-]*@utbm\.fr$" placeholder="prenom.nom@utbm.fr" required>
						</label>
						<p style="margin-left: 30px;" data-tip="La seule information d'identité que tu dois saisir à l'inscription est ton adresse mail UTBM, dans l'unique but de t'envoyer un mail d'activation de ton compte qui permettra au site de vérifier que tu es bien étudiant.e à l'UTBM. Celle-ci ne sera pas stockée avec ton compte, sauf si tu souhaites recevoir tes notifications dessus. Ainsi, sois sûr que jamais personne, y compris l'administrateur de ce site, ne pourra associer tes notes à ta personne !"><u>Pourquoi me demander mon adresse mail ?</u></p>
						<label data-tip="Tu peux choisir de recevoir des notifications (nouveaux messages, nouvelles notes à saisir, fichiers ajoutés dans un de tes groupes...) directement sur ton adresse mail UTBM. Dans ce cas, coche simplement cette case, ainsi ton adresse mail UTBM sera associée à ton compte au lieu d'être détruite après activation. Tu pourras paramétrer la fréquence max des notifications une fois ton compte activé."><input type="checkbox" name="notifs_on_utbm_adress" id="notifs_on_utbm_adress" onclick="ShowMailField();" checked>Je souhaite recevoir mes notifications sur cette adresse</label><br><br>
						<label id="mail_field" data-tip="Si tu ne souhaites pas recevoir tes notifications par mail (déconseillé), laisse ce champ vide."><b>Adresse e-mail de notification :</b>&nbsp<input type="email" name="notif_mail"><br><br></label>
					</fieldset>

					<fieldset>
						<label><b>*Promo :</b>&nbsp	<!-- Restrict choice to the 7 last student promotions (a new promotion is created each month of September)-->
							<img id="promo_img" width="50px" height="50px" onerror="this.style.visibility = 'hidden'"/>
							<select name="promo" id="promo" onchange="change();" required>
								<option value='' selected>Sélectionne ta promo</option>
								<?php
								$year = date('Y', time());
								$month = date('m', time());
								$nb_prom_to_show = 6;
								$outgoing_prom = $year-$nb_prom_to_show+boolval($month>=2); 	//if we're after September, then we increase the outgoing promotion
								$outgoing_prom%=100;
								$last_prom = $outgoing_prom+$nb_prom_to_show;
								//echo $last_prom;
								for($i = $last_prom; $i>=$outgoing_prom; $i--){	
									?>
									<option style="background:url('promo_logos/logo.png') no-repeat; width:100px; height:100px;" value = <?php echo "\"".$i."\""?>><?php echo $i; ?></option>
									<?php
								}
								?>
							</select>
						</label>
						<br><br>
						<label><b>*Département :</b>&nbsp</label>
						<select name="department" id="department" required>
							<option value='' selected>Sélectionne ton département</option>
							<?php
							$req_dpt = $bdd->prepare("SELECT * FROM departments ORDER BY code ASC");
							$req_dpt->execute();
							while($dpts = $req_dpt->fetch()){
								?>
								<option value=<?php echo "\"".$dpts["ID"]."\""; ?>><?php echo $dpts['code']." | ".$dpts['name']; ?></option>
								<?php
							}
							?>
						</select><br>

						<label id="major_sel"><b>*Filière :</b>&nbsp
							<select name="major" id="major">
								<option value="">Sélectionne ta filière</option>
								<option value="none">Pas encore de filière</option>
							</select>
						</label>
						<p style="margin-left: 30px;" data-tip="Cela permettra au site de te proposer l'inscription aux UV qui te concernent en priorité. Toutefois, tu pourras bien t'inscrire à n'importe quelle UV."><u>Pourquoi dois-je donner ces informations ?</u></p>
					</fieldset>

					<fieldset>
						<label><input type="checkbox" name="accept_help" id="accept_help" onchange="enableSubBtn();">J'ai lu le <a href="" target="_blank">manuel d'utilisation de la plateforme</a></label><br>
						<label><input type="checkbox" name="accept_tos" id="accept_tos" onchange="enableSubBtn();">J'ai lu et j'accepte les <a href="" target="_blank">Conditions Générales d'Utilisation</a></label><br><br>
						<div class="g-recaptcha" data-sitekey="6LchxtUUAAAAABvmX5HmzZSsvTnBp0dwbNlycwh5"></div>
						<br/>
					</fieldset>
					<br><button type="submit" id="sub">Créer mon compte</button>

				</form>

				<p><i>Les champs marqués d'une * sont obligatoires.</i></p>
				<?php
			}
		}
	
	else{
		header("Location: main.php");
	}
	?>
	<footer><?php include("footer.php") ?></footer>
</body>
</html>