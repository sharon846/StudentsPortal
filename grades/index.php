<?php

$server_root = "SITE_PATH/";
$curr_dir = getcwd();
$representive_dir = str_replace($server_root, "", $curr_dir);
$representive_url = $_SERVER['REQUEST_SCHEME'].'://SITE_DOMAIN/'.$representive_dir;

@set_time_limit(3600);
session_name("SITE_SESSION_NAME");
session_start();

if (!isset($_SESSION["SITE_SESSION_NAME"])){
    header("Location: https://SITE_DOMAIN/login/index.php?referer=$representive_url/");
    exit();
}



?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="UTF-8">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<link rel="stylesheet" href="select.css">
<link rel="stylesheet" href="../header.css">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<script type="text/javascript" src="script.js"></script>
<script src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<style>

h5{
    float: right;
    margin-top: -0rem;
}

div.select{
    position: relative;
}

h2.title{
    right: 5%;
    position: relative;
    margin-top: 5em;
    top: 1em;
}

div.charts{
    margin-top: 6em;
    position: absolute;
    right: 5%;
    width: 60%;
}

@media (max-width: 600px) { 
    div.dropdown-container{
        width: 300px;
    }
}

/* here the rules for windows between 500px and 900px */
@media (min-width: 601px) {

    div.dropdown-container{
        width: 350px;
    }
        
    canvas{
        top: -25px;
    }
}

</style>

</head>
<body>

<header id="header">
    <div style="width: 100%; height: 100%">
        <div style="width: 100%; height: 62%; top: 0%; position: absolute;">
            <a class="hover" href="uploads/" title="Directory Lister" style="position: absolute; top: 20%; left: 6%; width: 50px; height:30px;">
                <img width="100%" src="../img/upload.png"/>
            </a>
        
            <form id='frm' method="POST" onsubmit="return false">
                <span class='ad'><?php echo file_get_contents("../data/ad"); ?></span>
            </form>
            
            <div id="mode">
               <div class="transform" style="transition-property: all;transition-duration: .3s;transition-timing-function: cubic-bezier(.4,0,.2,1);background-color: white;border-radius: 9999px;width: 1.25rem; height: 1.25rem;margin-left: -0.08rem;">
                    <i id="lamp" class="fa fa-lightbulb-o" style="margin-left: 7px; font-size: .75em;"></i>
                </div>
            </div>
        </div>
        <div style="position: absolute;left: 4.8%;height: 60%;color: white;top: 45%;">
            <h4 class="dl hover" title="Info" style="font-size: 1.1rem; line-height: 1.25rem;">
                העלאת ציונים
            </h4>
        </div>
        <div style="position: absolute;right: 4.8%;height: 60%;color: white;top: 45%;">
            <h4 class="dl hover" title="Info" style="font-size: 1.1rem; line-height: 1.25rem;">
                DEPT_NAME
            </h4>
        </div>
    </div>
</header>

<center style="top: 100px;position: relative">
    <h3 id='statics'>באתר הוזנו עד כה 33 קורסים ו 33 מועדים</h3>
</center>

<?php


$courses = simplexml_load_file("../data/courses.xml");
$lectures = simplexml_load_file("../data/lectures.xml");

if (!isset($_POST['data'])){
    $data = (string)$courses->Course[0];
}
else $data = $_POST['data'];

?>

<h2 class='title'>סנן לפי שם מרצה:</h2>

    <div class="dropdown-container"><form action="index.php" method="post">
      <select id="select-by-name" name="data" onchange="this.form.submit()">
        <?php
           
            foreach ($lectures as $lecture) { echo "<option value='".$lecture."'";
            if ($data == $lecture) echo " selected"; echo ">" .$lecture."</option>"; }
        ?>
      </select>
      <div class="select-icon">
        <svg focusable="false" viewBox="0 0 104 128" width="17" height="35" class="icon">
          <path d="m2e1 95a9 9 0 0 1 -9 9 9 9 0 0 1 -9 -9 9 9 0 0 1 9 -9 9 9 0 0 1 9 9zm0-3e1a9 9 0 0 1 -9 9 9 9 0 0 1 -9 -9 9 9 0 0 1 9 -9 9 9 0 0 1 9 9zm0-3e1a9 9 0 0 1 -9 9 9 9 0 0 1 -9 -9 9 9 0 0 1 9 -9 9 9 0 0 1 9 9zm14 55h68v1e1h-68zm0-3e1h68v1e1h-68zm0-3e1h68v1e1h-68z"></path>
        </svg>
      </div>
    </form></div>

