<html>
<head>
<title>Manage Kdams</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="UTF-8">
<meta http-equiv="Cache-Control" content="max-age=0, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0" />

<script src="http://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
<script src="http://code.jquery.com/jquery-1.12.4.js"></script>
<script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<style>

* {
    font-family: Arial;
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

table{
    color: white;
    width: 100%;
    padding-top: 50px;
    font-size: 25px;
}

tr>td {
  padding-bottom: 1em;
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

select{
    width: 50%;
    direction: rtl;
    background-color: lightslategrey;
    color: white;
    border: 6px solid transparent;
    font-size: 14px;
}

input{
    background: transparent;
    border: none;
    color: white;
    font-size: 25px;
    text-align: center;
    font-family: auto;
}

input#pts{
    width: 15px;
}

input#ids{
    width: 35px;
}

input#note{
    width: 160px;
}

img.line{
    content:url("../img/-.png");
    width: 17px;
    position: absolute;
    left: 40px;
    padding-top: 7px;
}

img.add{
    content:url("../img/+.png");
    width: 17px;
    position: relative;
    left: 80px;
    padding-top: 4px;
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

input.year{
    background-color: transparent;
    color: white;
    width: 100px;
    top: 30px;
    font-size: 3em;
}

</style>
</head>

<body>

    <center>
        <h1>מערכת ניהול קדמים</h1>
        <input onchange="update_year();" class="year" type="text" value=<?php echo "'".file_get_contents("../data/kdams_year")."'"; ?>/>
        <h3 style="color: white;"><u><a onclick="change_course_name()" style="color: white;">click here to change globally course name</a></u></h3>
        <h3 style="color: white;"><u><a onclick="change_lecture_name()" style="color: white;">click here to change globally lecture name</a></u></h3>
        <h3 style="color: white;">dbclick on course name to change its picture</h3>
        <input hidden type='file' name='fileToUpload' id='formImage' accept="image/jpeg"/>
        <table>
            <thead>
                <tr>
                    <td>name</td>
                    <td>lecture</td>
                    <td>points</td>
                    <td>semesters</td>
                    <td>note</td>
                    <td>dependencies</td>
                </tr>
            </thead>
            <tbody>
                <?php
                    require_once '../site_manager/pdoconfig.php';
 
                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                    } catch (PDOException $pe) {
                        die("Could not connect to the database $dbname :" . $pe->getMessage());
                    }
                    
                    $sql = "SELECT * FROM `Tkdams`";
                    $result = $conn->query($sql);
                    $list = $result->fetchAll(); 
                    
                    //$all_courses = simplexml_load_file("../data/courses.xml")->Course;
                    //$all_courses = json_decode(json_encode($all_courses), true);
                    
                    foreach ($list as $course) {
                        if ($course["name"] == "מפגש חוגי") continue;
                        
                        echo "<tr>";
                        /*echo "<td class='middle'><select>";
                        foreach ($all_courses as $ccrs){
                            if ($ccrs == $course["name"])
                                echo "<option selected value='$ccrs'>".$ccrs."</option>";
                            else
                                echo "<option value='$ccrs'>".$ccrs."</option>";
                        }
                        echo "</select></td>";*/
                        
                        echo "<td class='thin'><img class='line'/><input id='name' class='big' type='text' value='".$course["name"]."'/></td>";
                        echo "<td class='thin'><input id='lecture' class='big' type='text' value='".$course["lecture"]."'/></td>";
                        echo "<td class='thin'><input id='pts' type='text' value='".$course["pts"]."'/></td>";
                        echo "<td class='thin'><input id='ids' type='text' value='".implode(',', str_split($course["ids"]))."'/></td>";
                        echo "<td class='thin'><input id='note' type='text' value='";
                        echo $course["note"] == "" ? "None" : $course["note"];
                        echo "'/></td>";
                        
                        $kdams = $course["kdams"] == "" ? array() : explode(',', $course["kdams"]);
                        
                        if (count($kdams) > 0){
                             echo "<td class='middle'><select>";
                            foreach ($kdams as $depen){
                                echo "<option value='$depen'>".$depen."</option>";
                            }
                            echo "<option>הוספה</option>";
                            echo "<option>מחיקה</option>";
                            echo "</select></td>";
                        }
                        else{
                            echo "<td class='thin'><img class='add'/>None</td>";
                        }
                        echo "<input type='hidden' value='".$course["year"]."'/>";
                        echo "<input type='hidden' value='".$course["link"]."'/>";
                        echo "<tr/>";
                    }
                
                ?>
            </tbody>
        </table>
        <button id="add-line">Add New Line</button>
        <button onclick="save()">Save</button>
        <button onclick="restore()">Restore</button>
    </center>

