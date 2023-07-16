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

	$sql = "UPDATE `Tusers` SET `sendmail`=0 WHERE `email`='$mail'";
    $result = $conn->query($sql)->rowCount();
    $result = $result == 1 ? "successfully unsubscribed" : "you are not listed!";
    
    ?> <script> window.alert(<?php echo "'$result'"; ?>); window.location.href = "http://DOMAIN";</script> <?php exit(); 
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
			<div class="flex flex-col">
				<input id="login" type="submit" value="הסרה" class="button blue-button mt-6" />
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