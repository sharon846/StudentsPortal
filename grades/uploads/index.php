<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST as $key => $value) {
        if (is_array($value)) {
            // If the value is an array, you can recursively apply htmlspecialchars
            $_POST[$key] = array_map(function($item) {
                return is_array($item) ? $item : htmlspecialchars($item, ENT_QUOTES);
            }, $value);
        } else {
            // If the value is a string, apply htmlspecialchars
            $_POST[$key] = htmlspecialchars($value, ENT_QUOTES);
        }
    }
}

function year_translate($year){
    $ar = explode(' ', jdtojewish(gregoriantojd(1, 1, $year), true, CAL_JEWISH_ADD_ALAFIM));
    return iconv ('WINDOWS-1255', 'UTF-8', end($ar));
}

if (count($_FILES) > 0 && isset($_POST['course'], $_POST['lecturer'], $_POST['year_sem'])){

$target_dir = "data/";

$new_name = 'DEPT_NAME_'.str_replace(' ', '_', $_POST['year_sem']).'_'.str_replace(' ', '_', $_POST['course']).'_'.str_replace(' ', '_', $_POST['lecturer']);

$target_file = $target_dir .$new_name. ".jpg";
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
$uploadOk = 1;

// Check if image file is a actual image or fake image

    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
       file_put_contents(getcwd()."/../../admin/updates", "grade uploaded in ".$_POST['course']."##https://SITE_URL/grades/uploads/data/viewer.php".PHP_EOL, FILE_APPEND);
        echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
      } else {
        echo "Sorry, there was an error uploading your file.";
      }
}
else {

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="css/normalize.css">
  <link rel="stylesheet" href="css/style.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
  <title>Form</title>
</head>
<body>
  <div class="wrapper">
    <div class="form">
      <form method="post" onsubmit="return validate();" action="index.php" id="form" enctype="multipart/form-data" class="form__body">
        <h1 class="form__title">טופס העלאת ציון</h1>

        <div class="form__item">
            <div class="form__label">שנה סמסטר ומועד*:</div>
            <select id="sel1" onchange="select(this)" name="year_sem" class="select">
                <option value="def">ברירת מחדל</option>
                
                <?php
                    $month = date('m');
                    $year = date('Y');
                    $semester = array("01" => "a", "02" => "a", "03" => "a", "04" => "a", "05" => "a", "06" => "b", "07" => "b", "08" => "b", "09" => "c", "10" => "c", "11" => "c", "12" => "c")[$month];

                    $my = $year;
                    $ms = ord($semester);
                    $count = 0;
                    
                    for ($y = $my; $count < 4; $y--)
                    {
                        for ($s = (($y != $my) ? 99 : $ms); $s >= 97 && $count < 4; $s--)
                        {
                            $he = array("א", "ב", "ג")[$s-97];
                            echo "<option value='".$y."_sem".chr($s)."_a'>".year_translate($y).", סמסטר ".$he.", מועד א</option>";
                            echo "<option value='".$y."_sem".chr($s)."_b'>".year_translate($y).", סמסטר ".$he.", מועד ב</option>";
                            echo "<option value='".$y."_sem".chr($s)."_c'>".year_translate($y).", סמסטר ".$he.", מועד ג</option>";
                            echo '<option value="def">----------------------------</option>';
                            $count++;
                        }
                    }
                
                ?>
            </select>
        </div>
        
        <div class="form__item">
               <div class="form__label">קורס*:</div>
               <select id="sel2" onchange="select(this)" name="course" class="select">
                  <option value="def">ברירת מחדל</option>
                  <?php
                     $courses = json_decode(file_get_contents("../../data/courses.json"), true)["Data"];
                     foreach ($courses as $course){
                         echo "<option value='$course'>$course</option>";
                     }
                     ?>
               </select>
               <div class="form__label">מרצה*:</div>
               <select id="sel3" onchange="select(this)" name="lecturer" class="select">
                  <option value="def">ברירת מחדל</option>
                  <?php
                     $lecturers = json_decode(file_get_contents("../../data/lecturers.json"), true)["Data"];
                     foreach ($lecturers as $lecturer){
                         echo "<option value='$lecturer'>$lecturer</option>";
                     }
                     ?>
               </select>
            </div>


<div class="form__item">
  <div class="form__label">היסטוגרמה (דוגמה למטה)*:</div>
  <div class="file">
    <div class="file__item">
      <input type="file" name="fileToUpload" id="formImage" class="file__input">
      <div onclick="upload()" class="file__button">העלאה</div>
    </div>
    <div id="formPreview" class="file__preview"></div>
        <img width="50%" height="50%" src="" hidden id="img" alt="data" />
  </div>
</div>
<!--accept=".jpg, .png, .gif, .jpeg, .bmp, .jfif" -->
<button type="submit" class="form__button">send</button>
<img src="example.png" style="position: relative;width: 50%;left: 25%;top: 2em;">
      </form>
    </div>
  </div>
</body>
<script>
    
    function upload(){
        document.getElementById("formImage").click();
    }
    
    function select(obj){
        if (obj.options[0].value == 'def' || obj.options[0].value == "ברירת מחדל")
            obj.remove(0);
    }
                    
    function validate(){
        if ($('#sel1 option:selected').val() == "def" || $('#sel2 option:selected').val() == "ברירת מחדל" || $('#sel3 option:selected').val() == "ברירת מחדל")
            return false;
            
        return $("#img").attr('src') != "";
    }
    
    function readURL(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
          $('#img').attr('src', e.target.result);
        }
        
        reader.readAsDataURL(input.files[0]); // convert to base64 string
      }
    }
    
    $("#formImage").change(function() {
      $("#img").show();
      readURL(this);
    });
    
    
</script>
</html>
<?php } ?>