</body>

<script>
var arr = [];
var new_crs = [];

var curr = "";

window.onload = function(){
    
    
    $("input#name").each(function(){
        arr.push($(this).val());
    });
    
    $(document).on("dblclick", "input#name" , function() {
        $("input#formImage").click();
        curr = $(this).val();
    });
    
    $("#formImage").change(function() {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            
            var img = new Image();      
            img.src = e.target.result;

            img.onload = function () {
                var w = this.width;
                var h = this.height;
                
                if (w == h && w <= 300)
                {
                    $.ajax({
                        url:"save_kdams.php", //the page containing php script
                        type: "post", //request type,
                        data: {"command": "add_image", "data": e.target.result, "course": curr},
                        success:function(result){
                            if (result == "") 
                                window.alert("saved");
                            else 
                                window.alert(result);
                                    
                            location.reload();
                        }
                    });
                }
                else
                    window.alert("image size must be WxW where W<=300");
            }
        }
        
        reader.readAsDataURL(this.files[0]); // convert to base64 string
    });
    
    $(document).on("mouseover", "td" , function() {
        $("input#name").css('color', 'white');
        $(this).parent().find(":input").first().css('color', 'orange');
    });

    $("input.big").each(function() {
        $(this).width(11.5 * Math.max(1, $(this).val().length));
    })
    
    $(document).on("input", "input.big" , function() {
        $(this).width(11.5 * Math.max(1, $(this).val().length));
    });
    
    $(document).on("click", "img.line" , function() {
        var del = confirm("Are you sure you want to delete course?");
        if (del) {
            var val = $(this).parent().parent().children()[7].value;
            const index = new_crs.indexOf(val);
            if (index > -1) { 
              new_crs.splice(index, 1);
            } else{
                $.ajax({
                    url:"save_kdams.php", //the page containing php script
                    type: "post", //request type,
                    data: {"command": "delete_course", "data": val },
                    success:function(result){
                        if (result == "1") 
                            window.alert("saved");
                        else 
                            window.alert(result);
                            
                        location.reload();
                    }
                });
            }
            $(this).parent().parent().remove();
        }
    })
    
    $(document).on("click", "img.add" , function() {
        $(this).parent().className = "middle";
        var p = $(this).parent();
        $(this).parent().html("<select><option>הוספה</option><option>מחיקה</option></select>");
        p.find("select").last().prop('selectedIndex', -1);
    })

    $("button#add-line").on('click', function(){
        
        var year = prompt("for specific year?");
        var link = prompt("course link", "50620094");
        
        if (link == "" || link == null) return false;
        
        var text = "<tr><td class='thin'><img class='line'/><input id='name' class='big' type='text' value='name'/></td>" + 
                    "<td class='thin'><input class='big' type='text' value='lecture'/></td>" + 
                    "<td class='thin'><input id='pts' type='text' value='0'/></td>" + 
                    "<td class='thin'><input id='ids' type='text' value='a'/></td>" +
                    "<td class='thin'><input id='note' type='text' value='None'/></td>" + 
                    "<td class='thin'><img class='add'/>None</td><input type='hidden' value='"+year+"'/><input type='hidden' value='"+link+"'/><tr/>";
        
        new_crs.push(link);
        $("tbody").append(text);
    })
    
    $(document).on("change", "select" , function() {
            var opt = $(this).children("option:selected");
            if (opt.val() == "הוספה"){
                var crs = prompt("מהו שם הקורס אותו תרצה להוסיף?");
                if (crs != null) {
                    if (!arr.includes(crs)) {
                        window.alert("course doesnt exists");
                        return;
                    }
                    opt.before("<option value='"+crs+"'>"+crs+"</option>");
                }
                $(this).prop('selectedIndex', $(this).children().length - 3);
            } else if (opt.val() == "מחיקה"){
                var ind = prompt("select removal index");
                if (ind != null && ind < $(this).children().length - 2) {
                    $(this).children().eq(ind).remove();
                }
                $(this).prop('selectedIndex', -1);
            }
        });
}

function update_year()
{
    var val = $("input.year").val();
    var isExecuted = confirm("do you want to change year to " + val + "?");
    if (isExecuted)
    {
        $.ajax({
            url:"save_kdams.php", //the page containing php script
            type: "post", //request type,
            data: {"command": "year", "data": val},
                success:function(result){
                    if (result == "") 
                        window.alert("saved");
                    else 
                        window.alert(result);
                        
                    location.reload();
               }
            });
    }
}

/*
each course has to be a,b lec1,lec2 or non ',' at all

delete courses with new course, lecture, pts 0 (except mifgash hugi), selects with only add / del are "None"
None becomes ""

*/

