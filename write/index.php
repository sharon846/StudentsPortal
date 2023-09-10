<?php
include '../resources/header.php';
// Auth
$headers = "From: admin@is-haifa.com \r\n"; // Sender Email

if (isset($_POST["text"])) {
    
    $mail = $_POST['mail'] != "" ? $_POST['mail'] : date('Y-m-d-H-i-s');
    
    if (file_exists("uploads/$mail.txt")){
        ?> <script> window.alert(<?php echo "'already pending, please wait'"; ?>); window.location.href = "index.php";</script> <?php exit();
    }

    file_put_contents(getcwd()."/../admin/updates", "someone wrote us##https://SITE_URL/write/uploads/viewer.php".PHP_EOL, FILE_APPEND);
    foreach ($admin_mail as $mail2)
    {
        mail($mail2, "SITE_NAME Message", "Someone wrote to SITE_NAME site!", $headers);
    }

    $lines = array();
    array_push($lines, $mail.PHP_EOL);
    array_push($lines, $_POST['text'].PHP_EOL);
    
    file_put_contents("uploads/$mail.txt", $lines);
    
    ?> <script> window.alert(<?php echo "'success! we will contact soon'"; ?>); window.location.href = "https://SITE_DOMAIN";</script> <?php exit();
}

else {  
	// Form
	show_header();
?>
<div class="h-cover w-full text-center flex-1 flex flex-col items-center w-full min-w-full h-full">
	<div class="w-full p-8 m-4 md:max-w-md">
		<img src="../img/logoranker.png" alt="לוגו ועד הסטודנטים" class="round-big-logo">
		<h1 class="text-4xl my-6 text-primary">כתבו לנו</h1>
		<form onsubmit="return funct()" method="post">
		    <div class="mb-4 text-right">
				<label for="name">אימייל:</label>
				<input inputmode="email" id="mail" name="mail" type="text" placeholder="לא חובה, אך כדי שנוכל ליצור קשר" class="form-field" />
			</div>
			<div class="mb-4 text-right">
				<label for="text">תארו לנו את הבעיה:</label>
				<textarea id="text" name="text" class="form-field" placeholder="הטקסט שאנחנו נראה. ניתן להרחיב את התיבה."></textarea>
			</div>
			<div class="flex flex-col">
				<input id="login" type="submit" value="שליחה" class="button green-button mt-6" />
			</div>
			<br/>
			<p><b>שימו לב: </b>רק סטודנט יכול למלא טופס זה</p>
		</form>
	</div>
</div>

<script>
function funct()
{
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
