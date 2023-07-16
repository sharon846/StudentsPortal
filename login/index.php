<?php
include '../resources/header.php';
require_once '../site_manager/pdoconfig.php';

if (isset($_POST['user'], $_POST['password'])) {
    
    $mail = $_POST['user'];

	if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        ?> <script> window.alert(<?php echo "'email is not valid'"; ?>); window.location.href = "index.php";</script> <?php exit(); }
	
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    } catch (PDOException $pe) {
        ?> <script> window.alert(<?php echo "'Could not connect to the database $dbname'" ?>); window.location.href = "index.php";</script> <?php exit();
    }

	$sql = "SELECT `name`,`degree`,`year`,`msg`,`password` FROM `Tusers` WHERE `email`='$mail'";

    $result = $conn->query($sql);

    if ($result->rowCount() == 0){
        ?> <script> window.alert(<?php echo "'user does not exists'"; ?>); window.location.href = "index.php";</script> <?php exit();
    } else {
        $arr = $result->fetchAll();
    
        $degree = $arr[0]["degree"];
        $msg = $arr[0]["msg"];
        $password1 = $arr[0]["password"];
        $name = $arr[0]["name"];
        $year = $arr[0]["year"];

        if ($password1 == NULL)
        {
            //creating password
            
            $ciphering = "AES-128-CTR";
  
            // Use OpenSSl Encryption method
            $iv_length = openssl_cipher_iv_length($ciphering);
            $options = 0;
              
            // Non-NULL Initialization Vector for encryption
            $encryption_iv = '1234567891011121';
              
            // Store the encryption key
            $encryption_key = "CSPlus209";
             
            $simple_string = "$mail##$name##$degree";
            
            // Use openssl_encrypt() function to encrypt the data
            $encryption = openssl_encrypt($simple_string, $ciphering,
                        $encryption_key, $options, $encryption_iv);
            
            $encryption = base64_encode($encryption);
            
            header("Location: http://DOMAIN/login/password.php?data=$encryption");
            exit();
        } 
        else if ($password1 != md5($_POST['password'])){
        ?> <script> window.alert(<?php echo "'wrong mail or password'"; ?>); window.location.href = "index.php";</script> <?php exit();
        }
        
        $date = new DateTime(null, new DateTimeZone('Asia/Jerusalem'));
        $date = $date->format('Y-m-d H:i:s');
        
        $sql = "UPDATE `Tusers` SET `connected`='$date' WHERE `email`='$mail'";
        $conn->query($sql);

        @set_time_limit(3600);
	    session_name("SESSION_NAME");
        session_start();
    
	    $_SESSION["SESSION_NAME"]['deg'] = $degree;
	    $_SESSION["SESSION_NAME"]['mail'] = $mail;
	    
	    if (isset($_GET['referer']))
	        $location = $_GET['referer'];
	    else    
	        $location = "../site/cs/";
	    
	    if ($msg != "")
	    {
	        ?> <script> window.alert(<?php echo "'".$msg."'"; ?>); window.location.href = <?php echo "'$location'"; ?> </script> <?php 
	    }
	    else
	    {
	        ?> <script> window.location.href = <?php echo "'$location'"; ?> </script> <?php
	    }
    }
}

else {  
	// Form
	show_header();
?>
<div class="h-cover w-full text-center flex-1 flex flex-col items-center w-full min-w-full h-full">
	<div class="w-full p-8 m-4 md:max-w-md">
		<img src="../img/logoranker.png" alt="לוגו ועד הסטודנטים" class="round-big-logo">
		<h1 class="text-4xl my-6 text-primary">כניסה לאתר</h1>
		<form onsubmit="return funct()" method="post" autocomplete="on">
			<div class="mb-4 text-right">
				<label for="email">אימייל:</label>
				<input inputmode="email" id="email" name="user" type="email" placeholder="האמייל האישי אליו אתם מקבלים הודעות" class="form-field">
			</div>
			<div class="mb-4 text-right">
				<label for="password">ססמה:</label>
				<input inputmode="text" id="password" name="password" type="password" title="password" placeholder="ססמה" class="form-field">
			</div>
			<p><b>שימו לב: </b>ססמתכם מוצפנת ומאובטחת</p>
			<p><b>שימו לב: </b>אם אין לכם ססמה - השאירו ריק</p>
			<div class="flex flex-col">
				<input id="login" type="submit" value="התחבר" class="button blue-button mt-6" />
				<input type="button" onclick="trouble_click()" value="הרשמה" class="button green-button mt-6" />
				<input type="button" onclick="forgot_click()" value="שכחתי ססמה" class="button red-button mt-6" />
			</div>

		</form>
	</div>
</div>

<script>
function funct()
{
    var result = $("input#email").val();
    return validateEmail(result);
}

function trouble_click()
{
    window.location.href="http://DOMAIN/login/register.php";
    return false;
}

function forgot_click()
{
    window.location.href="http://DOMAIN/login/forgot.php?mail=" + $("input#email").val();
    return false;
}

function validateEmail(email) {
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

</script>

<?php

show_footer();
exit();
}
?>