function save(){
    
    var success = true;
    var arr2 = [];
    $("input#name").each(function(){
        if (arr2.includes($(this).val())) {
            window.alert($(this).val() + " appears twice");
            success = false;
            return false;
        }
        arr2.push($(this).val());
    });

    if (!success) return false;
    
    var output = "";
    var courses = [];
    var lectures = [];
    
    $("tr").each(function(){
        
        var inputs = $(this).find(":input");
        if (inputs.length == 0) return;
        var values = [];
        
        inputs.each(function(){ 
            if ($(this).prop("tagName") != "SELECT")
                values.push($(this).val()); 
        });
        
        if (values[0] == "" || values[1] == "" || values[2] == "" || values[3] == "") success = false;
        if (values[0] == "name" || values[1] == "lecture") success = false;
        if (values[2] == "0") success = false;
        if (values[3].indexOf('a') == -1 && values[3].indexOf('b') == -1 && values[3].indexOf('c') == -1) success = false;
        if ((values[1].match(/,/g) || []).length != (values[3].match(/,/g) || []).length) success = false;
        
        if (!success){
            window.alert("syntax error in " + values[0]);
            return false;
        }
        
        if (values[4] == "None") values[4] = "";
        values[3] = values[3].replace(",","");
        
        var depens = [];
        if ($(this).find("option").length > 2){
            $(this).find("option").each(function(){depens.push($(this).val());});
            depens.pop();
            depens.pop();
        }
        values.push(depens);
        
        var data = "UPDATE `Tkdams` SET `name`='" + values[0] + "',`lecture`='" + values[1] + "',`ids`='" + values[3].replace(",","") + "',`pts`='" + values[2] + "',`year`='" + values[5] + "',`note`='" + values[4] + "'";
        if (new_crs.includes(values[6]))
            data = "INSERT INTO `Tkdams`(`link`, `name`, `lecture`, `ids`, `pts`, `year`, `note`, `kdams`) VALUES ('" + values[6] + "','" + values[0] + "','" + values[1] + "','" + values[3].replace(",","") + "','" + values[2] + "','" + values[5] + "','" + values[4] + "'";
        
        if (values[7].length > 0){
            if (new_crs.includes(values[6])) data += ",'";
            else data += ", kdams='";
            for (var i = 0; i < values[7].length; i++)
                data += values[7][i] + ",";
            data = data.slice(0, -1);
            data += "'";
            if (new_crs.includes(values[6])) data += ");";
        }
        else if (new_crs.includes(values[6]))
            data += ",NULL);";
        
        if (!new_crs.includes(values[6]))
            data += " WHERE link='" + values[6] + "';";
            
        output += data;
        
        data = values[1].split(',')
        lectures.push(...data);
        courses.push(values[0]);
    });
    
    if (success){
        
        output = output.slice(0,-1);
        lectures = lectures.filter((v, i, a) => a.indexOf(v) === i);
        
        $.ajax({
            url:"save_kdams.php", //the page containing php script
            type: "post", //request type,
            data: {"data": output, "courses": JSON.stringify(courses), "lectures": JSON.stringify(lectures)},
            success:function(result){
                if (result == "") 
                    window.alert("saved");
                else 
                    window.alert(result);
                    
                location.reload();
            }
        });
    }
}

function change_course_name(){
    var old = prompt("enter old course name");
    if (old == null || old.trim().length == 0) return;
    if (!arr.includes(old)){
        window.alert("no such course");
        return;
    }
    
    var new_name = prompt("enter new course name");
    if (new_name.trim().length == 0 || new_name == null){
        window.alert("empty course is not possible");
        return;
    }
    
    $.ajax({
        url:"save_kdams.php", //the page containing php script
        type: "post", //request type,
        data: {"command": "change_course", "old": old, "data": new_name},
        success:function(result){
            if (result == "") 
                window.alert("saved");
            else 
                window.alert(result);
                
            location.reload();
        }
    });
}

function change_lecture_name(){
    var old = prompt("enter old lecture name");
    if (old.trim().length == 0 || old == null){
        window.alert("no such lecture");
        return;
    }
    
    var new_name = prompt("enter new lecture name");
    if (new_name.trim().length == 0 || new_name == null){
        window.alert("empty lecture is not possible");
        return;
    }
    
    $.ajax({
        url:"save_kdams.php", //the page containing php script
        type: "post", //request type,
        data: {"command": "change_lecture", "old": old, "data": new_name},
        success:function(result){
            if (result == "") 
                window.alert("saved");
            else 
                window.alert(result);
                
            location.reload();
        }
    });
}

function restore(){
    var cor = confirm("Are you sure? you will lose all changes");
    if (cor) location.reload();
}


</script>


</html>