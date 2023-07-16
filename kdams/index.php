<?php Header("Cache-Control: max-age=0, min-fresh=10, must-revalidate"); ?>
<!DOCTYPE html>
<html>
<head>
<title>חישוב קדמים</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="UTF-8">
<meta http-equiv="Cache-Control" content="max-age=0, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0" />

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


<script src="http://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
<script src="http://code.jquery.com/jquery-1.12.4.js"></script>
<script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="../header.css">
<style>

label{
    color: black;
}

body.dark .bar-container{
    background-color: darkslateblue;
}

body.dark input#lname{
    color: white;
}

body.dark .button-selected{
    background-color: thistle;
}

a{
    color: white;
}

a.result{
    color: black;
}

body.dark a.result{
    color:white;
}

</style>


</head>

<body>

<?php

include 'ds.php';
$chosen = array();

if (isset($_POST["hidden-input"]))
{
    $chosen = explode(",", $_POST["hidden-input"]);
    $chosen = array_filter($chosen); 
    
    $temp = generateMatchCourses($chosen);
    
    $finalArr = $temp[0];
    $pts = $temp[1];
    if ($pts == NULL) $pts = 0;
}

$mode = isset($_GET['mode']) ? $_GET['mode'] : 1;
$mode_arr = array("קדמים לקורס", "קורסים השנה", "?מה הקורס חוסם");

$catalog_year = intval(file_get_contents("../data/kdams_year"));

?>


<header id="header">
    <div style="width: 100%; height: 100%">
        <div style="width: 100%; height: 62%; top: 0%; position: absolute;">
            <form id='frm' method="POST" onsubmit="return false">
                <span class='ad'><a href="tree.php">לחצו כאן לעץ הקדמים</a></span>
            </form>
            
            <div id="mode">
               <div class="transform">
                    <i id="lamp" class="fa fa-lightbulb-o"></i>
                </div>
            </div>
        </div>
        <div style="position: absolute;left: 4.8%;height: 60%;color: white;top: 45%;">
            <h4 class="dl hover" title="Info" style="font-size: 1.1rem; line-height: 1.25rem;">
                <?php echo '<a href="?mode='.(int)($mode == 0).'">'.$mode_arr[$mode == 0]; ?></a>
            </h4>
        </div>
        <div style="position: absolute;right: 4.8%;height: 60%;color: white;top: 45%;">
            <h4 class="dl hover" title="Info" style="font-size: 1.1rem; line-height: 1.25rem;">
                <?php echo '<a href="?mode='.(int)(2 - ($mode == 2)).'">'.$mode_arr[2 - ($mode == 2)]; ?></a>
            </h4>
        </div>
    </div>
</header>

<center><h1 style="top: 94px;position: relative;">קורסים <?php echo ($catalog_year-1).'-'.$catalog_year; ?></h1></center>
<form method="post">
<div class="bar-container">
    <input type="text" id="lname" name="lname" placeholder="הזן קורסים שעשית"/>
    <?php if ($mode == 1) {?>
    <input type="submit" onclick="return validate()" value="submit"/>
        <?php } ?>
    <input type="hidden" id="hidden-input" name="hidden-input">
    
    <div style="padding-bottom:20px;position: relative; direction: rtl;">
        <?php if ($mode == 1) { ?>
                <label style="position: relative;width: 100%">בחרו קורסים שעשיתם, ותגלו למטה מה תוכלו לקחת השנה</label></br />
                <label style="position: relative;width: 100%;">התחילו להקליד שם קורס. לנוחיות - יש גם שנה א, שנה ב, שנה ג</label><br/>
                <label style="position: relative;width: 100%;">כדי לבטל בחירת קורס, פשוט לחצו עליו.</label><br/>
        <?php } if ($mode == 0) { ?>
            <label style="position: relative;width: 100%;">הזן קורס וגלה מה הקדמים הדרושים כדי לקחת אותו</label><br/>
        <?php } if ($mode == 2) { ?>
            <label style="position: relative;width: 100%;">הזן קורס וגלה את מה הוא חוסם בהמשך הלימודים</label><br/>
        <?php } ?>
    </div>
    
    
    <div id='selected-list'>
           
    </div>
</div>
</form>

<?php
    $block = array();
    if (date('m') < "08"){
        $block = array('a');
            if (date('m') > "05"){
            $block = array('a', 'b');
        }
    }
    
    if (isset($_POST['hidden-input']))
    {
        echo "<div class='row'><h2 dir='rtl'>יש לכם ".$pts." נקודות! הקורסים שתוכלו לקחת השנה:</h2><h5 dir='rtl'>לחצו על הפלוס כדי להוסיף את הקורס לרשימה</h5></div>";
        foreach ($finalArr as $semester=>$courses)
        {
            if (in_array($semester, $block))
                echo "<div hidden class='row'>";
            else
                echo "<div class='row'>";
                
            echo "<h2 dir='rtl'>Semester ".$semester."</h2>";
        
            foreach ($courses as $course)
                echo "<h5 dir='rtl'><img style='position: relative; width: 15px; left:6px; top: 3px' onclick='addOutputCourse(this)' src='../img/+.png' />$course</h5>";
                
            echo "</div>";
        }

        if (count($block))
        {
            echo "<div id='tohide' class='row'>";
            echo "<h2 dir='rtl'>יש תוצאות בסמסטרים שלא רלונטיים בשנת הלימודים הנוכחית</h2>";
            echo "<h5 dir='rtl'><a class='result' href='#' id='anyways'>לחץ כאן אם בכל זאת תרצה לראות</a></h5>";
            echo "</div>";
        }
        
        echo "<br/><br/>";
    }
    