<h2 class='title'>סנן לפי קורס</h2>

    <div class="dropdown-container"><form action="index.php" method="post">
      <select id="select-by-name" name="data" onchange="this.form.submit()">
        <?php
            
            foreach ($courses as $course) { echo "<option value='".$course."'";
            if ($data == $course) echo " selected"; echo ">" .$course."</option>"; }
        ?>
      </select>
      <div class="select-icon">
        <svg focusable="false" viewBox="0 0 104 128" width="17" height="35" class="icon">
          <path d="m2e1 95a9 9 0 0 1 -9 9 9 9 0 0 1 -9 -9 9 9 0 0 1 9 -9 9 9 0 0 1 9 9zm0-3e1a9 9 0 0 1 -9 9 9 9 0 0 1 -9 -9 9 9 0 0 1 9 -9 9 9 0 0 1 9 9zm0-3e1a9 9 0 0 1 -9 9 9 9 0 0 1 -9 -9 9 9 0 0 1 9 -9 9 9 0 0 1 9 9zm14 55h68v1e1h-68zm0-3e1h68v1e1h-68zm0-3e1h68v1e1h-68z"></path>
        </svg>
      </div>
    </form></div>

    


<div class="charts">
<?php

$arrRanks = ["'a'" => "א", "'b'" => "ב", "'c'" => "ג"];
require_once '../site_manager/pdoconfig.php';
 
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
} catch (PDOException $pe) {
    die("Could not connect to the database $dbname :" . $pe->getMessage());
}

$sql = "SELECT * FROM TgradesSemesters,`Tgrades` WHERE Tgrades.`idsemester` IN (SELECT id from TgradesSemesters WHERE lecture='data' OR name='data') AND TgradesSemesters.id=Tgrades.`idsemester` ORDER BY TgradesSemesters.year DESC, TgradesSemesters.semester DESC, id, Tgrades.moed ASC";
$sql = str_replace('data', $data, $sql);
$result = $conn->query($sql);
$rows = $result->fetchAll();

$year = "";
$id = "";

foreach ($rows as $row)
{
    if (($row['year'] != $year && $year != "") ||
        ($row['id'] != $id && $id != ""))           //finish semester
        echo "<br /></div><br />";
    
    
    if ($row['year'] != $year)     //finish year and start new one
    {
        $year = $row['year'];
        echo "<h1>".$row['year'].":</h1>";
    }
    
    if ($row['id'] != $id)     //start semester
    {
        $id = $row['id'];
        
        echo "<div class='row'><div class='row'>";
        echo "<h2 style='padding-top:30px'>Semester ".$row["semester"]."</h2>";
        echo "<h4 dir='rtl'>מרצה: ".$row["lecture"].", קורס: ".$row["name"]."</h4></div>";
    }
    
    if (isset($row["proj"]))
    {
        echo "<div class='row'><h3>";
        echo $row["proj"].", ";
        echo "ציון סופי</h3>";
        echo "<h5 dir='rtl'>ממוצע: ".$row['avg'].", מס סטודנטים: ".$row['num']."</h5>";
    }
            
    else
    {
        echo "<div class='row'><h3>מועד ".$arrRanks["'".$row["moed"]."'"];
        if (isset($row["proj2"])) echo ", ".$row["proj2"];
        echo "</h3>";
        echo "<h5 dir='rtl'>ממוצע: ".$row['avg'].", נבחנו: ".$row['num']."</h5>";
    }
            
    $id11 = $row["lecture"]."-".$row["name"]."-".$row["moed"]."-".$row["semester"]."-".$row["year"];
                

    $arrGrades = str_replace(',', '+', $row["grades"]);
    echo "<canvas width='300px' height='36px' style='float: right; padding-right: 40px; position: relative; right:0%; top:0px' id='$id11#$arrGrades'></canvas>";
    echo "</div>";
}
echo "<br /></div><br />";
?>
</div>
<script>

window.onload = function(){
        
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)
    		    switchMode();
    		    
        $("div#mode").click(switchMode);
        
        var arr = document.getElementsByTagName('canvas');
        for (elem of arr) { creatAndDrawGrade(elem.id); }
        for (elem of arr) { removeId(elem); }
    }
    
    function removeId(elem)
    {
        elem.id = "";
    }


    function creatAndDrawGrade(data)
    {
        var canvasId = data;
        var arrGrades = data.split("#")[1].split("+");
        
        var canvas = document.getElementById(canvasId);
        var context = canvas.getContext("2d");
        var imageObj = new Image();
        imageObj.onload = function(){
            context.drawImage(imageObj, 0, 0);
            context.font = "10pt David";
            
            for (var i = 0; i < arrGrades.length; i++)
                context.fillText(arrGrades[i], 13 + 30 * i, 30);
        };
        imageObj.src = "table.png"; 
    }
    
    function switchMode()
	{
	    if ($("body").hasClass("dark")){
	        $("body").attr('class', 'light');
	    }
	    
	    else{
	        $("body").attr('class', 'dark');
	    }
	}
    	
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => { 
    	
        if (event.matches)
    	   switchMode();
    });
    
</script>


</body>
</html>


