<?php
include 'header.php';

// Auth



if (isset($_POST['courses'], $_POST['user'], $_POST['text'], $_POST['phone'], $_FILES["cv"])) {
    
    if (file_exists("uploads/".$_POST['user'].".txt")){
        ?> <script> window.alert(<?php echo "'already pending, please wait'"; ?>); window.location.href = "index.php";</script> <?php exit();
    }
    
    $target_file = "uploads/".$_POST['user'].".pdf";
    $success = move_uploaded_file($_FILES["cv"]["tmp_name"], $target_file);
    
    if ($success){
        file_put_contents(getcwd()."/../admin/updates", $_POST['user']." is new teacher##https://SITE_URL/teachers/uploads/viewer.php".PHP_EOL, FILE_APPEND);
        
        $lines = array();
        array_push($lines, $_POST['user'].PHP_EOL);
        array_push($lines, $_POST['phone'].PHP_EOL);
        array_push($lines, $_POST['courses'].PHP_EOL);
        array_push($lines, $_POST['text'].PHP_EOL);
    
        file_put_contents("uploads/".$_POST['user'].".txt", $lines);
        
        ?> <script> window.alert(<?php echo "'success!'"; ?>); window.location.href = "index.php";</script> <?php exit();
    } else {
        ?> <script> window.alert(<?php echo "'failed'"; ?>); window.location.href = "index.php";</script> <?php exit();
    }
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
				<input inputmode="email" id="email" name="user" type="email" placeholder="האימייל שאיתו אתם מתחברים לאתר" class="form-field" />
			</div>
			<div class="mb-4 text-right">
				<label for="phone">טלפון:</label>
				<input inputmode="tel" id="phone" name="phone" type="tel" placeholder="מספר הטלפון, כדי שיוכלו לכתוב לכם" class="form-field" />
			</div>
			<div class="mb-4 text-right">
				<label for="courses">קורסים:</label>
				<input inputmode="text" id="courses" name="courses" type="text" placeholder="הקורסים אותם תרצו ללמד" class="form-field" />
			</div>
			<div class="mb-4 text-right">
				<label for="text">תוכן:</label>
				<textarea id="text" name="text" class="form-field">הטקסט שיוצג לסטודנטים עליך</textarea>
			</div>
			<div class="mb-4 text-right">
				<label for="cv">העלה גליון ציונים:</label>
				<input accept="application/pdf" type="file" id="cv" name="cv" class="form-field" />
			</div>
			<div class="flex flex-col">
				<input id="login" type="submit" value="הרשמה" class="button green-button mt-6" />
			</div>
			<br/>
			<p><b>שימו לב: </b>רק סטודנט יכול למלא טופס זה</p>
		</form>
	</div>
</div>

<script>
function funct()
{
    var year =  $("input#courses").val();
    if (year == "") return false;
    
    year =  $("input#phone").val();
    if (year == "") return false;
    
    year =  $("input#text").val();
    if (year == "") return false;
    
    year =  $("input#courses").val();
    if (year == "") return false;
    
    var email = $("input#email").val();
    if (email == "") return false;

    var result = $("input#cv").val();
    if (result == "") return false;
    
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
