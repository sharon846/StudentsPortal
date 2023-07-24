<?php
require_once '../site_manager/pdoconfig.php';

if (!isset($_POST['data'])){
    exit();
}

function sortXmlFile($filePath, $key)
{
    $xml = new DOMDocument();
    $xml->preserveWhiteSpace = false;
    $xml->formatOutput = true;
    $xml->load($filePath);

    $lectures = $xml->getElementsByTagName($key);
    $lectureArr = [];

    foreach ($lectures as $lecture) {
        $lectureArr[] = $lecture->nodeValue;
    }

    // Sort the lectures
    usort($lectureArr, function($a, $b) {
        return strcmp($a, $b);
    });

    $newXml = new DOMDocument('1.0', 'UTF-8');
    $newXml->preserveWhiteSpace = false;
    $newXml->formatOutput = true;

    $newData = $newXml->createElement('Data', "");
    
    foreach ($lectureArr as $lectureText) {
        $newLecture = $newXml->createElement($key, $lectureText);
        $newData->appendChild($newLecture);
    }
    $newXml->appendChild($newData);

    $newXml->save($filePath);
}

if (isset($_POST['command']))       //update year or new course
{
    if ($_POST['command'] == "year")
        file_put_contents("../data/kdams_year", $_POST['data']);
    if ($_POST['command'] == "change_course")
    {
        $old = $_POST['old'];
        $new = $_POST['data'];
        
        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        } catch (PDOException $pe) {
            die("Could not connect to the database $dbname :" . $pe->getMessage());
        }
        
        $sql = 'UPDATE Twhatsapp SET Twhatsapp.title_cap = "new" WHERE Twhatsapp.title_cap = "old";UPDATE Tquestions SET Tquestions.course = "new" WHERE Tquestions.course = "old";UPDATE Tkdams SET Tkdams.name = "new" WHERE Tkdams.name = "old";UPDATE Tkdams SET Tkdams.`kdams` = REPLACE(Tkdams.`kdams`, "old", "new");UPDATE TgradesSemesters SET TgradesSemesters.name = "new" WHERE TgradesSemesters.name = "old"';
        $sql = str_replace("new", $new, $sql);
        $sql = str_replace("old", $old, $sql);
        $sqls = explode(';',$sql);
        
        foreach ($sqls as $sql1)
            $conn->query($sql1);
    
        $dt = file_get_contents("../data/courses.xml");
        $dt = str_replace("<Course>$old</Course>", "<Course>$new</Course>", $dt);
        file_put_contents("../data/courses.xml", $dt);
        
        rename("../img/courses/$old.jpg", "../img/courses/$new.jpg");
    }
    if ($_POST['command'] == "change_lecture")
    {
        $old = $_POST['old'];
        $new = $_POST['data'];
        
        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        } catch (PDOException $pe) {
            die("Could not connect to the database $dbname :" . $pe->getMessage());
        }
        
        $sql = 'UPDATE Twhatsapp SET Twhatsapp.lecture = "new" WHERE Twhatsapp.lecture = "old";UPDATE Tquestions SET Tquestions.lecture = "new" WHERE Tquestions.lecture = "old";UPDATE Tkdams SET Tkdams.`lecture` = REPLACE(Tkdams.`lecture`, "old", "new");UPDATE TgradesSemesters SET TgradesSemesters.lecture = "new" WHERE TgradesSemesters.lecture = "old";';
        $sql = str_replace("new", $new, $sql);
        $sql = str_replace("old", $old, $sql);
        $sqls = explode(';',$sql);
        
        foreach ($sqls as $sql1)
            $conn->query($sql1);
    
        $dt = file_get_contents("../data/lectures.xml");
        $dt = str_replace("<Lecture>$old</Lecture>", "<Lecture>$new</Lecture>", $dt);
        file_put_contents("../data/lectures.xml", $dt);
    }
    if ($_POST['command'] == "delete_course")       //an existing one
    {
        $link = $_POST['data'];
        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        } catch (PDOException $pe) {
            die("Could not connect to the database $dbname :" . $pe->getMessage());
        }
        
        $sql = 'DELETE FROM `Tkdams` WHERE `link`="link_str"';
        $sql = str_replace("link_str", $link, $sql);
        $result = $conn->query($sql);
        echo $result->rowCount();
    }
    if ($_POST['command'] == "add_image")       //an existing one
    {
        $course = $_POST['course'];
        $path = "../img/courses/$course.jpg";
        $ifp = fopen($path, 'wb'); 
        $data = explode(',', $_POST['data']);

        fwrite($ifp, base64_decode($data[1]));
        fclose($ifp); 
    }
}

else                            //update kdams
{
   
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    } catch (PDOException $pe) {
        die("Could not connect to the database $dbname :" . $pe->getMessage());
    }
    
    $sqls = explode(';', $_POST['data']); 
    
    foreach ($sqls as $sql)
        $conn->query($sql);
    
    $courses = file_get_contents("../data/courses.xml");
    $lectures = file_get_contents("../data/lectures.xml");
    
    $courses_j = json_decode($_POST['courses'], true);
    $lectures_j = json_decode($_POST['lectures'], true);
    
    foreach ($courses_j as $crs){
        if (strpos($courses, "<Course>$crs</Course>") === false){
            $courses = str_replace("</Data>", "\t<Course>$crs</Course>\n</Data>", $courses);
        }
    }
    
    foreach ($lectures_j as $lec){
        if (strpos($lectures, "<Lecture>$lec</Lecture>") === false){
            $lectures = str_replace("</Data>", "\t<Lecture>$lec</Lecture>\n</Data>", $lectures);
        }
    }
    
    file_put_contents("../data/courses.xml", $courses);
    file_put_contents("../data/lectures.xml", $lectures);

    sortXmlFile("../data/courses.xml", 'Course');
    sortXmlFile("../data/lectures.xml", 'Lecture');
}


exit();

?>