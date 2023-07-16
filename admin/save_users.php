<?php
require_once '../site_manager/pdoconfig.php';

if (!isset($_POST['command'])) exit();

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
} catch (PDOException $pe) {
    die("Could not connect to the database $dbname :" . $pe->getMessage());
}


if ($_POST['command'] == "get_users"){
    
    $sql = "SELECT `email`,`name`,`degree`,`year`,`msg` FROM `Tusers` ORDER BY `degree`";
    $result = $conn->query($sql); 
    
    $arr = $result->fetchAll(); 
    echo json_encode($arr);
}

else if ($_POST['command'] == "delete_user"){

    $sql = "DELETE FROM `Tusers` WHERE `email`='" . $_POST['email'] . "'";
    $result = $conn->query($sql); 

    echo $result;
}

else if ($_POST['command'] == "update_user"){
    
    $sql = "UPDATE `Tusers` SET `email`='". $_POST['data'][0] ."',`name`='". $_POST['data'][1] ."',`degree`=". $_POST['data'][2] .",`year`=". $_POST['data'][3] .",`msg`='". $_POST['data'][4] ."' WHERE `email`='".$_POST['data'][5]."'";
    $result = $conn->query($sql); 
    
    echo $result;
} 

else if ($_POST['command'] == "insert_user"){
    
    $sql = "INSERT IGNORE INTO `Tusers`(`email`, `name`, `degree`, `year`) VALUES ('".$_POST['data'][0]."', '".$_POST['data'][1]."', '".$_POST['data'][2]."', '".$_POST['data'][3]."');";  
    $result = $conn->query($sql); 
    
    echo $result->rowCount();
} 

else if ($_POST['command'] == "set_msg"){
    
    $msg = $_POST['msg'];
    $mail = $_POST['mail'];
    
    $sql = "UPDATE `Tusers` SET `msg` = '$msg' WHERE `email` = '$mail'";
    $result = $conn->query($sql); 

    echo $result;
}

else if ($_POST['command'] == "inc_year"){
    
    $sql = "UPDATE `Tusers` SET `year` = `year` + 1";
    $result = $conn->query($sql); 

    echo $result;
}

else if ($_POST['command'] == "get_mails"){
    
    $year = $_POST['year'];
    $deg = $_POST['deg'];
    
    $sql = "SELECT `email` FROM `Tusers` WHERE `sendmail`=1 AND `degree`=1 AND `year`=$year ORDER BY `email`";
    
    if ($deg == "2")
        $sql = "SELECT `email` FROM `Tusers` WHERE `sendmail`=1 AND `degree`=2 ORDER BY `email`";
    else if ($year == 3)
        $sql = "SELECT `email` FROM `Tusers` WHERE `sendmail`=1 AND `degree`=1 AND (`year`=3 OR `year`=4) ORDER BY `email`";
        
    $result = $conn->query($sql); 
    
    $arr = $result->fetchAll(); 
    $arr2 = array();
    foreach ($arr as $ar) 
        array_push($arr2, $ar["email"]);

    echo implode(",", $arr2);
}

exit();



?>