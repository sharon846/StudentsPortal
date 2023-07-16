<html>
<head>
<title>Manage Grades</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="UTF-8">
<meta http-equiv="Cache-Control" content="max-age=0, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0" />

<script src="http://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
<script src="http://code.jquery.com/jquery-1.12.4.js"></script>
<script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js" integrity="sha256-/H4YS+7aYb9kJ5OKhFYPUjSJdrtV6AeyJOtTkw6X72o=" crossorigin="anonymous"></script>

<style>

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
    width: 10%;
    position: absolute;
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
    border: none;
    color: white;
    font-size: 25px;
    text-align: center;
    font-family: auto;
}

input.tiny{
    width: 70px;
}

div.data{
    position: relative;
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

</style>
</head>

<body>
    <center>
        <h1>מערכת ניהול ציונים</h1>
        
        <table class="choose">
            <?php
                $month = date('m');
                $year = date('Y');
                $semester = array("01" => "a", "02" => "a", "03" => "a", "04" => "a", "05" => "a", "06" => "b", "07" => "b", "08" => "b", "09" => "c", "10" => "c", "11" => "a", "12" => "a")[$month];

                $my = $year;
                $ms = ord($semester);
            ?>
            <tr>
                <td><input min="2013" max="2038" id="year" class="tiny" type="text" placeholder="שנה" value="<?php echo $my;?>"/></td>
                <td>הזן שנה: </td>
            </tr>
            <tr>
                <td>
                    <select id="semesters">
                        <?php 
                            
                            for ($s = 97; $s < 100; $s++)
                            {
                                $he = array("א", "ב", "ג")[$s-97];
                                echo "<option value='".chr($s-32)."'";
                                if ($s == $ms) echo " selected";
                                echo " >סמסטר $he</option>";
                            }
                        
                        ?>
                    </select></td>
                <td>הזן סמסטר: </td>
            </tr>
            <tr>
                <td>
                    <select id="courses">
                        <?php
                        $xml = simplexml_load_file("../data/courses.xml");
                    
                        foreach ($xml->Course as $course){
                            echo "<option value='".$course."'>$course</option>";  
                        }
                        ?>
                    </select>
                </td>
                <td>בחר קורס: </td>
            </tr>
            <tr>
                <td>
                    <select id="lectures">
                        <?php
                        $xml = simplexml_load_file("../data/lectures.xml");
                    
                        foreach ($xml->Lecture as $lecture){
                            echo "<option value='".$lecture."'>$lecture</option>";  
                        }
                        ?>
                    </select>
                </td>
                <td>בחר מרצה: </td>
            </tr>
            <tr>
                <td><button id="load" onclick="load()">Load</button></td>
                <td><button id="unload" hidden onclick="unload()">Unload</button></td>
            </tr>
        </table>
        
        <div id="grade-edit" hidden>
            <div class="data">
                <h3>template row means there is no grade for moed</h3>
                <select class="tiny">
                    <option value></option>
                    <option value='סמינר'>סמינר</option>
                    <option value='מעבדה'>מעבדה</option>
                    <option value='פרוייקט'>פרוייקט</option>
                    <option value='סופי'>סופי</option>
                    <option value='del'>מחיקת הקורס</option>
                </select>
            </div>
            <table id="grade-edit">
                <thead>
                    <tr>
                        <td>moed</td>
                        <td>average</td>
                        <td>no avg</td>
                        <td>final grades</td>
                        <td class="grades">grades</td>
                    <tr/>
                </thead>
                <tbody>
                    <tr id="a">
                        <td>a</td>
                        <td><input class='tiny' type='text' value='avg'/></td>
                        <td><input type='checkbox'/></td>
                        <td><input type='checkbox'/></td>
                        <td><input placeholder='n0,n1,n2,n3,n4,n5,n6,n7,n8,n9'/></td>
                    </tr>
                    <tr id="b">
                        <td>b</td>
                        <td><input class='tiny' type='text' value='avg'/></td>
                        <td><input type='checkbox'/></td>
                        <td><input type='checkbox'/></td>
                        <td><input placeholder='n0,n1,n2,n3,n4,n5,n6,n7,n8,n9'/></td>
                    </tr>
                    <tr id="c">
                        <td>c</td>
                        <td><input class='tiny' type='text' value='avg'/></td>
                        <td><input type='checkbox'/></td>
                        <td><input type='checkbox'/></td>
                        <td><input placeholder='n0,n1,n2,n3,n4,n5,n6,n7,n8,n9'/></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="save" hidden>
            <button onclick="save()">Save</button>
            <button onclick="restore()">Restore</button>
        </div>
    </center>

</body>

<script>
var backup;

window.onload = function(){
    $("select.tiny").on('change', function(){
        if ($(this).val() == "del"){
            if (confirm("are you sure?") != true)
                return;
        
            $.ajax({
                url:"save_grades.php", //the page containing php script
                type: "post", //request type,
                data: {"cmd": "delete", "year": $("input#year").val(), "proj": $('select.tiny').val(), "semester": $("select#semesters").val(), "course": $("select#courses").val(), "lecture": $("select#lectures").val() },
                success: function(result){
                    window.alert("saved");
                    unload();
                }
            });
        }
        else if ($(this).val() == ""){
            $("tr#a").children().first().html("a");
            $("tr#b").show();
            $("tr#c").show();
        } else{
            $("tr#a").children().first().html("moed");
            $("tr#b").hide();
            $("tr#c").hide();
        }
    });
    backup = $("table#grade-edit").html();
}

function save(){
   
    var arr = [];
    
    $("table#grade-edit tbody tr").each(function(){
        
        if ($("select.tiny").val() != "" && $(this).attr('id') != "a") return;
        
        if (!$(this).find(":input").eq(1).prop('checked') && 
            ["", "avg"].includes($(this).find(":input").eq(0).val()))  { return; }
        if ($(this).find(":input").eq(3).val() == "")  { return; }
        if (($(this).find(":input").eq(3).val().match(/,/g) || []).length != 9)   { return; }
        if (($(this).find(":input").eq(3).val().match(/,,/g) || []).length > 0) {window.alert("syntax error in moed " + $(this).attr('id')); return; }
        
        var avg = $(this).find(":input").eq(0).val();
        var grades = $(this).find(":input").eq(3).val().split(',').map(x=>+x);
        var num = grades.reduce((a, b) => a + b, 0);
        var sum = 0;
        for (var i = 0; i < 10; i++){
            sum += (5 + 10 * i) * grades[i];
        }
        if ($(this).find(":input").eq(1).prop("checked"))
            avg = (sum / num).toFixed(3);

        var obj = {
            avg: avg,
            grades: $(this).find(":input").eq(3).val(),
            num: num,
            moed: $(this).attr('id'),
            proj2: ($(this).find(":input").eq(2).prop("checked")) ? "סופי" : ""
        };
        arr.push(obj);
    });
    
    if (confirm("are you sure?") != true)
        return;
    
    $.ajax({
        url:"save_grades.php", //the page containing php script
        type: "post", //request type,
        data: {"data": JSON.stringify(arr), "year": $("input#year").val(), "proj": $('select.tiny').val(), "semester": $("select#semesters").val(), "course": $("select#courses").val(), "lecture": $("select#lectures").val() },
        success: function(result){
            window.alert("saved");
        }
    });
   
}

function restore(){
    var cor = confirm("Are you sure? you will lose all changes");
    if (cor) location.reload();
}

function load(){
    
    if ($("input#year").val() == ""){
        window.alert("year!");
        return false;
    }
    
    var id = $("input#year").val() + $("select#semesters").val();   //2021a
    id += $("select#courses").val() + $("select#lectures").val();
    $.ajax({
        url:"save_grades.php", //the page containing php script
        type: "post", //request type,
        data: {"year": $("input#year").val(), "semester": $("select#semesters").val(), "course": $("select#courses").val(), "lecture": $("select#lectures").val() },
        success: function(result){
            
            result = JSON.parse(result);
            
            if (result != "")
                parseResults(result);
            
            $("table.choose").find(":input").prop('disabled', true);
            $("button#load").hide();
            $("button#unload").show();
            $("button#unload").prop('disabled', false);
           
            $("div#grade-edit").show();
            $("div").show();
        }
    });
    //"option:selected"
}

function unload(){
    
    
    $("table.choose").find(":input").prop('disabled', false);
    $("button#load").show();
    $("button#unload").hide();
           
    $("table#grade-edit").html(backup);
    $("div#grade-edit").hide();
    
    $("div").hide();
    $('select.tiny :nth-child(1)').prop('selected', true); 
}

function parseResults(attempts){
    
    var moeds = ['a', 'b', 'c'];
    
    hasNote = attempts[0].proj;
    
    for (var i = 0; i < attempts.length; i++){
        var moed = attempts[i].moed;
        var avg = attempts[i].avg;
        var final_grade = attempts[i].proj2;
        
        var grades = attempts[i].grades;

        var code = "<td>"+moed+"</td><td><input class='tiny' type='text' value='"+avg+"'/></td><td><input type='checkbox'/></td><td><input";
        if (final_grade) code += " checked";
        code += " type='checkbox'/></td><td><input value='"+grades+"'/></td>";
        $("tr#"+moed).html(code);
    }
    $('select.tiny')
    .val(hasNote)
    .trigger('change');
}

</script>


</html>