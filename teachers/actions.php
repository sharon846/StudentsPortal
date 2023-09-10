<?php
require_once '../site_manager/pdoconfig.php';

if (!isset($_POST['command'])) exit();

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

if ($_POST['command'] == "add_vote"){
    
    try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    } catch (PDOException $pe) {
        die("Could not connect to the database $dbname :" . $pe->getMessage());
    }
    
    $sql = "UPDATE Tteachers SET `rank`=`rank`+".intval($_POST['vote'])." WHERE lecture='".$_POST['lecture']."';";

    $t = $conn->query($sql); 
    $result = $t->rowCount();

    echo $result;
    exit();
}

exit();

?>
