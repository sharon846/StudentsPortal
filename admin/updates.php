<?php

$lines = file("updates", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if (isset($_POST['index'])){
    $ind = intval($_POST['index']);
    
    if ($ind == -1)
    {
        file_put_contents("updates", "");
    }
    
    else if (count($lines) > $ind){
        unset($lines[$ind]);
        file_put_contents("updates", implode("\r\n", $lines)."\r\n");
    }
    
    echo "";
    exit();
}

?>


<html>
<head>
<title>Manage Grades</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="UTF-8">
<meta http-equiv="Cache-Control" content="max-age=0, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0" />

<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js" integrity="sha256-/H4YS+7aYb9kJ5OKhFYPUjSJdrtV6AeyJOtTkw6X72o=" crossorigin="anonymous"></script>

<style>

::placeholder { 
  color: wheat;
}

body{
    background-image: url('admin.png');
    background-size: contain;
    background-repeat: round;
}

h1{
    color: white;
    position: relative;
    top: 30px;
    font-size: 3em;
}

select{
    width: 50%;
    direction: rtl;
    background-color: lightslategrey;
    color: white;
    border: 6px solid transparent;
    font-size: 14px;
}

select.tiny{
    width: 50%;
    position: relative;
    left: 128%;
    top: -45px;
}

h3{
    direction: rtl;
    color:white;
    position: relative;
}

table{
    color: white;
    width: 50%;
    padding-top: 50px;
    font-size: 25px;
}

table#grade-edit{
    width: 60%;
}

tr>td {
  padding-bottom: 1em;
}

td{
    text-align: center; 
    vertical-align: middle;
    direction: rtl;
    width: 4%;
}

td.grades{
    width: 10%;
}

input{
    background: transparent;
    border: none;
    color: white;
    font-size: 25px;
    text-align: center;
    font-family: auto;
}

input.tiny{
    width: 70px;
}

div.data{
    position: absolute;
    top: 51%;
    left: 25%;
}

button{
    border-radius: 9999px;
    border-color: transparent;
    background-color: darkorange;
    width: 200px;
    height: 54px;
    font-size: 25px;
    color: white;
    margin: 20 30 20 30;
}

a{
    color: white;
}

button:hover{
    border: 2px solid white;
    
}

td{
    text-align: center; 
    vertical-align: middle;
}

td.thin{
    width: 4%;
}

td.heavy{
    width: 12%;
}

td.middle{
    width: 10%;
}


</style>
</head>

<body>
    <center>
        <h1>מערכת הודעות</h1>
        <button>Clear all</button>
        <table>
            <?php
                foreach ($lines as $line){
                    $content = @explode('##', $line)[0];
                    $link = @explode('##', $line)[1];
                    echo "<tr><td class='middle'><a href='$link'>$content</a></td><td class='thin'><img style='width: 15px; top: 3px' src='../img/check.png'></td></tr>";
                }
                
                if (count($lines) == 0){
                    echo "<h3 style='direction: ltr'>No New Messages!</h3>";
                }
            ?>
    </table>
    </center>
    <script>
        $("img").on( "click", function() {
            $.ajax({
                url: "updates.php", 
                type: "post",
                data: {"index": $(this).parent().parent().index()},
                
                success: function(res){
                    window.alert("success");
                    location.reload();
                }
            });
        });
        $("button").on( "click", function() {
            $.ajax({
                url: "updates.php", 
                type: "post",
                data: {"index": -1},
                
                success: function(res){
                    window.alert("success");
                    location.reload();
                }
            });
        });

        $("a").on("click", function() {
            if ($(this).text().includes("registered")){
                window.alert($(this).attr("href"));
                return false;
            }
        });
    </script>
</body>
</html>
