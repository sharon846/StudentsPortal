<?php
require_once '../site_manager/pdoconfig.php';

function log_data($dir, $mail)
{
    global $host, $username, $password, $dbname;   
    $conn = @new mysqli($host, $username, $password, $dbname);
    
    $sql = "UPDATE `Tusers` SET `last_dir`='$dir' WHERE `email`='$mail'"; 
    $conn->query($sql);
    $conn->close();
}


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

log_data($representive_dir, $_SESSION['SITE_SESSION_NAME']["mail"]);

$edit = isset($_GET['edit']);
$ref = md5($_SESSION['SITE_SESSION_NAME']["mail"]);

?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="UTF-8">

<link rel="stylesheet" href="../header.css">

<script src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>

@media (max-width: 600px) { 
    input.search{
        top: 20%;
    }
}

div.container{
    top: 5em;
    position: relative;
    direction: rtl; 
}

body {
    background-color: #eee;
}

span.mr-1{
    height: 21px;
    color: #fff;
    font-size: 11px;
    padding: 8px;
    border-radius: 4px;
    line-height: 3px
}

.ml-3, .mx-3 {
    margin-right: 2rem!important;
}

.duty{
    background-color: blue;
}

.seminar{
    background-color: green;
}

.choose{
    background-color: red;
}

.all{
    background-color: gray;
}

.edit{
    background-color: darkcyan;
}

.hit-voting:hover {
    color: blue
}

.hit-voting {
    cursor: pointer
}

span.time{
    position: relative;
    direction: ltr;
    margin-right: 5px;
}

div.row{
    margin-top: 3rem;
}

div.comment-text-sm{
    right: 20px;
    position: relative;
}

input.search{
    border-radius: 999px;
    border-width: 0px;
    direction: rtl;
    background-color: #eee;
    margin-top: 20px;
    text-align: center; 
}

.d-flexx{
    display: flex;
}


span.filter{
    cursor: pointer;
}

textarea{
    font-family: 'Rokkitt', Helvetica, Arial, sans-serif;
    font-size: 1em;
    padding-bottom: 1em;
    background-color: transparent;
    border-width: 0px;
    width: 100%;
    line-height: 1.4em;
    color: #444;
    align-content:center;
    overflow:auto;
}

</style>

</head>
<body>

<header id="header">
    <div style="width: 100%; height: 100%">
        <a class="hover" onclick="add_q()"  title="Directory Lister">
            <img width="100%" style="cursor:pointer" src="../img/+.png"/>
        </a>
        
        <div style="width: 100%; height: 62%; top: 0%; position: absolute;">
            <form id='frm' method="POST" onsubmit="return false">
                <span class='ad'><?php echo file_get_contents("../data/ad"); ?></span>
            </form>
            
            <div id="mode">
               <div class="transform">
                    <i id="lamp" class="fa fa-lightbulb-o"></i>
                </div>
            </div>
        </div>
        <div style="position: absolute;left: 3.3%;height: 30%;color: white;top: 70%;">
            <h4 class="dl hover" title="Info" style="font-size: 1.1rem; line-height: 1.25rem;">
                הוספת פוסט
            </h4>
        </div>
        <div style="position: absolute;right: 3.3%;height: 30%;color: white;top: 70%;">
            <h4 class="dl hover" title="Info" style="font-size: 1.1rem; line-height: 1.25rem;">
                DEPT_NAME
            </h4>
        </div>
    </div>
</header>

<center style="top: 100px;position: relative">
    <span class='filter mr-1 choose'>בחירה</span>
    <span class='filter mr-1 duty'>חובה</span>
    <span class='filter mr-1 seminar'>סמינר</span>
    <span class='filter mr-1 all'>הכל</span>
    <?php if ($edit) { ?> <span class='filter mr-1 edit'>כיבוי עריכה</span>
    <?php } else { ?><span class='filter mr-1 edit'>עריכה</span><?php } ?>
    <input type="text" oninput="filter()" placeholder="סנן לפי מרצה" class="search lecture"/>
    <input type="text" oninput="filter()" placeholder="סנן לפי קורס" class="search course"/>
</center>

