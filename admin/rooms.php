<?php

if (isset($_POST['aa']))
{
    $res = 0;

    $data = file_get_contents("https://studapps.haifa.ac.il/catalog/CatalogServlet?operation=getActiveYear");
    $data = json_decode($data, true);
    $data = $data['data']['year'];
        
    $res += file_put_contents("../rooms-cs/backend/data.json", file_get_contents("https://studapps.haifa.ac.il/catalog/CatalogServlet?operation=getCourses&p_lang=B&p_year=".$data));

    if ($res > 0)
        ?> <script> window.alert(<?php echo "'success'"; ?>); </script> <?php
}

?>


<html>
<head>
<title>Manage Rooms</title>
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

*{
    font-family: Arial;
}

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
    border-width: 1px;
    border-color: white;
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
        <h1>מערכת חדרים פנויים</h1>
        <br/>
        <form method="post">
            <input type="hidden" name="aa" value="aa"/>
            <button onclick="submit()">Load JSON</button>
        </form>
        
        <button onclick="window.location.href='txt_editor.php?filename=../rooms-cs/backend/rooms'">Edit rooms list</button>
        <button onclick="window.location.href='../rooms-cs/backend/rooms_prepear.php?id=wipe'">Wipe data</button>
        <br/><br/>
        
        <?php if(file_exists("../rooms-cs/backend/data.json")): ?>

        <label>Sems:</label>
        <input id="s" type="text" placeholder="semesters: A,B,C"/>
        <br/><br/>
        <label>Hugs numer:</label>
        <input id="h" type="text" placeholder="hugs: 203,206,701"/>
        <br/>
        
        <button onclick="prepear()">Generate</button>
        
        <div id="links"></div>
        <?php endif; ?>
    </center>
    
    <script>
        function prepear()
        {
            var html = "";
            
            var sems = $("input#s").val().split(',');
            var hugs = $("input#h").val().split(',');
            
            var list = cartesianProduct(sems, hugs);
            
            list.forEach(elem => {
                var link = '../rooms-cs/backend/rooms_prepear.php?id=50000' + elem[1] + '_00' + (elem[0].charCodeAt(0) - 64);
                var text = elem[1] + ' sem ' + elem[0];

                html += '<button onclick="window,location.href=' + "'" + link + "'" + '">' + text + '</button>';
            })
            
            $("div#links").after(html);
        }
        
        const cartesianProduct = (arr1, arr2) => {
           const res = [];
           for(let i = 0; i < arr1.length; i++){
              for(let j = 0; j < arr2.length; j++){
                 res.push(
                    [arr1[i]].concat(arr2[j])
                 );
              };
           };
           return res;
        };
    </script>
</body>
</html>