?>

<script>
    var courses = <?php echo '["' . implode('", "', getAllCoursesList()) . '"]' ?>;
    
    window.onload = function(){
        
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)
    		    switchMode();
    	
        $("div#mode").click(switchMode);
        
        $( "#lname" ).autocomplete({
            source: courses
        });

        var chosen = <?php echo '["' . implode('", "', $chosen) . '"]' ?>;
        chosen.forEach(elem => handleItemSelected(elem));
        
        $("ul#ui-id-1").on('click', 'li', clickEvt);
        $("#lname").keyup(checkClick);
        
        $("input.search").css('top', '0');
        $("a#anyways").click(function() {
            $("div.row").show();
            $("div#tohide").hide();
        });
    }
  
    function findBlocking(val1){
        $.ajax({
            url:"ds.php", //the page containing php script
            type: "post", //request type,
            data: {"blocking": val1},
            success:function(result){
                var arr = JSON.parse(result);
                window.alert(arr);
           }
        });
    }
  
    function checkClick(e){
        if (e.keyCode == 13)
            clickEvt(e);
    }
  
    var mode = <?php echo "'$mode'";?>;
  
    function clickEvt(e){
      
      var val = e.target.innerText == "" ? e.target.value : e.target.innerText;
      e.target.value = "";
      e.target.innerText = "";
      
      if (mode == 1 || mode == 0)
      {
        $.ajax({
            url:"ds.php", //the page containing php script
            type: "post", //request type,
            data: {"course": val},
            success:function(result){
                var arr = JSON.parse(result);
                if (mode == 0){
                    arr.shift();
                    window.alert(arr);
                }
                else
                {
                    arr.forEach(elem => handleItemSelected(elem));
                    
                    //if (val.startsWith("שנה")){
                    //    courses.remove(val);
                    //    courses.remove("שנה א");
                    //}
                }
           }
        });
      }
      if (mode == 2)
      {
          findBlocking(val);
      }
  }
  
  function addOutputCourse(elem)
  {
      handleItemSelected(elem.parentNode.innerText.split(" -")[0]);
   //   $('html, body').animate({ scrollTop: 0 }, 'fast');
  }
  
  function handleItemSelected(text)
  {
    if (text.length > 0 && courses.includes(text) && text != "מפגש חוגי")
    {
        courses.remove(text);
        document.getElementById("hidden-input").value += text+",";
            
        var buttonx = document.createElement("button");
        buttonx.setAttribute("class", "button-selected");
        buttonx.innerText = text;
        buttonx.setAttribute("id", text);
        buttonx.addEventListener("click", buttondbclick);
        if (document.getElementById("selected-list").children.length > 4)
            buttonx.style.marginTop = (8 * (document.getElementById("selected-list").children.length / 5)) + "px";
        if (isMobile)
            buttonx.style.width = (text.length * 8.7) + "px";
        else
            buttonx.style.width = (text.length * 1.2) + "%";
        document.getElementById("selected-list").appendChild(buttonx);
        document.getElementById("lname").value = "";
    }
  }
  
  function validate()
  {
      return true;
      //for future use
  }


function buttondbclick(e)
{
    document.getElementById(e.target.innerText).remove();
    if (!courses.includes(e.target.innerText)) courses.push(e.target.innerText);
    document.getElementById("hidden-input").value = document.getElementById("hidden-input").value.replace(e.target.innerText+",", "");
    if (document.getElementById("selected-list").children.length == 0){
        if (!courses.includes("שנה ג")) courses.unshift("שנה ג");
        if (!courses.includes("שנה ב")) courses.unshift("שנה ב");
        if (!courses.includes("שנה א")) courses.unshift("שנה א");
        
    }
}

var isMobile = false; //initiate as false
// device detection
if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) 
    || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) { 
    isMobile = true;
}

if (isMobile){
    document.getElementById("selected-list").style.width = "100%";
    document.getElementById("selected-list").style.left = "0%";
    document.getElementsByClassName("bar-container")[0].style.width = "110%";
}

Array.prototype.remove = function() {
    var what, a = arguments, L = a.length, ax;
    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
};

Element.prototype.remove = function() {
    this.parentElement.removeChild(this);
}
NodeList.prototype.remove = HTMLCollection.prototype.remove = function() {
    for(var i = this.length - 1; i >= 0; i--) {
        if(this[i] && this[i].parentElement) {
            this[i].parentElement.removeChild(this[i]);
        }
    }
}

function switchMode()
	{
	    if ($("body").hasClass("dark")){
	        $("body").attr('class', 'light');
	    }
	    
	    else{
	        $("body").attr('class', 'dark');
	    }
	}
    	
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => { 
    	
        if (event.matches)
    	   switchMode();
    });


</script>


</body>
</html>