<div class="container mt-5 mb-5">
    <?php
        require_once '../site_manager/pdoconfig.php';
        
        function get_tag_heb($tag){
            if ($tag == "choose") return "בחירה";
            if ($tag == "duty") return "חובה";
            if ($tag == "seminar") return "סמינר";
        }
    
        $quesPattern = explode('commenttagseperator', file_get_contents("q.html"))[0];
        $commentPattern = explode('commenttagseperator', file_get_contents("q.html"))[1];
        
        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        } catch (PDOException $pe) {
            die("Could not connect to the database $dbname :" . $pe->getMessage());
        }
        
        $sql  = "SELECT * FROM Tquestions ORDER BY YEAR(`time`) DESC, `rank` DESC;";
        $result = $conn->query($sql);
        $rows = $result->fetchAll();

        foreach ($rows as $row)
        {
            $toprint = str_replace("qrank", $row["rank"], $quesPattern);
            $toprint = str_replace("qlecture", $row["lecture"], $toprint);
            $toprint = str_replace("qcourse", $row["course"], $toprint);
            $toprint = str_replace("qtime", $row["time"], $toprint);
            $content = "";
            if ($row["lecture"] != "") $content .= "מרצה: " . $row["lecture"];
            if ($row["course"] != "") $content .= " קורס: " . $row["course"];
            
            $toprint = str_replace("qcontent", $content, $toprint);
            
            $tags = explode(',', $row["tag"]);
            $tags_print = "";
            foreach ($tags as $tag){
                $tagheb = get_tag_heb($tag);
                $tags_print .= "<span class='$tag mr-1'>$tagheb</span>";
            }
                
            $toprint = str_replace("qtag", $tags_print, $toprint);
            
            $sql = "SELECT `name`,`time`,`content`,`rank` FROM Tcomments WHERE `idquestion`='".$row['id']."'";
            if ($edit) $sql .= " AND `ref`='".$ref."'";
            $sql .= " ORDER BY EXTRACT(YEAR_MONTH FROM `time`) DESC, `rank` DESC";

            $result = $conn->query($sql);
            $cols = $result->fetchAll();

            $toprint = str_replace("qcomments", count($cols), $toprint);
            
            $totalComments = "";
            
            foreach ($cols as $col)
            {
                $commentContextPattern = $edit ? "<textarea rows='6'>ccontent</textarea>" : "<span>ccontent</span>";
                $commentContext = str_replace("ccontent", $col["content"], $commentContextPattern);
                
                $comments = "";
                $comments = str_replace("cname", $col["name"], $commentPattern);
                $comments = str_replace("ctime2", $col["time"] , $comments);
                $comments = str_replace("ctime", substr($col["time"], 0, -3) , $comments);
                $comments = str_replace("<span>ccontent</span>", $commentContext, $comments);
                $comments = str_replace("crank", $col["rank"], $comments);
                $totalComments .= $comments;
            }
            
            if ($edit && count($cols) == 0) continue;
            
            $toprint = str_replace("commentssection", $totalComments, $toprint);
            echo $toprint;
        }
    ?>
<div style="margin-bottom: -100px; position: absolute; height: 100px; width: 100%; bottom: 0;"></div>
</div>

<script>

