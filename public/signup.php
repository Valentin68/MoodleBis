<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Créer un compte</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="style.css">
	<script src="https://www.google.com/recaptcha/api.js?hl=fr" async defer>
	</script>
	<script type="text/javascript">
		var passwd = document.getElementById("passwd");
		var passwd_conf = document.getElementById("passwd_conf");

		function validatePassword(){
			if(passwd.value != passwd_conf.value){
				passwd_conf.setCustomValidity("Les mots de passe saisis sont différents");
			}else{
				passwd_conf.setCustomValidity("");
			}
		}

		passwd.onchange = validatePassword;
		passwd_conf.onkeyup = validatePassword;

	</script>
</head>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"  ></script>
<script type="text/javascript">
	$(document).ready(function() {
		////////////
		$('#department').change(function(){
		//var st=$('#category option:selected').text();
		var dpt_id=$('#department').val();
		var mjr_sel = document.getElementById("major_sel");
		mjr_sel.style.display="inline-block";
		if(dpt_id.localeCompare("1")==0){
			mjr_sel.style.display="none";
		}
			$('#major').empty(); //remove all existing options
		///////
		$.get('populate_majors',{'dpt_id':dpt_id},function(return_data){
			$("#major").append("<option value=''>Sélectionne ta filière</option><option value='none'>Pas encore de filière</option>");
			if(return_data.data.length>0){
				//$('#msg').html( return_data.data.length + ' records Found');
				$.each(return_data.data, function(key,value){
					$("#major").append("<option value='" + value.ID +"'>"+value.code +" | "+value.name+"</option>");
				});
			}else{
			//$('#msg').html('No records Found');
		}	
	//}

}, "json");

		///////
	});
		/////////////////////
	});

	function change(){
		var e = document.getElementById("promo");
		var selectedPromo = e.options[e.selectedIndex].text;
		console.log(selectedPromo);
		var frame = document.getElementById("promo_img");
		frame.setAttribute("src","https://ae.utbm.fr/static/core/img/promo_"+selectedPromo+".png");
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
    // do something
    alert("Tu n'as pas complété le captcha !");
}
}

</script>

<body>
	<header><?php include("header.php") ?></header>
	<?php
	if(empty($_SESSION)){
		include("dbconnect.php");
		require_once('recaptchalib.php');
		$recap_key_file = parse_ini_file('../private/recaptcha_key.ini');
		$recap_key = $recap_key_file['key'];
		if(!empty($_POST)){
			if(isset($_POST['g-recaptcha-response'])){
				$captcha=$_POST['g-recaptcha-response'];
			}
			if(!$captcha){
				echo '<h2>Please check the the captcha form.</h2>';
				exit;
			}
			$secretKey = $recap_key;
			$ip = $_SERVER['REMOTE_ADDR'];
        // post request to server
			$url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) .  '&response=' . urlencode($captcha);
			$response = file_get_contents($url);
			$responseKeys = json_decode($response,true);
        // should return JSON with success as true
			if($responseKeys["success"]) {
				echo '<h2>Ton compte a bien été créé, il ne te reste plus qu\'à le valider grâce au mail que tu viens de recevoir sur ta boîte mail UTBM.</h2>';
			} else {
				echo '<h2>You are spammer ! Get the @$%K out</h2>';
			}
		}
		?>
		<h1>Créer un compte</h1>
		<p><u>Attention :</u> Ce site est réservé aux étudiants de <a href="https://www.utbm.fr/" target="_blank">l'Université de Technologie de Belfort-Montbéliard</a>. Une vérification de l'adresse mail sera effectuée pour l'inscription. Tout compte qui n'appartiendrait pas à un étudiant de l'UTBM pourra être supprimé sans préavis.</p>
		<form id="signup" method="post" action="<?php echo pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME); ?>">
			<?php
				//echo 
			?>
			<fieldset>
				<label><b>*Pseudo :</b>&nbsp
					<div style="display: inline;" data-tip="Ton pseudo doit contenir entre 5 et 25 lettres/chiffres">
						<input type="text" name="pseudo" pattern="[A-Za-z0-9]{5,25}" placeholder="Saisis ton pseudo ici" required>
					</div>
				</label>
				<p style="margin-left: 30px;" data-tip="Ton pseudo sera ta seule identité sur le site. Les autres membres du site pourront le voir, mais ils ne pourront pas voir tes notes."><u>Qui pourra voir mon pseudo ?</u></p>
				<p style="margin-left: 30px;" data-tip="Si tu souhaites rester anonyme vis-à-vis des autres étudiants et de l'administrateur, choisis un pseudo qui ne permette pas de t'identifier. Sinon, tu es libre de choisir n'importe quel pseudo (si tu en as un, n'hésite pas à utiliser ton surnom UTBM !)"><u>Comment choisir mon pseudo ?</u></p>
				<label data-tip="Ton mot de passe doit contenir entre 5 et 20 caractères dont au moins 1 chiffre, 1 minuscule et 1 majuscule"><b>*Mot de passe :</b>&nbsp<input type="password" id="passwd" name="passwd" pattern="^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\d]){1,})(?=(.*[\W]){0,})(?!.*\s).{5,20}$" placeholder="Saisis ton mot de passe ici" required></label><br>
				<label><b>*Confirmation du mot de passe :</b>&nbsp<input type="password" id="passwd_conf" name="passwd_conf" placeholder="Confirme ton mot de passe ici" required></label><br><br>
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
					<select name="promo" id="promo" onchange="change();" required>
						<option value='' selected>Sélectionne ta promo</option>
						<?php
						$year = date('Y', time());
						$month = date('m', time());
						$nb_prom_to_show = 6;
								$outgoing_prom = $year-$nb_prom_to_show+boolval($month>=2); 	//if we're after September, then we increase the outgoing promotion
								$outgoing_prom%=100;
								$last_prom = $outgoing_prom+$nb_prom_to_show;
								echo $last_prom;
								for($i = $last_prom; $i>=$outgoing_prom; $i--){	
									?>
									<option style="background:url('promo_logos/logo.png') no-repeat; width:100px; height:100px;" value = <?php echo "\"".$i."\""?>><?php echo $i; ?></option>
									<?php
								}
								?>
							</select>
						</label>
						<img id="promo_img" width="50px" height="50px" src="https://ae.utbm.fr/static/core/img/promo_20.png" alt="Logo Promo Introuvable" onerror="this.style.visibility = 'hidden'"/><br><br>
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
							<select name="major" id="major" required>
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
			else{
				header("Location: main.php");
			}

			?>
			<footer><?php include("footer.php") ?></footer>
		</body>
		</html>