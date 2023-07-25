<?php
include '../resources/header.php';
require_once '../site_manager/pdoconfig.php';

if (!isset($_GET['data'])){
    exit();
}

$dt = base64_decode($_GET['data']);

// Auth
$decryption_iv = '1234567891011121';
  
// Store the decryption key
$decryption_key = "SITE_ENC_KEY";
  
$ciphering = "AES-128-CTR";
  
// Use OpenSSl Encryption method
$iv_length = openssl_cipher_iv_length($ciphering);
$options = 0;
  
// Use openssl_decrypt() function to decrypt the data
$decryption=openssl_decrypt ($dt, $ciphering, 
        $decryption_key, $options, $decryption_iv);

if ($decryption === false) {
        ?> <script> window.alert(<?php echo "'damaged data'"; ?>); window.location.href = "index.php";</script> <?php exit(); }
    
$mail = explode('##', $decryption)[0];
$name = explode('##', $decryption)[1];
$degree = explode('##', $decryption)[2];

if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        ?> <script> window.alert(<?php echo "'email is not valid'"; ?>); window.location.href = "index.php";</script> <?php exit(); }


if (isset($_POST['password']) && $_POST['password'] != NULL && $_POST['password'] != ""){
    
    $fake_password = $_POST['password'];
    $password1 = md5($_POST['password']);
    
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    } catch (PDOException $pe) {
        ?> <script> window.alert(<?php echo "'Could not connect to the database $dbname'" ?>); history.back();</script> <?php exit();
    }

	$date = new DateTime(null, new DateTimeZone('Asia/Jerusalem'));
    $date = $date->format('Y-m-d H:i:s');
	
	$sql = "UPDATE `Tusers` SET `connected`='$date',`password`='$password1' WHERE `email`='$mail' AND `name`='$name'";

    $result = $conn->query($sql);

    if (!$result){
        ?> <script> window.alert(<?php echo "'user does not exists'"; ?>); history.back();</script> <?php exit();
    } else {
        @set_time_limit(3600);
	    session_name("SITE_SESSION_NAME");
        session_start();
    
	    $_SESSION["SITE_SESSION_NAME"]['deg'] = $degree;
	    $_SESSION["SITE_SESSION_NAME"]['mail'] = $mail;
	    
	    $location = "../site/files/";
	    
	    ?> <script> window.location.href = <?php echo "'$location'"; ?> </script> <?php exit();
    }
    
    exit();
} 
	
$link = "password.php?data=".$_GET['data'];
// Form
show_header();
?>
<div class="h-cover w-full text-center flex-1 flex flex-col items-center w-full min-w-full h-full">
	<div class="w-full p-8 m-4 md:max-w-md">
		<img src="../img/logoranker.png" alt="לוגו ועד הסטודנטים" class="round-big-logo">
		<h1 class="text-4xl my-6 text-primary"></h1>
		<form method="post" action=<?php echo "'$link'"; ?> autocomplete="on">
			<div class="mb-4 text-right">
				<label for="email">אימייל:</label>
				<input inputmode="email" disabled id="email" value=<?php echo "'$mail'"; ?> name="email" type="email" placeholder="האמייל האישי אליו אתם מקבלים הודעות" class="form-field">
			</div>
			<div class="mb-4 text-right">
				<label for="name">שם מלא:</label>
				<input inputmode="name" disabled id="name" name="name" type="text" value=<?php echo "'$name'"; ?> placeholder="שם מלא" class="form-field">
			</div>
			<div class="mb-4 text-right">
				<label for="password">ססמה:</label>
				<input id="password" name="password" type="password" title="password" placeholder="הזינו ססמה קבועה" class="form-field">
			</div>
			<div class="flex flex-col">
				<input id="login" type="submit" value="התחברות" class="button green-button mt-6" />
			</div>
			<br/>
			<p><b>שימו לב: </b>הססמה מאובטחת. מטרתה של הססמה היא להגן על הפרטיות של משתמשי האתר במידע שהם משתפים, למנוע ניצול לרעה על ידי אנשים אחרים או מנהלי האתר, כמו גם להבטיח שאכן רק סטודנטים מאונ חיפה ייכנסו, בדומה לטכניון
		</form>
	</div>
</div>

<?php

show_footer();
exit();
?>
