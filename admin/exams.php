<?php

if (isset($_POST['aa']))
{
    $res = 0;

    if (isset($_FILES["a"])) {
        $res += move_uploaded_file($_FILES["a"]["tmp_name"], "../exams/a.html");
    }
    
    if (isset($_FILES["b"])) {
        $res += move_uploaded_file($_FILES["b"]["tmp_name"], "../exams/b.html");
    }
    
    if ($res > 0)
        ?> <script> window.alert(<?php echo "'success'"; ?>); </script> <?php
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
input[type=file]{
    font-size: 18px;
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

label{
    color: white;
    font-size: 18px;
    padding-right: 10px;
}

p{
    color: white;
    font-size: 18px;
    width: 80%;
}

</style>
</head>

<body>
    <center>
        <h1>עדכון לוח מבחנים</h1>
        <br/>
        <form method="post" enctype="multipart/form-data">
            <label>Semester A</label>
            <input accept=".html" type="file" id="a" name="a" class="form-field" />
            <br/><br/>
            <label>Semester B</label>
            <input accept=".html" type="file" id="b" name="b" class="form-field" />
            <br/>
            <input type="hidden" name="aa" value="aa"/>
            <button onclick="submit()">Submit</button>
        </form>
        
        <p>
            In order to upload html, go to portal, navigate to info -> faculty exams list -> choose info of current year.<br/>
            Right click on mouse -> inspect elements -> network. Click submit on portal. <br/>
            On inspect window, right click on PageBuilder -> copy response, save as html file & upload<br/>
        </p>
    </center>
</body>
</html>