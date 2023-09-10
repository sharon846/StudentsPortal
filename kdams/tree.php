<?php 
require_once '../site_manager/pdoconfig.php';

$current = array("a");
$total = array();
$total_names = array();
    
$layers = array();
 
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
} catch (PDOException $pe) {
    die("Could not connect to the database $dbname :" . $pe->getMessage());
}

while (count($current) > 0)
{
    $sql = "SELECT `code`,`name`,`kdams` FROM `Tkdams` WHERE `name` NOT IN ('".implode("','", $total_names)."') ORDER BY `name` ASC";
        
    $result = $GLOBALS['conn']->query($sql);
    $arr = $result->fetchAll(); 

    $current = array_filter($arr, function($tp) use($total_names) { return $tp["kdams"] == "" || count(array_diff(explode(',',$tp["kdams"]),$total_names)) == 0; });

    $current = array_map(function($tp) {
        return [
            "code" => str_replace(".", "", $tp["code"]),
            "name" => $tp["name"],
            "kdams" => $tp["kdams"]
            ];
    }, $current);

    $total = array_merge($total, $current);
        
    $total_names = array_merge($total_names, array_map(function($tp) { return $tp["name"]; }, $total));
    $total_names = array_unique($total_names);
        
    array_push($layers, $current);
}

$layers = json_encode($layers);

?>

<html>
    <head>
        <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

        <style>
            span.course{
                border-width:2px;
                border-style:dashed;
                border-color:inherit;
                padding: 0.5em;
                font-size: 14px;
                margin: 16px;
                margin-bottom: 20px;
                display: inline-block;
            }
            div{
                padding-bottom: 1em;
            }
            div.layer0{
                border-color: #FFA500;
            }
            div.layer1{
                border-color: #FF0000;
            }
            div.layer2{
                border-color: #007700;
            }
            div.layer3{
                border-color: #0000FF;
            }
            div.layer4{
                border-color: #FF69B4;
            }
            div.layer5{
                border-color: #0000c6;
            }
            .arrow{
              
              stroke-width:1; 
              marker-end:url(#markerArrow)
            }
            svg{
                top: 0px;
                position: absolute;
                left: 0px;
                width: 100%;
                height: 200%;
                z-index: -1;
            }
            label.switch {
              position: absolute;
              display: inline-block;
              width: 60px;
              height: 34px;
              left: 10%;
              top: 13px;
            }
            
            label.switch2 {
              top: 55px;
            }
            
            label.switch input { 
              opacity: 0;
              width: 0;
              height: 0;
            }
            .slider {
              position: absolute;
              cursor: pointer;
              top: 0;
              left: 0;
              right: 0;
              bottom: 0;
              background-color: #ccc;
              -webkit-transition: .4s;
              transition: .4s;
            }
            
            .slider:before {
              position: absolute;
              content: "";
              height: 26px;
              width: 26px;
              left: 4px;
              bottom: 4px;
              background-color: white;
              -webkit-transition: .4s;
              transition: .4s;
            }
            
            input:checked + .slider {
              background-color: #2196F3;
            }
            
            input:focus + .slider {
              box-shadow: 0 0 1px #2196F3;
            }
            
            input:checked + .slider:before {
              -webkit-transform: translateX(26px);
              -ms-transform: translateX(26px);
              transform: translateX(26px);
            }
            
            /* Rounded sliders */
            .slider.round {
              border-radius: 34px;
            }
            
            .slider.round:before {
              border-radius: 50%;
            }
            span.switch{
                display: flex;
                top: 20px;
                position: absolute;
            }
            span.switch2{
                top: 62px;
            }
            body.dark{
                color: white;
                background-color: black;
            }
            path{
                fill: #000000;
            }
            body.dark path{
                fill: #ffffff;
            }
            h4{
                direction: rtl;
            }
        </style>
        <!--stroke:rgb(0,0,0);-->
    </head>
    <body>
        <center>
            <span class="switch switch2">dark mode:</span><label class="switch switch2"><input onchange="switchMode()" id="slide" type="checkbox"><span class="slider round"></span></label>
            <span class="switch">full kdams:</span><label class="switch"><input checked id="slide" type="checkbox"><span class="slider round"></span></label>
            <h1>עץ קדמים</h1>
            <h4>חג חוגי כללי: 87 נז קורסי חובה, סמינר 4 נז, פרוייקט 4 נז</h4>
            <h4>29 נקודות קורסי בחירה, 4 נז דרך הרוח / יזמות</h4>
            <h4>חלוקת הניקוד עשויה להשתנות בין התמחויות שונות</h4>
            <div class="layer0"></div>
            <div class="layer1"></div>
            <div class="layer2"></div>
            <div class="layer3"></div>
            <div class="layer4"></div>
            <div class="layer5"></div>
            <svg>
            <defs>
                <marker id="markerArrow" markerWidth="13" markerHeight="13" refX="2" refY="6"
                       orient="auto">
                    <path d="M2,2 L2,11 L10,6 L2,2" />
                </marker>
            </defs>
          </svg>
          
        </center>
        <script>
            
            var data = jQuery.parseJSON(<?php echo "'$layers'"; ?>);
            
            function get_mid(elem)
            {
                var offset = elem.offset();
                var width = elem.width();
                var height = elem.height();
                
                var centerX = offset.left + width / 2;
                var centerY = offset.top + height / 2;
                
                return [centerX, centerY];
            }
            
            function get_rand_color()
            {
                colors = ["#808000", "#FF1493", "#2E8B57", "#FF7F50", "#00FA9A", "#BDB76B", "#00FFFF", "#40E0D0", "#FFD700"];
                return colors[Math.floor(Math.random()*8)];
            }
            
            function showArrows(elem, flgHide)
            {
                var id = flgHide ? elem.id : elem.attr('id');
                
                if (flgHide)
                    $("line").hide();
                $("line[class~="+id+"]").show();
                
                if ($("input#slide").is(":checked"))
                {
                    var ar = $("span[id='"+id+"']").attr('title').split(',');
                    if (ar[0] != "null")
                    {
                        ar.forEach(crs => {
                            var elee = $("span").filter(function(){ return $(this).text() == crs; })
                            showArrows(elee, false);
                        });
                    }
                }
                
                //can take the span with elem.id, and for each of the kdams, call showArrows, with flg that tells not to hide arrows
            }
            
            window.onload = function()
            {
                $("div[class^='layer']").each(function(index){
                    var div = $(this);
                    $.each(data[index], function(key,value) {
                        div.append('<span title="'+value.kdams+'" onmouseover="showArrows(this,true)" id="s'+value.code+'" class="course">'+value.name+'</span>'); 
                    });
                });
                
                $("div[class^='layer']").each(function(index){
                    var div = $(this);
                    $.each(data[index], function(key,value) {
                        
                        $("span[class='course']").each(function(){
                            if (value.kdams != null && value.kdams.split(',').includes($(this).text()))
                            {
                                var newLine = document.createElementNS('http://www.w3.org/2000/svg','line');
                                newLine.setAttribute('class','arrow '+div.attr('class')+' s'+value.code);
                                newLine.setAttribute('x1',get_mid($(this))[0]);
                                newLine.setAttribute('y1',get_mid($(this))[1]);
                                newLine.setAttribute('x2',get_mid($("span#s"+value.code))[0]);
                                newLine.setAttribute('y2',get_mid($("span#s"+value.code))[1]);
                                newLine.setAttribute('stroke',get_rand_color());
                                $("svg").append(newLine);
                            }
                        })
                    });
                });
                $("line").hide();
                
                if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)
    		        switchMode();
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