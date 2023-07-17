<?php
require_once '../site_manager/pdoconfig.php';

$basic = array("שנה א", "שנה ב", "שנה ג");

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
} catch (PDOException $pe) {
    die("Could not connect to the database $dbname :" . $pe->getMessage());
}

if (isset($_POST['blocking']))
{
    echo json_encode(implode(", ", array_values(getWhoBlocking($_POST['blocking']))));
    exit();
}

//region of getting 
if (isset($_POST["course"]))
{
    $lst = array();
    $currCourses = array($_POST["course"]);
    
    if (strpos($_POST["course"], 'שנה') !== false)
    {
        $currCourses = getYear(1);
        if ($_POST["course"] == "שנה ב" || $_POST["course"] == "שנה ג")
            $currCourses = array_merge($currCourses, getYear(2));
        if ($_POST["course"] == "שנה ג")
            $currCourses = array_merge($currCourses, getYear(3));
    }
    
    foreach ($currCourses as $course)
        $lst = array_merge($lst, getKdams($course));
    
    echo json_encode(array_values($lst));
    exit();
}

function generateMatchCourses($chosen)
{
    $finalArr = array("a" => array("מפגש חוגי - 0 נקודות, קורס חובה, מרצה: אין. הערה: חובת רישום"), "b" => array("מפגש חוגי - 0 נקודות, קורס חובה, מרצה: אין. הערה: חובת רישום"), "c" => array());
    $pts = 0;
    
    $sql = "SELECT * FROM `Tkdams` WHERE `name` NOT IN ('".implode("','", $chosen)."')";
    $result = $GLOBALS['conn']->query($sql);
    $next_courses = $result->fetchAll(); 
    
    $next_courses = array_filter($next_courses, function($tp) use($chosen) { return $tp["kdams"] == "" || count(array_diff(explode(',',$tp["kdams"]),$chosen)) == 0; });
    
    foreach($next_courses as $course)
    {
        $counter = 0;
        $ids = str_split($course["ids"]);
        foreach ($ids as $id){
            array_push($finalArr[$id], getOutput($course["name"], $course["pts"], $course["year"] != "" ? "חובה" : "בחירה", explode(',', $course["lecture"])[$counter], isset($course["note"]) ? $course["note"] : "", $course["link"], $id));
            $counter++;
        }
    }
    
    if (count($finalArr["a"]) == 0) unset($finalArr["a"]);
    if (count($finalArr["b"]) == 0) unset($finalArr["b"]);
    if (count($finalArr["c"]) == 0) unset($finalArr["c"]);
    
    $sql = "SELECT SUM(`pts`) FROM `Tkdams` WHERE `name` IN ('".implode("','", $chosen)."')";
    $pts = $GLOBALS['conn']->query($sql)->fetch()[0];
    
    return array($finalArr, $pts);
}

function getOutput($name, $pts, $id, $lect, $note, $link, $sem)
{
    $semArr = array("a" => "001", "b" => "002", "c" => "003");
    $str = $name;
    if ($link != "")
        $str = "<a class='result' href='https://studapps.haifa.ac.il/catalog/#/course/$link/".$semArr[$sem]."'>$name</a>";

    $str = "$str - pts נקודות, קורס duty, מרצה: lect";
    $str = str_replace("pts", $pts, $str);
    $str = str_replace("duty", $id, $str);
    $str = str_replace("lect", $lect == "" ? "אין" : $lect, $str);
    if ($note != "") { $str .= ". הערה: note"; $str = str_replace("note", $note, $str); }
    
    return $str;
}

function getAllCoursesList()
{
    $courses = $GLOBALS['basic'];
    
    $sql = "SELECT `name` FROM `Tkdams`";
    $result = $GLOBALS['conn']->query($sql);
    $list = $result->fetchAll(); 
    $list = array_map(function($tp) { return $tp["name"]; }, $list);

    return array_merge($courses, $list);
}

function getKdams($passedCourse)
{
    $list = array($passedCourse);
    
    $sql = "SELECT `kdams` FROM `Tkdams` WHERE `name`='$passedCourse'";
    $result = $GLOBALS['conn']->query($sql);
    $lst = $result->fetch()[0]; 
    
    if ($lst == "")
        return $list;
    
    $lst = explode(',', $lst); 

    foreach ($lst as $depen)
        $list = array_merge($list, getKdams($depen));
    
    $list = array_unique($list);
    return $list;
}

function getAllAsArray()
{
    $list = getAllCoursesList();
    array_shift($list);
    array_shift($list);
    
    $keys = array_map(function($tp) { return "'$tp'"; }, $list);
    $values = array();
    
    $final = array();
    
    foreach ($list as $course)
        array_push($values, getKdams($course));
    
    return array_combine($keys, $values);
}

function getWhoBlocking($passedCourse)
{
    $finalArr = array();
    $courses = getAllAsArray();
    foreach ($courses as $name => $prvs)
    {
        foreach ($prvs as $prv)
        {
            if ($prv == $passedCourse)
                array_push($finalArr, trim($name, "'"));
        }
    }
    
    return $finalArr;
}


function getYear($year)
{
    $list = array();
    
    $sql = "SELECT `name` FROM `Tkdams` WHERE `year`='$year'";
    $result = $GLOBALS['conn']->query($sql);
    $list = $result->fetchAll(); 
    $list = array_map(function($tp) { return $tp["name"]; }, $list);

    return $list;
}

?>