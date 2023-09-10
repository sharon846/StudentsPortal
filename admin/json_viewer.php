<?php

function pretty_print($json_data)
{
    $json_data = file_get_contents($json_data);
    //Initialize variable for adding space
    $space = 0;
    $flag = false;
    //Using <pre> tag to format alignment and font
    echo"<pre>";
    //loop for iterating the full json data
    for($counter=0; $counter<strlen($json_data); $counter++)
    {
    //Checking ending second and third brackets
    if( $json_data[$counter] == '}' || $json_data[$counter] == ']' )
        {
    $space--;
    echo"\n";
    echo str_repeat(' ', ($space*2));
        }
    
    //Checking for double quote(“) and comma (,)
    if( $json_data[$counter] == '"'&& ($json_data[$counter-1] == ',' || $json_data[$counter-2] == ',') )
        {
    echo"\n";
    echo str_repeat(' ', ($space*2));
        }
    if( $json_data[$counter] == '"'&& !$flag )
        {
    if( $json_data[$counter-1] == ':' || $json_data[$counter-2] == ':' )
    //Add formatting for question and answer
    echo'<span style="color:blue;font-weight:bold">';
    else
    //Add formatting for answer options
    echo'<span style="color:red">';
        }
    echo$json_data[$counter];
    //Checking conditions for adding closing span tag  
    if( $json_data[$counter] == '"'&&$flag )
    echo'</span>';
    if( $json_data[$counter] == '"' )
    $flag= !$flag;
    //Checking starting second and third brackets
    if( $json_data[$counter] == '{' || $json_data[$counter] == '[' )
        {
    $space++;
    echo"\n";
    echo str_repeat(' ', ($space*2));
        }
    }
    echo"</pre>";
}


if (isset($_POST['data'])){
    
    $str =  $_POST['data'];  
    $is_courses = $str == "courses";

    $old = $_POST['old'];

    if (!isset($_POST['new']))
    {
        $data = file_get_contents("../data/$str.json");
        $data = json_decode($data, true);
        $data['Data'] = array_diff($data['Data'], [$old]);
        $data['Data'] = array_values($data['Data']);
        $data['Data'] = array_unique($data['Data']);
        $data['Data'] = array_values($data['Data']);
        
        $updatedJsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents("../data/$str.json", $updatedJsonData);
        
        exit();
    }

    require_once '../site_manager/pdoconfig.php';
    
    $new = $_POST['new'];

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    } catch (PDOException $pe) {
        die("Could not connect to the database $dbname :" . $pe->getMessage());
    }
    
    if ($str == "lecturers")
        $sql = 'UPDATE Twhatsapp SET Twhatsapp.lecture = "new" WHERE Twhatsapp.lecture = "old";UPDATE Tquestions SET Tquestions.lecture = "new" WHERE Tquestions.lecture = "old";UPDATE TgradesSemesters SET TgradesSemesters.lecture = "new" WHERE TgradesSemesters.lecture = "old"';
    
    else{
        $sql = 'UPDATE Twhatsapp SET Twhatsapp.title_cap = "new" WHERE Twhatsapp.title_cap = "old";UPDATE Tquestions SET Tquestions.course = "new" WHERE Tquestions.course = "old";UPDATE Tkdams SET Tkdams.name = "new" WHERE Tkdams.name = "old";UPDATE Tkdams SET Tkdams.`kdams` = REPLACE(Tkdams.`kdams`, "old", "new");UPDATE TgradesSemesters SET TgradesSemesters.name = "new" WHERE TgradesSemesters.name = "old"';
        @rename("../img/courses/$old.jpg", "../img/courses/$new.jpg");
    }
    
    $sql = str_replace("new", $new, $sql);
    $sql = str_replace("old", $old, $sql);
    
    $sqls = explode(';',$sql);
        
    foreach ($sqls as $sql1)
        $conn->query($sql1);
    
    //update json
    $data = file_get_contents("../data/$str.json");
    $data = json_decode($data, true);
    
    $data['Data'] = array_diff($data['Data'], [$old]);
    array_push($data['Data'], $new);
    $data['Data'] = array_values($data['Data']);
    $data['Data'] = array_unique($data['Data']);
    $data['Data'] = array_values($data['Data']);
    
    sort($data["Data"]); // Sort the array
        
    $updatedJsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents("../data/$str.json", $updatedJsonData);
    
    exit();
} 

else if (isset($_GET['data']))
{
    $str =  $_GET['data'];  
    $is_courses = $str == "courses";
} 

else 
    exit();

?>

<html>
    <head>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.1/ace.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.slim.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>
    <body>
        <h2 style="display: contents;">Filename: <?php echo $str; ?></h2> <br/><br/>
        <button style="margin-right: 1em;" onclick="global_change()">Global change <?php echo $str; ?> name</button>
        <button style="margin-right: 1em;" onclick="local_delete()">Local delete <?php echo $str; ?> name</button>
        <div id="editor">
        <?php
        
        pretty_print("../data/$str.json");
        ?>
        </div>
        
        <script>
            function local_delete()
            {
                const str = <?php echo "'$str'"; ?>;
                var old = prompt("enter " + str + " name");
                if (old == null || old.trim().length == 0) return;
                if (!$("div#editor").text().includes(old)){
                    window.alert("no such " + str);
                    return;
                }
                
                $.ajax({
                    url:"json_viewer.php", //the page containing php script
                    type: "post", //request type,
                    data: {"data": str, "old": old},
                    success:function(result){
                        if (result == "") 
                            window.alert("saved");
                        else 
                            window.alert(result);
                            
                        location.reload();
                    }
                });
            }
        
            function global_change()
            {
                const str = <?php echo "'$str'"; ?>;
                var old = prompt("enter old " + str + " name");
                if (old == null || old.trim().length == 0) return;
                /*if (!$("div#editor").text().includes(old)){
                    window.alert("no such " + str);
                    return;
                }*/
                
                var new_name = prompt("enter new " + str + " name");
                if (new_name.trim().length == 0 || new_name == null){
                    window.alert("empty " + str + " is not possible");
                    return;
                }
                
                $.ajax({
                    url:"json_viewer.php", //the page containing php script
                    type: "post", //request type,
                    data: {"data": str, "old": old, "new": new_name},
                    success:function(result){
                        if (result == "") 
                            window.alert("saved");
                        else 
                            window.alert(result);
                            
                        location.reload();
                    }
                });
            }
        </script>
    </body>
</html>