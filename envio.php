<?php
/******************************/
/***						***/
/***	CONFIGURACIÓN		***/
/***						***/
/******************************/
$destinatario = 'AAA@outlook.es'; // dirección de destino del email
$usrSMTP = 'BBB@gmail.com'; // mail de la cuenta SMTP
$pasSMTP = 'CCC'; // contraseña de la cuenta SMTP
$sk = 'DDD'; // clave privada de Google Recaptcha

$nombre 		= $_POST['campo_nombre'];
$asunto 		= addslashes($_POST['campo_asunto']);
$email 			= $_POST['campo_email'];

/******************************/
/******************************/

$cuerpo = "<html><head></head><body><style>table {font-family:tahoma,sans-serif;} th {text-align:right;}</style>\n";
$cuerpo .= "<table>\n";
foreach($_POST as $nombre_campo => $valor){
	if ( is_array($valor) ) {
		$cuerpo .= "<tr><th>".$nombre_campo.": </th><td>".implode(",",$valor)."</td></tr>\n";
	} else {
		$cuerpo .= ($nombre_campo != 'g-recaptcha-response')? "<tr><th>".$nombre_campo.": </th><td>".$valor."</td></tr>\n" : "";
	}
}

$cuerpo .= "</table>\n";
$cuerpo .= "</body></html>\n";

$cuerpo2 = "Asunto: ".$asunto."\n";
foreach($_POST as $nombre_campo => $valor){
	if ( is_array($valor) ) {
		$cuerpo2 .= $nombre_campo.": ".implode(",",$valor)."\n";
	} else {
		$cuerpo2 .= ($nombre_campo != 'g-recaptcha-response')? $nombre_campo.": ".$valor."\n" : "";
	}
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// recaptcha
if(isset($_POST['g-recaptcha-response'])) {
	$captcha=$_POST['g-recaptcha-response'];

	//$urlRecaptcha = "https://www.google.com/recaptcha/api/siteverify?secret=".$sk."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR'];
	//$response=json_decode(file_get_contents($urlRecaptcha), true);
	
	$curl = curl_init();

	curl_setopt_array($curl, [
		CURLOPT_URL => "https://www.google.com/recaptcha/api/siteverify",
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => [
			'secret'   => $sk,
			'response' => $captcha,
			'remoteip' => $_SERVER['REMOTE_ADDR']
		],
		CURLOPT_RETURNTRANSFER => true
	]);
	
	$responseData = curl_exec($curl);
	curl_close($curl);

	$response = json_decode($responseData, true);

	if($response['success'] == true) {

		require('assets/PHPMailer/src/Exception.php');
		require('assets/PHPMailer/src/PHPMailer.php');
		require('assets/PHPMailer/src/SMTP.php');

		//Create an instance; passing `true` enables exceptions
		$mail = new PHPMailer(true);

		try {
			//Server settings
			$mail->isSMTP();                                            //Send using SMTP
			$mail->Host       = 'smtp.gmail.com';                   	//Set the SMTP server to send through
			$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
			$mail->Username   = $usrSMTP;                     			//SMTP username
			$mail->Password   = $pasSMTP;                              	//SMTP password
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;			//Enable implicit TLS encryption
			$mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
			$mail->SMTPDebug  = 0;                      				//Enable verbose debug output; set value to 2
			$mail->setLanguage('es', 'assets/PHPMailer/language/');

			//Recipients
			$mail->setFrom($usrSMTP);
			$mail->addAddress($destinatario);     					//Add a recipient
			$mail->addReplyTo($email, $nombre);

			// Attachment
			foreach($_FILES as $nombre_campo => $valor){
				if ($valor['error'] == UPLOAD_ERR_OK && is_uploaded_file($valor['tmp_name'])) {
					$mail->addAttachment($valor['tmp_name'], $valor['name']);
				}
			}

			//Content
			$mail->isHTML(true);                                  //Set email format to HTML
			$mail->Subject = $asunto;
			$mail->Body    = $cuerpo;
			$mail->AltBody = $cuerpo2;
			$mail->CharSet = "UTF-8";

			$mail->send();
			echo "ok";
		} catch (Exception $e) {
			echo "error: {$mail->ErrorInfo}";
		}

	} else { // recaptcha
		echo "error: recaptcha 2";
	}
} else { // recaptcha
	echo "error: recaptcha 1";
}

?>