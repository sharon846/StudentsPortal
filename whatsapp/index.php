<?php

$server_root = "{$_SERVER['DOCUMENT_ROOT']}/";
$curr_dir = getcwd();
$representive_dir = str_replace($server_root, "", $curr_dir);
$representive_url = $_SERVER['REQUEST_SCHEME'].'://SITE_DOMAIN/'.$representive_dir;

@set_time_limit(3600);
session_name("SITE_SESSION_NAME");
session_start();

if (!isset($_SESSION["SITE_SESSION_NAME"])){
    header("Location: https://SITE_URL/login/index.php?referer=$representive_url/");
    exit();
}

?>

<!-- Code By Webdevtrick ( https://webdevtrick.com ) -->
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>SITE_NAME</title>
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css'>
  <link href="https://cdn.jsdelivr.net/css-toggle-switch/latest/toggle-switch.css" rel="stylesheet" />
  <link rel="stylesheet" href="../style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

  <style>
  select{
      font-family: 'Montserrat', sans-serif; 
      border-width: 0;
      font-size: 1.75rem;
      position: relative;
      top: -4px;
      margin-right: 20px;
      background-color: transparent;
  }
  
  body.dark select{
      color: white; 
      background-color: transparent;
  }
  
  body.dark option{
      color: black; 
  }
  
  p{
      direction: rtl;
  }
  </style>
</head>
<body>

<!--<div class="msg2" style="left: 10%"><img width="100px" height="75px" src="../img/144.png"/></div>
<div class="msg2" style="left: 80%"><img width="100px" height="75px" src="../img/144.png"/></div>-->

<body class="hero-anime">	
<div id="loading"></div>
</center>
<div class="navigation-wrap bg-light start-header start-style">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <nav class="navbar navbar-expand-md navbar-light">
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <form style="display: contents" action="" method="post">
              <select id="years" name="year" onchange="this.form.submit()">
              </select>
              <select id="semesters" name="semester" onchange="this.form.submit()">
                <option value="c">Semester C</option>
                <option value="b">Semester B</option>
                <option value="a">Semester A</option>
              </select>
              <h3> WA links</h3>
            </form>
            <ul class="navbar-nav ml-auto py-4 py-md-0">
              <div style="margin-left:20px" class="switch-toggle switch-3 switch-candy">
                <input id="on" name="state-d" type="radio" checked="">
                <label for="on" id="1" onclick="">ON</label>
                <input id="na" name="state-d" type="radio" disabled="" checked="checked">
                <label for="na" class="disabled" onclick="">&nbsp;</label>
                <input id="off" name="state-d" type="radio">
                <label for="off" id="0" onclick="">OFF</label>
                <a></a>
              </div>
            </ul>
          </div>
        </nav>
      </div>
    </div>
  </div>
</div>

 
 <!-- two in a col!! -->

<?php
$row_start = '<div class="row">';
$row_end = '</div>';
$col_start = '<div class="col-md-6"><div class="row">';
$col_end = '</div></div>';
$cell_data = '<div class="col-sm-6 caption flex"><a target="_blank" href="href_cap" title="title_cap"><h4>title_cap</h4></a><br/><a target="_blank" href="href_cap" title="title_cap"><img src="img_cap" class="serv" alt="title_cap"/></a><p></p></div>';
?>

<section id="services" class="container2">
    <?php
    require_once '../site_manager/pdoconfig.php';
    
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    } catch (PDOException $pe) {
        die("Could not connect to the database $dbname :" . $pe->getMessage());
    }
    
    $sql  = "SELECT * FROM `Twhatsapp` ORDER BY `id` ASC";
    
    if (isset($_POST['year'],$_POST['semester']))
    {
        $year = $_POST['year'];
        $semester = $_POST['semester'];
        
        $id = intval($year)*10+ord($semester)-96;
        $min = 100*$id;
        $max = $min + 100;
        $sql  = "SELECT * FROM `Twhatsapp` WHERE `id`>=$min AND `id`<$max ORDER BY `id` ASC";
    }

    $result = $conn->query($sql);
    $rows = $result->fetchAll();

    $count = count($rows);
    $str = "";
    
    if ($count > 0)
    {
        $year = intval(end($rows)['id'] / 1000);
        $semester_n = intval((end($rows)['id'] % 1000) / 100);
        $semester = array(1 => "a", 2 => "b", 3 => "c")[$semester_n];
        
        $rows = array_filter($rows, function($dt) { global $year, $semester_n; return intval($dt['id'] / 100) == $year*10+$semester_n; });
        $rows = array_values($rows);
        $count = count($rows);
        
        for ($i = 0; $i < $count; $i++)
        {
            if ($i == 0)
            {
                $str .= $row_start;
                $str .= $col_start;
            }
            
            else if ($i % 2 == 0)
            {
                $str .= $col_end;
                
                if ($i % 4 == 0)
                {
                    $str .= $row_end;
                    $str .= $row_start;
                }
                
                $str .= $col_start;
            }
    
            $data = $cell_data;
            $data = str_replace('href_cap', $rows[$i]['href_cap'], $data);
            $data = str_replace('title_cap', $rows[$i]['title_cap'], $data);
            $data = str_replace('img_cap', "https://SITE_URL/img/courses/".$rows[$i]['title_cap'].".jpg", $data);
            if ($rows[$i]["lecture"] != "")
                $data = str_replace('<p></p>', "<p>מרצה: ".$rows[$i]['lecture']."</p>", $data);
            $str .= $data;
        }
        
        if ($i > 0)
        {
            $str .= $col_end;
            $str .= $row_end;
        }
    } else {
        $str = "<h1>No results</h1>";
    }

    echo $str;
    ?>

</section>
</body>

<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
<script src='https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js'></script>
<script  src="../script.js"></script>

<script>
    var year = <?php echo "'$year'"; ?>;
    var semester = <?php echo "'$semester'"; ?>;
    var curyear = new Date().getFullYear();
    if (new Date().getMonth() > 7) curyear += 1;
    
    window.onload = function(){
        
        for (var i = curyear; i >= 2023; i--)
        {
            $("select#years").append($('<option>', {
                value: i,
                text: i
            }));
        }
        
        $("select#years").val(year);
        $("select#semesters").val(semester);
    }
</script>
 
 
</body>
</html>