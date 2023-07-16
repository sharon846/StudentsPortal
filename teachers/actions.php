<?php
require_once '../site_manager/pdoconfig.php';

if (!isset($_POST['command'])) exit();

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
