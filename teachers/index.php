<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="UTF-8">


<link rel="stylesheet" href="../header.css">

<script src="http://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
<script src="http://canvasjs.com/assets/script/canvasjs.min.js"></script>

<script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<link rel="stylesheet" href="http://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<script src="http://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>


input.search{
    border-radius: 999px;
    border-width: 0px;
    direction: rtl;
    background-color: #eee;
    margin-top: 20px;
    text-align: center; 
}


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
    line-height: 3px;
    white-space:nowrap;
    display: inline-block;
    overflow: auto;
}

.cpp{
    background-color: blue;
}

.c{
    background-color: green;
}

.algebra1{
    background-color: red;
}

.calculus2{
    background-color: yellowgreen;
}

.algebra2{
    background-color: gray;
}

.compilers{
    background-color: darkcyan;
}

.ds{
    background-color: coral;
}

.logic{
    background-color: brown;
}

.hardware{
    background-color: cornflowerblue;
}

.prob{
    background-color: blueviolet;
}

.lc3{
    background-color: darkblue;
}

.graphics{
    background-color: darkviolet;
}

.python{
    background-color: limegreen;
}

.discrete{
    background-color: fuchsia;
}

.algo {
    background-color: lightseagreen;
}

.models{
    background-color: orangered;
}

.dss{
    background-color: cornflowerblue;
}

.os{
    background-color: lightgray;
}

.uml{
    background-color: aqua;
}

.comments {
    text-decoration: underline;
    text-underline-position: under;
    cursor: pointer
}

.info {
    background-color: darkblue;
}

.hit-voting:hover {
    color: blue
}

.hit-voting {
    cursor: pointer
}

.ml-3, .mx-3 {
    margin-right: 2rem!important;
}

div.row{
    margin-top: 3rem;
}

div.comment-text-sm{
    right: 20px;
    position: relative;
}

.d-flexx{
    display: flex;
}

span.filter{
    cursor: pointer;
}
</style>

</head>
<body>

<header id="header">
    <div style="width: 100%; height: 100%">
        <div style="width: 100%; height: 62%; top: 0%; position: absolute;">
            <a class="hover" href="register.php" title="Register" style="position: absolute; top: 25%; left: 5%; width: 40px; height:30px;">
                <img width="100%" src="../img/+.png"/>
            </a>
        
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
                לחצו להרשמה
            </h4>
        </div>
        <div style="position: absolute;right: 3.3%;height: 30%;color: white;top: 70%;">
            <h4 class="dl hover" title="Info" style="direction: rtl; font-size: 1.1rem; line-height: 1.25rem;">
                <?php $ent = file_get_contents("log"); file_put_contents("log", intval($ent)+1); echo $ent; ?> כניסות
            </h4>
        </div>
    </div>
</header>

<center style="top: 100px;position: relative">
    <input type="text" oninput="filter()" placeholder="סנן לפי מורה" class="search lecture"/>
    <input type="text" oninput="filter()" placeholder="סנן לפי קורס" class="search course"/>
</center>

<div class="container mt-5 mb-5">
    <?php
        require_once '../site_manager/pdoconfig.php';
        function get_tag_heb($tag)
        {
            if ($tag == "calculus2") return "חדוא 2";
            if ($tag == "cpp") return "תכנות מונחה עצמים";
            if ($tag == "c") return "מבוא למדמח";
            if ($tag == "algebra2") return "אלגברה ב";
            if ($tag == "algebra1") return "אלגברה לינארית א";
            if ($tag == "compilers") return "מבנה מהדרים";
            if ($tag == "ds") return "מבני נתונים";
            if ($tag == "logic") return "מבוא ללוגיקה";
            if ($tag == "hardware") return "מבוא לחמרה";
            if ($tag == "lc3") return "שפות סף";
            if ($tag == "graphics") return "גרפיקה ממוחשבת";
            if ($tag == "discrete") return "מתמטיקה דיסקרטית";
            if ($tag == "models") return "מודלים חישוביים";
            if ($tag == "algo") return "אלגו";
            if ($tag == "dss") return "מבני נתונים מתקדמים";
            if ($tag == "prob") return "שיטות הסתברותיות";
            if ($tag == "uml") return "הנדסת תוכנה";
            if ($tag == "info") return "תורת האינפורמציה";
            return $tag;
        }
    
        $quesPattern = explode('commenttagseperator', file_get_contents("q.html"))[0];
        $commentPattern = explode('commenttagseperator', file_get_contents("q.html"))[1];
        
        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        } catch (PDOException $pe) {
            die("Could not connect to the database $dbname :" . $pe->getMessage());
        }
        
        $sql  = "SELECT * FROM Tteachers WHERE `hidden`=0 ORDER BY `rank` DESC;";     //
        $result = $conn->query($sql);
        $rows = $result->fetchAll();

        foreach ($rows as $row)
        {
            $toprint = str_replace("qrank", $row["rank"], $quesPattern);
            
            $lecture = $row["lecture"];
            $phone = $row["phone"];
            $lecture = "http://wa.me/$phone?text=היי%20$lecture,%20אני%20מעוניין%20בשיעורים%20פרטיים";
            $lecture = "<a href='$lecture'>".$row["lecture"]."</a>";
            $toprint = str_replace("qcontent", $lecture, $toprint);
            
            $tags = explode(',', $row["tag"]);
            $tags_print = "";
            $tags_heb = array();
            foreach ($tags as $tag){
                $tagheb = get_tag_heb($tag);
                array_push($tags_heb, $tagheb);
                $tags_print .= "<span class='$tag mr-1'>$tagheb</span>";
            }
            
            $toprint = str_replace("qlecture", $row["lecture"], $toprint);
            $toprint = str_replace("qcourse", implode(",", $tags_heb), $toprint);
            $toprint = str_replace("qtag", $tags_print, $toprint);
            
            $content = $row["content"];
            $content = $row["study"]."<br/>".$row["content"];
            
            $commentContext = str_replace("ccontent", $content, $commentPattern);

            $toprint = str_replace("commentssection", $commentContext, $toprint);
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
            data: {"command": "add_vote", "lecture": ifields.find(':input[name="lecture"]').val(), "vote": up},
            success: function(result){
                if (result != "1"){
                    window.alert(result);
                }
            }
        });
        
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
