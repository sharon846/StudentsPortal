<html>
<head>
<title>Manage Kdams</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="UTF-8">
<meta http-equiv="Cache-Control" content="max-age=0, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0" />

<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

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

.overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1;
}
.popup {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 80%;
    max-width: 400px;
    background-color: #fff;
    padding: 20px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
}
.close {
    position: absolute;
    top: 10px;
    right: 10px;
    cursor: pointer;
}
.input-field {
    width: 100%;
    padding: 10px;
    margin-top: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    color: black;
}
.ui-autocomplete {
    position: absolute;
    background-color: #fff;
    border: 1px solid #ccc;
    max-height: 150px; /* Limit the height of the dropdown */
    overflow-y: auto; /* Add a scrollbar when needed */
    z-index: 1000; /* Adjust z-index to ensure it's displayed above other elements */
    direction: rtl;
}
.ui-menu-item {
    padding: 10px;
    cursor: pointer;
    direction: rtl;
}
.ui-menu-item:hover {
    background-color: #f0f0f0;
}


</style>
</head>

<body>

    <center>
        <h1>מערכת ניהול קדמים</h1>
        <input onchange="update_year();" class="year" type="text" value=<?php echo "'".file_get_contents("../data/kdams_year")."'"; ?>/>
        <h3 style="color: white;">dbclick on course name to change its picture</h3>
        <h4 style="color: white;">to edit course name, go to global course change!</h4>
        <input hidden type='file' name='fileToUpload' id='formImage' accept="image/jpeg"/>
        <table>
            <thead>
                <tr>
                    <td>name</td>
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
                    
                    $all_courses = json_decode(file_get_contents("../data/courses.json"), true)["Data"];
                    $all_courses = json_encode($all_courses);
                    
                    foreach ($list as $course) {
                        if ($course["name"] == "מפגש חוגי") continue;
                        
                        echo "<tr>";
                        
                        echo "<td class='thin'><img class='line'/><input id='name' class='big he' type='text' value='".$course["name"]."'/></td>";
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
                        echo "<input type='hidden' value='".$course["code"]."'/>";
                        echo "<tr/>";
                    }
                
                ?>
            </tbody>
        </table>
        <button id="add-line">Add New Line</button>
        <button onclick="save()">Save</button>
        <button onclick="restore()">Restore</button>
    </center>

<div id="overlay" class="overlay">
    <div id="popup" class="popup">
        <span class="close" onclick="overlay.style.display = 'none';" id="closeButton">&times;</span>
        <input id="lname" type="text" class="input-field" placeholder="שם הקורס אותו תרצה להוסיף">
    </div>
</div>
</body>

<script>
var all_existing = <?php echo $all_courses; ?>;
var new_crs = [];

var curr = "";

