<?php

$sem = isset($_GET['sem']) ? $_GET['sem'] : (isset($_POST['sem']) ? $_POST['sem'] : '$$');
$sem = htmlspecialchars($sem, ENT_QUOTES);
$input = isset($_POST['hidden-input']) ? $_POST['hidden-input'] : "" ;                        //from my choicefreak
$input = htmlspecialchars($input, ENT_QUOTES);

$temp = intval(date('m'));
if ($sem == "$$"){
    $sem = "b";
    if ($temp < 3 || $temp > 9){
        $sem = "a";
    }
}

$catalog_year = intval(file_get_contents("../data/kdams_year"));

$sems = array("a", "b");
unset($sems[ord($sem) - 97]);
$sems = array_values($sems);

$data = file_get_contents("$sem.html");
$ind = strpos($data, "<table");
$data = substr($data, $ind);
$ind = strpos($data, "]]>");
$data = substr($data, 0, $ind);

$doc = new DOMDocument();
@$doc->loadHTML($data);

//get year of exams board
$catalog_year = $doc->getElementById("aaaa.OutputExamsView.DefaultTextView")->nodeValue;
$pattern = "/\b\d{4}\b/";

preg_match_all($pattern, $catalog_year, $matches);
$catalog_year = intval($matches[0][0])+1;

//Image tag
$rows = $doc->getElementById("aaaa.OutputExamsView.TableGui-contentTBody");

$list = array();

for ($j = 0; $j < $rows->childNodes->length-1; $j++) {
    
    $tt = $doc->getElementById("aaaa.OutputExamsView.courseName_editor.$j")->nodeValue;
    $tt = str_split($tt);
    $tt = array_filter($tt, function($t) { return ord($t) < 194; });
    
    $tt = array_values($tt);
    
    for ($i = 0; $i < count($tt); $i++){
        if (ord($tt[$i]) == 151 && ($i == 0 || ord($tt[$i-1]) != 215)){
            $tt[$i] = chr(215);
        }
    }
    
    $ind = count($tt);
    $c = 0;
    while ($c < 2){
        if ($tt[--$ind] == '-') break;
        if ($tt[$ind] == ' ') $c++;
    }
    
    $tt = array_splice($tt, 0, $ind);
    
    if (end($tt) == '-')
        $tt = array_splice($tt, 0, -1);
    
    if (end($tt) == ' ')
        $tt = array_splice($tt, 0, -1);
        
    $course = implode("", $tt);
    $code = $doc->getElementById("aaaa.OutputExamsView.courseID_editor.$j")->nodeValue;
    array_push($list, ["code" => $code, "course" => $course]);
}

$tempArray = [];
    
// Filter out duplicates while keeping the first occurrence
$resultArray = array_filter($list, function ($item) use (&$tempArray) {
    $key = $item['course'];
    if (!isset($tempArray[$key]) && !str_starts_with($item['code'], '203.8')) {
        $tempArray[$key] = true;
        return true;
    }
    return false;
});

$courses_names = array_map(function ($item) {
    return $item["course"];
}, $resultArray);

$resultArray = array_values($resultArray);
$courses_names = array_values($courses_names);

$list = base64_encode(json_encode($resultArray));
$names = base64_encode(json_encode($courses_names));
?>

<!DOCTYPE html>
<html>
<head>
<title>חישוב בחינות</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="UTF-8">
<meta http-equiv="Cache-Control" content="max-age=0, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0" />

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="../kdams/style.css">
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

<header id="header">
    <div style="width: 100%; height: 100%">
        <div style="width: 100%; height: 62%; top: 0%; position: absolute;">
            <form id='frm' method="POST" onsubmit="return false">
                
            </form>
            
            <div id="mode">
               <div class="transform">
                    <i id="lamp" class="fa fa-lightbulb-o"></i>
                </div>
            </div>
        </div>
        <div style="position: absolute;left: 4.8%;height: 60%;color: white;top: 45%;">
            <h4 class="dl hover" title="Info" style="font-size: 1.1rem; line-height: 1.25rem;">
                <a href="?sem=a">semester a</a>
            </h4>
        </div>
        <div style="position: absolute;right: 4.8%;height: 60%;color: white;top: 45%;">
            <h4 class="dl hover" title="Info" style="font-size: 1.1rem; line-height: 1.25rem;">
                <a href="?sem=b">semester b</a>
            </h4>
        </div>
    </div>
</header>

<center><h1 style="top: 94px;position: relative;">exams <?php echo ($catalog_year-1).'-'.$catalog_year; ?> semester <?php echo $sem;?></h1></center>
<form method="post" action="output.php">
<div class="bar-container">
    <input type="text" id="lname" name="lname" placeholder="רשמו את הקורסים שלכם. לרשימה המלאה, השאירו ריק ולחצו על הכפתור"/>
    <input type="submit" onclick="return validate()" value="submit"/>
    <input type="hidden" id="hidden-input" name="hidden-input">
    <input type="hidden" id="sem" name="sem" value=<?php echo "'$sem'"; ?>/>
    
    <div style="padding-bottom:20px;position: relative; direction: rtl;">
        <label style="position: relative;width: 100%;">החילו להקליד שם קורס.</label><br/>
        <label style="position: relative;width: 100%;">כדי לבטל בחירת קורס, פשוט לחצו עליו.</label><br/>
    </div>
    
    <div id='selected-list'>
           
    </div>
</div>
</form>

<script>
    var courses = JSON.parse(atob(<?php echo "'$list'" ?>));  
    var names = JSON.parse(atob(<?php echo "'$names'" ?>)); 
    window.onload = function(){
        
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)
    		    switchMode();
    	
        $("div#mode").click(switchMode);
        
        $( "#lname" ).autocomplete({
            source: names
        });

        $("ul#ui-id-1").on('click', 'li', clickEvt);
        $("#lname").keyup(checkClick);
        
        $("input.search").css('top', '0');
        $("a#anyways").click(function() {
            $("div.row").show();
            $("div#tohide").hide();
        });
        
        var input = <?php echo "'$input'"; ?>;
        input = input.split(',');
        input.forEach(inp => {
           handleItemSelected(inp); 
        });
    }
  
    function checkClick(e){
        if (e.keyCode == 13)
            clickEvt(e);
    }
  
    function clickEvt(e){
      
      var val = e.target.innerText == "" ? e.target.value : e.target.innerText;
      e.target.value = "";
      e.target.innerText = "";
      
      handleItemSelected(val);
  }
  
  function search(text){
     res = "";
     for (var i = 0; i < courses.length; i++){
         if (courses[i].course === text) {
            return courses[i].code;
        }
     }
     
    return "";
  }
  
  
  function handleItemSelected(text)
  {
    if (text.length > 0 && names.includes(text))
    {
        names.remove(text);
        document.getElementById("hidden-input").value += search(text)+",";
        
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
    if (!names.includes(e.target.innerText)) names.push(e.target.innerText);
    document.getElementById("hidden-input").value = document.getElementById("hidden-input").value.replace(e.target.innerText+",", "");
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
