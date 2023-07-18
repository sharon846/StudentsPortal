<?php
include '../resources/header.php';
include 'validate_pdf.php';
require_once '../site_manager/pdoconfig.php';

$show_msg = false;
$message = "";
$success = false;

if (isset($_POST['user'], $_POST['name'], $_FILES["cv"])) {
    
    do 
    {
        $mail = $_POST['user'];
        $name = $_POST['name'];
        
        $pdf_data = detect($_FILES['cv']['tmp_name']);
    
        if (is_numeric($pdf_data)) {
            $show_msg = true;
            $name = explode(' ', $name)[0];
            $success = true;
            $message = "היי $name, שמנו לב שאינך מהחוג למערכות מידע. <br/>מעבר לעובדה שהאתר מיועד רק למי שלומד מערכות מידע, אין בו שום תוכן רלוונטי לחוגים אחרים (גם קורסים עם שמות דומים, שונים לגמרי). בהצלחה!";
            $message .= "<br/>לחזרה לעמוד הראשי לחצו <a style='color: blue' href='https://DOMAIN'>כאן</a>";
            break;
        }
        
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $show_msg = true;
            $message = "המייל שהוזן אינו תקין";
            break;
        }
     
        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        } catch (PDOException $pe) {
            $show_msg = true;
            $message = "Could not connect to the database $dbname";
            break;
        }
    
    	$sql = "SELECT * FROM `Tusers` WHERE `email`='$mail'";
        $result = $conn->query($sql);
    
        if ($result->rowCount() == 0)
        {
            $deg = $pdf_data[0];
            $year = $pdf_data[1];
            
            $sql = "INSERT IGNORE INTO `Tusers`(`email`, `name`, `degree`, `year`) VALUES ('$mail', '$name', '$deg', '$year');";
            $res = $conn->query($sql);
            
            $show_msg = true;
            $message = "Your registration was approved!<br/>Login to our site and than choose permanent password. Good Luck!";
            
            if ($deg == "2")
                $message .= "<br/>Special addition for MSC";
               
            file_put_contents(getcwd()."/../admin/updates", "$mail registered".PHP_EOL, FILE_APPEND);
            $message .= "<br/>לחזרה לדף ההתחברות לחצו <a style='color: blue' href='index.php'>כאן</a>";
            $success = true;
        } 
        
        else 
        {
            $show_msg = true;
            $message = "You already registered!";
            $success = true;
            $message .= "<br/>לחזרה לדף ההתחברות לחצו <a style='color: blue' href='index.php'>כאן</a>";
        }
    } while (0);
}
 
// Form
show_header();
?>
<div class="h-cover w-full text-center flex-1 flex flex-col items-center w-full min-w-full h-full">
	<div class="w-full p-8 m-4 md:max-w-md">
		<img src="../img/logoranker.png" alt="לוגו ועד הסטודנטים" class="round-big-logo">
		<h1 class="text-4xl my-6 text-primary"></h1>
		
		<form onsubmit="return funct()" method="post" enctype="multipart/form-data">
		    <?php if (!$success): ?>
			<div class="mb-4 text-right">
				<label for="email">אימייל:</label>
				<input inputmode="email" id="email" name="user" type="email" placeholder="האמייל האישי אליו אתם מקבלים הודעות" class="form-field">
			</div>
			<div class="mb-4 text-right">
				<label for="name">שם מלא:</label>
				<input inputmode="name" id="name" name="name" type="text" placeholder="שם מלא" class="form-field">
			</div>
			<div class="mb-4 text-right">
				<label for="cv">העלה אישור לימודים / מערכת מהפורטל:</label>
				<input accept="application/pdf" type="file" id="cv" name="cv" class="form-field" />
			</div>
			<div class="flex flex-col">
				<input id="login" type="submit" value="הרשמה" class="button green-button mt-6" />
			</div>
			<br/>
			<?php endif; 
			if ($show_msg) {
    		    echo "<p style='color: darkblue'>$message</p>";
    		} else { ?>
                <p><b>שימו לב: </b>ההרשמה תאושר רק לאחר שמנהל יוודא שאתם סטודנטים אמיתיים מהחוג</p>
                <p>שימו לב: ההרשמה אך ורק לתלמידים שלוקחים קורסים בחוג למערכות מידע</p>
            <?php } ?>
		</form>
	</div>
</div>

<script>
function funct()
{
    var email = $("input#email").val();
    if (email == "") return false;
    emaill = email.toLowerCase();
    
    var result = $("input#name").val();
    if (result == "") return false;
    
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
?>