window.onload = function(){
    
    $("#lname").autocomplete({
        source: all_existing,
        appendTo: "#popup",
        select: function(event, ui) {
            const selectedValue = ui.item.value;
            if (currOpt.parent().find("option:contains('" + selectedValue + "')").length > 0) {
                window.alert("already in list!");
            }
            else
            {
                currOpt.before("<option value='"+selectedValue+"'>"+selectedValue+"</option>");
                currOpt.parent().prop('selectedIndex', currOpt.parent().children().length - 3);
                currOpt = null;
                overlay.style.display = "none";
            }
        },
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
        $(this).attr('disabled', true);
        $(this).width(11.5 * Math.max(1, $(this).val().length));
    })
    
    $(document).on("input", "input.big" , function() {
        $(this).width(11.5 * Math.max(1, $(this).val().length));
    });
    
    $(document).on("click", "img.line" , function() {
        var del = confirm("Are you sure you want to delete course?");
        if (del) {
            var val = $(this).parent().parent().children()[5].value;
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
        
        var code = prompt("course code", "203.1111");
        
        if (code == "" || code == null) return false;

        var text = "<tr><td class='thin'><img class='line'/><input id='name' class='big he' type='text' value='name'/></td>" + 
                    "<td class='thin'><input id='pts' type='text' value='0'/></td>" + 
                    "<td class='thin'><input id='ids' type='text' value='a'/></td>" +
                    "<td class='thin'><input id='note' type='text' value='None'/></td>" + 
                    "<td class='thin'><img class='add'/>None</td><input type='hidden' value='"+code+"'/><tr/>";
        
        new_crs.push(code);
        $("tbody").append(text);
    })
    
    var currOpt = null;
    $(document).on("change", "select" , function() {
        var opt = $(this).children("option:selected");
        if (opt.val() == "הוספה"){
            overlay.style.display = "block";
            $("#lname").val("");
            currOpt = opt;
        } else if (opt.val() == "מחיקה"){
            var ind = prompt("select removal index");
            if (ind != null && ind < $(this).children().length - 2) {
                $(this).children().eq(ind).remove();
            }
            $(this).prop('selectedIndex', 0);
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

function load_specific()
{
    var crs = prompt("Add link", "51314543/001");
    if (crs != null && /^\d{8}\/00[123]$/.test(crs)) {
        $.ajax({
            url:"save_kdams.php", //the page containing php script
            type: "post", //request type,
            data: {"command": "load_specific", "data": crs},
                success:function(result){
                if (result == "") {
                    window.alert("saved");
                    location.reload();
                }
                else 
                    window.alert(result);
            }
        });
    }
}

function auto_load()
{
    var isExecuted = confirm("Warning: this will REMOVE the existing data and insert new data. Proceed?");
    if (isExecuted)
    {
        $.ajax({
            url:"save_kdams.php", //the page containing php script
            type: "post", //request type,
            data: {"command": "auto_load", "data": ""},
                success:function(result){
                    if (result == "") {
                        window.alert("success!");
                        location.reload();
                    }
                    else
                        window.alert(result);
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
    var arr = [];
    $("input#name").each(function(){
        if (arr.includes($(this).val())) {
            window.alert($(this).val() + " appears twice");
            success = false;
            return false;
        }
        arr.push($(this).val());
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
        
        if (values[0] == "" || values[1] == "" || values[2] == "") success = false;
        if (values[0] == "name") success = false;
        if (values[1] == "0") success = false;
        if (values[2].indexOf('a') == -1 && values[2].indexOf('b') == -1 && values[2].indexOf('c') == -1) success = false;
        
        if (!success){
            window.alert("syntax error in " + values[0]);
            return false;
        }
        
        if (values[3] == "None") values[3] = "";
        values[2] = values[2].replace(",","");
        
        var depens = [];
        if ($(this).find("option").length > 2){
            $(this).find("option").each(function(){depens.push($(this).val());});
            depens.pop();
            depens.pop();
        }
        values.push(depens);
        
        var data = "UPDATE `Tkdams` SET `ids`='" + values[2].replace(",","") + "',`pts`='" + values[1] + "',`note`='" + values[3] + "'";
        if (new_crs.includes(values[4]))
            data = "INSERT INTO `Tkdams`(`code`, `name`, `ids`, `pts`, `note`, `kdams`) VALUES ('" + values[4] + "','" + values[0] + "','" + values[2].replace(",","") + "','" + values[1] + "','" + values[3] + "'";
        
        if (values[5].length > 0){
            if (new_crs.includes(values[4])) data += ",'";
            else data += ", kdams='";
            for (var i = 0; i < values[5].length; i++)
                data += values[5][i] + ",";
            data = data.slice(0, -1);
            data += "'";
            if (new_crs.includes(values[4])) data += ");";
        }
        else if (new_crs.includes(values[4]))
            data += ",NULL);";
        
        if (!new_crs.includes(values[4]))
            data += " WHERE code='" + values[4] + "';";
            
        output += data;

        courses.push(values[0]);
    });
    
    if (success){
        
        output = output.slice(0,-1);
        
        $.ajax({
            url:"save_kdams.php", //the page containing php script
            type: "post", //request type,
            data: {"data": output, "courses": JSON.stringify(courses)},
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

function restore(){
    var cor = confirm("Are you sure? you will lose all changes");
    if (cor) location.reload();
}


</script>


</html>