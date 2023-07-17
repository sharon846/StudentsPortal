<?php
include '../resources/header.php';
require_once '../site_manager/pdoconfig.php';

if (isset($_POST['user'])) {

    $mail = $_POST['user'];

    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        ?> <script> window.alert(<?php echo "'email is not valid'"; ?>); window.location.href = "index.php";</script> <?php exit(); }
	
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    } catch (PDOException $pe) {
        ?> <script> window.alert(<?php echo "'Could not connect to the database $dbname'" ?>); window.location.href = "index.php";</script> <?php exit();
    }

	$sql = "SELECT `name`,`degree`,`password` FROM `Tusers` WHERE `email`='$mail'";

    $result = $conn->query($sql);

    if ($result->rowCount() == 0){
        ?> <script> window.alert(<?php echo "'user does not exists'"; ?>); window.location.href = "index.php";</script> <?php exit();
    } else {
        $arr = $result->fetchAll();
    
        $degree = $arr[0]["degree"];
        $password1 = $arr[0]["password"];
        $name = $arr[0]["name"];

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
        
        $name = explode(' ', $name)[0];
        $encryption = base64_encode($encryption);
        $link = "https://DOMAIN/login/password.php?data=$encryption";
        $headers = "From: SESSION_NAME admin <admin@DOMAIN>\r\n";
        $headers .= "MIME-Version: 1.0" . "\r\n"; 
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n"; 
        $text = "היי $name,<br/>הקישור שלך לאיפוס הססמה באתר הסטודנטים של מערכות מידע הוא: <a href='$link'>זה שכאן</a><br/><br/>הקישור הוא אישי, <b>אל תעבירו אותו לאיש</b>.";

        $res = mail($mail, "Password recover link", $text, $headers);
        
        if ($res)
        {
            ?> <script> window.alert(<?php echo "'success! Check inbox and spam'"; ?>); window.location.href = "index.php";</script> <?php exit();
        }
        else
        {
            ?> <script> window.alert(<?php echo "'error occurred, check again later'"; ?>); window.location.href = "index.php";</script> <?php exit();
        }
        exit();
        
    } 
    exit();
    /*
    
    if (file_exists("pending/".$_POST['user'].".pdf")){
        ?> <script> window.alert(<?php echo "'already pending, please wait'"; ?>); history.back();</script> <?php exit();
    }
    
    $target_file = "pending/".$_POST['user'].".pdf";
    $success = move_uploaded_file($_FILES["cv"]["tmp_name"], $target_file);
    
    if ($success){
        file_put_contents(getcwd()."/../admin/updates", $_POST['user']." forgot password##https://DOMAIN/login/pending/viewer.php".PHP_EOL, FILE_APPEND);
        
        ?> <script> window.alert(<?php echo "'done, we will contact soon'"; ?>); window.location.href = "https://DOMAIN";</script> <?php exit();
    } else {
        ?> <script> window.alert(<?php echo "'failed, try again later'"; ?>); window.location.href = "https://DOMAIN";</script> <?php exit();
    }*/
}

else {  
	// Form
	show_header();
?>
<div class="h-cover w-full text-center flex-1 flex flex-col items-center w-full min-w-full h-full">
	<div class="w-full p-8 m-4 md:max-w-md">
		<img src="../img/logoranker.png" alt="לוגו ועד הסטודנטים" class="round-big-logo">
		<h1 class="text-4xl my-6 text-primary"></h1>
		<form onsubmit="return funct()" method="post" enctype="multipart/form-data">
			<div class="mb-4 text-right">
				<label for="email">אימייל:</label>
				<input inputmode="email" id="email" name="user" type="email" placeholder="האמייל שאיתו התחברתם לאתר" class="form-field">
			</div>
			<div class="flex flex-col">
				<input id="login" type="submit" value="איפוס" class="button blue-button mt-6" />
			</div>
		</form>
	</div>
</div>

<script>

window.onload = function() {
    $("input#email").val(new URLSearchParams(window.location.search).get('mail'));
}

function funct()
{
    var email = $("input#email").val();
    if (email == "") return false;
    emaill = email.toLowerCase();
    
    var file1 = $("input#cv").val();
    if (file1 == "") return false;
    
    return validateEmail(email);
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
