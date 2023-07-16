<?php

require_once '../data/semester_dates.php';

if (!isset($_POST['sem'],$_POST['hidden-input']))
    exit();

$sem = $_POST['sem'];
$sem_idx = ord($_POST['sem']) - 97;

$list = explode(",", $_POST['hidden-input']);
$list = array_filter($list);

$semester_end = $semester_end[$sem_idx];
$semester_next_start = $semester_start[$sem_idx+1];
$exam_end_date = $exams_end[$sem_idx];

$arr = array();

$data = file_get_contents("$sem.html");
$ind = strpos($data, "<table");
$data = substr($data, $ind);
$ind = strpos($data, "]]>");
$data = substr($data, 0, $ind);

$doc = new DOMDocument();
@$doc->loadHTML($data);

//Image tag
$rows = $doc->getElementById("aaaa.OutputExamsView.TableGui-contentTBody");

$prv = "";
$keys = array();

$period = new DatePeriod(
     (new DateTime($semester_end))->modify('-1 week'),
     new DateInterval('P1D'),
     new DateTime($exam_end_date)
);

foreach ($period as $key => $value) {
    $dt = $value->format('Y-m-d'); 
    $arr[$dt] = array();
}

array_push($arr[$semester_next_start], array("תחילת סמסטר הבא", 3));
array_push($arr[$semester_end], array("סוף סמסטר", 3));

for ($j = 0; $j < $rows->childNodes->length-1; $j++) {

    $number = $doc->getElementById("aaaa.OutputExamsView.courseID_editor.$j")->nodeValue;
    if (substr($number, 0, 5) == "203.8") continue;
    
    $tt = $doc->getElementById("aaaa.OutputExamsView.courseName_editor.$j")->nodeValue;
    $date = $doc->getElementById("aaaa.OutputExamsView.date_editor.$j")->nodeValue;
    $date = date('Y-m-d', strtotime(str_replace('/', '-', $date)));
    $tt = str_split($tt);
    $tt = array_filter($tt, function($t) { return ord($t) < 194; });
    
    $tt = array_values($tt);
    
    for ($i = 0; $i < count($tt); $i++){
        if (ord($tt[$i]) == 151 && ($i == 0 || ord($tt[$i-1]) != 215)){
            $tt[$i] = chr(215);
        }
    }
    
    if (end($tt) == "'")
        $tt = array_splice($tt, 0, -1);
    
    $course = implode("", $tt);
    
    if ($prv != $course){
        
        if (in_array($course, $keys))
            continue;
            
        array_push($keys, $course);
    }

    $moed = array_splice($tt, -2);
    $moed = ord($moed[1]) - 144;

    $ind = count($tt);
    $c = 0;
    while ($c < 2){
        if ($tt[--$ind] == '-') break;
        if ($tt[$ind] == ' ') $c++;
    }
    
    $tt2 = array_splice($tt, 0, $ind);
    
    if (end($tt2) == '-')
        $tt2 = array_splice($tt2, 0, -1);
    
    if (end($tt2) == ' ')
        $tt2 = array_splice($tt2, 0, -1);

    $only_name = implode("", $tt2);
    $only_name = str_replace('"', "", $only_name);
    
    if (in_array($only_name, $list) || count($list) == 0) {
        array_push($arr[$date], array($course, $moed));
    }
}

include 'Calendar.php';

$calendars = array();

for ($i = 0; $i < 4; $i++)
{
    $month = intval(explode('-',$semester_end)[1])+$i;
    $calendars[$month] = new Calendar(date('Y-m-d', strtotime("+$i months", strtotime($semester_end))));
}

$colors = array("green", "blue", "red", "purple");

foreach ($arr as $date => $courses){
    
    foreach ($courses as $course){
        $month = intval(explode('-',$date)[1]);
        $calendars[$month]->add_event($course[0], $date, 1, $colors[$course[1]]);
    }
    
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Exams Calendar</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link href="calendar.css" rel="stylesheet" type="text/css">
	</head>
	<body>
	    <nav class="navtop">
	    	<div>
	    		<h1 onclick="back()"><u>Exams Calendar</u></h1>
	    	</div>
	    </nav>
	    <?php foreach ($calendars as $m => $cal){
	        echo '<div class="content home">'.$cal.'</div>';
	    }?>
	    <br/><br/>
	<script>
	    function back()
	    {
	        window.location.href = "index.php";
	    }
	</script>
	</body>
</html>
