<?php
require_once '../site_manager/pdoconfig.php';

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

if (!isset($_POST['data'])){
    exit();
}

if (isset($_POST['command']))       //update year or new course
{
    if ($_POST['command'] == "year")
        file_put_contents("../data/kdams_year", $_POST['data']);
    if ($_POST['command'] == "delete_course")       //an existing one
    {
        $code = $_POST['data'];
        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        } catch (PDOException $pe) {
            die("Could not connect to the database $dbname :" . $pe->getMessage());
        }
        
        $sql = 'DELETE FROM `Tkdams` WHERE `code`="code_str"';
        $sql = str_replace("code_str", $code, $sql);
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
    
    //update courses
    $courses = file_get_contents("../data/courses.json");
    $courses = json_decode($courses, true);
    $courses_j = json_decode($_POST['courses'], true);
    
    $existingLecturers = $courses["Data"];
    foreach ($courses_j as $lecturer) {
        if (!in_array($lecturer, $existingLecturers)) {
            $existingLecturers[] = $lecturer;
        }
    }
    sort($existingLecturers);
    $updatedData = ["Data" => $existingLecturers];
    $updatedJsonData = json_encode($updatedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents("../data/courses.json", $updatedJsonData);
}



exit();

?>