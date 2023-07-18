<!-- Code By Webdevtrick ( https://webdevtrick.com ) -->
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>SITE_NAME</title>
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css'>
  <link href="https://cdn.jsdelivr.net/css-toggle-switch/latest/toggle-switch.css" rel="stylesheet" />
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css?family=Cookie" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>


<div class="msg">
<marquee class="msg" behavior="scroll" scrollAmount="10" direction="right">

<?php 

    $ar = file("data/news", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $msg = implode("&emsp;&emsp;&emsp;&emsp;", $ar);
    $msg = str_replace("!", "&excl;", $msg);
    
    echo $msg;
?>
</marquee>
</div>


<!--text&emsp;&emsp;&emsp;&emsp;&excl;-->

<body class="hero-anime">	

<?php $count = intval(file_get_contents("data/logCount")); 
file_put_contents("data/logCount", $count+1, LOCK_EX);
?>
<div id="loading"></div>
</center>
<div class="navigation-wrap bg-light start-header start-style">
   <div class="container">
      <div class="row">
         <div class="col-12">
            <nav class="navbar navbar-expand-md navbar-light">
               <div class="collapse navbar-collapse" id="navbarSupportedContent">
                  <h3 style="font-family: 'Montserrat', sans-serif; font-size: 18px; position: absolute">SITE_NAME, <?php echo $count; ?> Entries.</h3>
                  <?php if ($count % 500 == 0) echo "<script>window.alert('wow, you are the $count user'); </script>"; ?>
                  <ul class="navbar-nav ml-auto py-4 py-md-0">
                     <li class="nav-item pl-4 pl-md-0 ml-0 ml-md-4 active">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Home</a>
                     </li>
                     <li class="nav-item pl-4 pl-md-0 ml-0 ml-md-4">
                        <a class="nav-link" href="About/">About</a>
                     </li>
                     <li id="lst" class="nav-item pl-4 pl-md-0 ml-0 ml-md-4">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">More</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="">Coming soon...</a>
                        </div>
                     </li>
                     <div style="margin-left:20px" class="switch-toggle switch-3 switch-candy">
                      <input id="on" name="state-d" type="radio" checked="" />
                      <label for="on" id="1" onclick="">ON</label>
                    
                      <input id="na" name="state-d" type="radio" disabled checked="checked" />
                      <label for="na" class="disabled" onclick="">&nbsp;</label>
                    
                      <input id="off" name="state-d" type="radio" />
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
    require_once 'site_manager/pdoconfig.php';
    
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    } catch (PDOException $pe) {
        die("Could not connect to the database $dbname :" . $pe->getMessage());
    }
    
    $sql  = "SELECT * FROM `Tlinks` WHERE `hidden`=0 ORDER BY `id` ASC";
    $result = $conn->query($sql);
    $rows = $result->fetchAll();

    $count = count($rows);
    $str = "";
    
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
        $data = str_replace('img_cap', $rows[$i]['img_cap'], $data);
        $data = str_replace('<p></p>', "<p>".$rows[$i]['more_data']."</p>", $data);
        $str .= $data;
    }
    
    if ($i > 0)
    {
        $str .= $col_end;
        $str .= $row_end;
    }

    echo $str;
    ?>

</section>
</body>

<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
<script src='https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js'></script>
<script  src="script.js"></script>

<script>

    function show_lecs(){
        $("li#lst").addClass('show');
        $("div.dropdown-menu").focus();
    }

    
</script>
 
 
</body>
</html>