window.onload = function(){
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)
    		    switchMode();
    
    $("div#mode").click(switchMode);

    if (<?php echo json_encode($edit); ?>)
    {
        $("div[class*='voting']").remove();
        $("input.form-control.mr-3").attr('placeholder', "edit your comment, then press save. to delete, leave blank");
        $("button.btn.btn-primary").text("Save");
    } else {
        window.alert("קראתם? עכשיו תגיבו :)");
    }

    $("span:contains('ממתין לאישור מנהל')").parent().next().remove();
    $(document).on('click', 'button', function(){
        
        if (<?php echo json_encode($edit); ?>)
        {
            var comments = [];
            var elems = $(this).parent().parent();
            
            for (var i = 1; i < elems.children().length; i+=2)
            {
                var comment = [];
                var child = elems.children(":eq("+i+")");
                comment.push(child.find("input#time").val());
                comment.push(child.find("textarea").val());
                comments.push(comment);
            }
            
            $.ajax({
                    url:"actions.php", //the page containing php script
                    type: "post", //request type,
                    data: {"command": "update_comments", "comments": JSON.stringify(comments), 
                    "course": $(this).parent().parent().prev().find(':input[name="course"]').val(),
                    "lecture": $(this).parent().parent().prev().find(':input[name="lecture"]').val()},
                    success: function(result){
                        if (result == "1" || result == "2"){
                            window.alert("עודכן");
                            if (result == "2")
                                location.reload();
                        } else if (result == "3"){
                            window.alert("dont even think about it");
                        }
                    }
            });
        }
        
        else
        {
            var name = prompt("מה שמך? ניתן גם לרשום אנונימי.");
            if (name == null || name == "") return false;
            $.ajax({
                    url:"actions.php", //the page containing php script
                    type: "post", //request type,
                    data: {"command": "add_comment", "name": name, "comment": $(this).prev().val(), 
                    "course": $(this).parent().parent().prev().find(':input[name="course"]').val(),
                    "lecture": $(this).parent().parent().prev().find(':input[name="lecture"]').val(),
                     "mail": <?php echo json_encode($ref); ?>
                    },
                    success: function(result){
                        if (result == "1"){
                            window.alert("התגובה פורסמה ותימחק אם תהיה לא עניינית או פוגענית");
                        } else if (result == "2"){
                            window.alert("dont even think about it");
                        }
                    }
            });
        }
    });
    
    $("span.filter").on('click', function(){
        if ($(this).text() == "הכל"){
            $("div.d-flexx.justify-content-center.row").show();
            return true;
        }
        
        if ($(this).text() == "עריכה"){
            window.location.href = "?edit=1"
        } else if ($(this).text() == "כיבוי עריכה"){
            window.location.href = "index.php"
        }
        
        var text = $(this).text();
        text = 'mr-1">' + text;
        $("div.d-flexx.justify-content-center.row").hide();
        $("div.d-flexx.justify-content-center.row").filter(function() {
            return $(this).html().includes(text);
        }).show();
    });
    
    $(document).on('click', 'i[class$="hit-voting"]', function(){
       
       var name = prompt("מה שמך? לשימוש פנימי בלבד");
       if (name == null || name == "") return false;
       
       var up = $(this).attr('class').includes("up") ? 1 : -1;
       var isqorc = $(this).parent().attr('class').includes("column");
       var ifields = $(this).parents("div.d-flexx.justify-content-center.row");
       $(this).parent().find("span").text(parseInt($(this).parent().find("span").text()) + up);
       
       $.ajax({
            url:"actions.php", //the page containing php script
            type: "post", //request type,
            data: {"command": "add_vote", "course": ifields.find(':input[name="course"]').val(), "lecture": ifields.find(':input[name="lecture"]').val(),
                    "isQuestion": isqorc, "vote": up, "qTime": isqorc ? null : $(this).parents("div.commented-section.mt-2").find("span.time.mb-1.ml-2").text()},
            success: function(result){
                if (result != "1"){
                    window.alert(result);
                }
            }
        });
        
    });
}


function add_q()
{
    var lecture = prompt("הזן מרצה");
    if (lecture == "" || lecture == null) return false;
    
    var course = prompt("הזן קורס");
    if (course == null || course == "") return false;

    course = course.replace('"', "").replace("'", "");
    lecture = lecture.replace('"', "").replace("'", "");
    
    var tag = "1";
    while (!["חובה", "בחירה",""].includes(tag) && tag != null){
        tag = prompt("חובה / בחירה?");
    }
    if (tag == null) return false;
    if (tag == "חובה") tag = "duty";
    if (tag == "בחירה") tag="choose";
    
    $.ajax({
        url:"actions.php", //the page containing php script
        type: "post", //request type,
        data: {"command": "add_question", "tag": tag, "course": course, "lecture": lecture},
        success: function(result){
            if (result == "1"){
                window.alert("השאלה פורסמה");
            } else if (result == "-1") {
                window.alert("השאלה קיימת כבר");
            }
        }
    });
}

function filter(){
    $("div.d-flexx.justify-content-center.row").hide();
    $("div.d-flexx.justify-content-center.row").filter(function() {
        return $(this).find(':input[name="course"]')[0].value.includes($("input.search.course").val()) &&
               $(this).find(':input[name="lecture"]')[0].value.includes($("input.search.lecture").val());
    }).show();
}

function switchMode() { 
    if ($("body").hasClass("dark")){ 
        $("body").attr('class', 'light'); } 
    else{ 
        $("body").attr('class', 'dark'); 
    } 
} 

window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => { 
    if (event.matches) switchMode(); 
});


</script>


</body>
</html>
