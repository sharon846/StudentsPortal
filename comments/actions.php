<?php
require_once '../site_manager/pdoconfig.php';

if (!isset($_POST['command'])) exit();

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
} catch (PDOException $pe) {
    die("Could not connect to the database $dbname :" . $pe->getMessage());
}

if ($_POST['command'] == "add_vote"){
    
    if ($_POST['isQuestion'] == "true")
        $sql = "UPDATE Tquestions SET `rank`=`rank`+".intval($_POST['vote'])." WHERE course='".$_POST['course']."' AND lecture='".$_POST['lecture']."';";
    else
        $sql = "UPDATE Tcomments SET `rank`=`rank`+".intval($_POST['vote'])." WHERE time='".$_POST['qTime']."' AND idquestion=((SELECT id from Tquestions WHERE course='".$_POST['course']."' AND lecture='".$_POST['lecture']."'));";

    $t = $conn->query($sql); 
    $result = $t->rowCount();
    
    echo $result;
    exit();
}

if ($_POST['command'] == "add_comment"){
    
    $comment = str_replace('"', "", $_POST['comment']);
    $comment = str_replace("'", "", $comment);
    $comment = strtolower($comment);
    
    if (strpos($comment, "select") !== false || strpos($comment, "update") !== false || 
        strpos($comment, "delete") !== false || strpos($comment, "alter") != false)
        {
        echo 2;
        exit();
        }
    
    $sql = "INSERT INTO `Tcomments` (`idquestion`, `ref`, `name`, `content`) VALUES ((SELECT id from Tquestions WHERE course='".$_POST['course']."' AND lecture='".$_POST['lecture']."'), '".$_POST['mail']."', '".$_POST['name']."', '".$_POST['comment']."');";
    $conn->query($sql); 
    
    $sql = "UPDATE Tquestions SET time = NOW() WHERE course='".$_POST['course']."' AND lecture='".$_POST['lecture']."';";
    $t = $conn->query($sql); 
    $result = $t->rowCount();
    
    $time = date("d.m.Y H:i");
    
    file_put_contents(getcwd()."/../admin/updates", "added comment in ranker site##".PHP_EOL, FILE_APPEND);

    echo $result;
    exit();
}

else if ($_POST['command'] == "update_comments"){
    
    $sql = "";

    $ret = "1";
    $arr = json_decode($_POST['comments'], true);
    foreach ($arr as $comment)
    {
        $comment1 = str_replace('"', "", $comment[1]);
        $comment1 = str_replace("'", "", $comment1);
        $comment1 = strtolower($comment1);
        
        if (strpos($comment1, "select") !== false || strpos($comment1, "update") !== false || 
            strpos($comment1, "delete") !== false || strpos($comment1, "alter") !== false)
            {
            echo 3;
            exit();
            }
            
        if ($comment[1] == "")
        {
            $sql = "DELETE FROM `Tcomments` WHERE `time`='".$comment[0]."';";
            $ret = "2";
        }
        else
            $sql = "UPDATE `Tcomments` SET content='".$comment[1]."' WHERE `time`='".$comment[0]."';";
            
        $conn->query($sql); 
    }
       
    file_put_contents(getcwd()."/../admin/updates", "updated comment in ranker site##".PHP_EOL, FILE_APPEND);

    echo $ret;
    exit();
    
}

else if ($_POST['command'] == "add_question"){
    

    $sql = "INSERT INTO `Tquestions` (`id`, `course`, `lecture`, `tag`) VALUES (NULL, '".$_POST['course']."', '".$_POST['lecture']."', '".$_POST['tag']."');";
    $result = $conn->query($sql); 
    
    if ($result === false) { echo "-1"; exit(); }
    
    file_put_contents(getcwd()."/../admin/updates", "added question in ranker site##".PHP_EOL, FILE_APPEND);

    echo "1";
    exit();
}

exit();

?>