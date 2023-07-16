<html>
<head>
<title>Manage Users</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="UTF-8">
<meta http-equiv="Cache-Control" content="max-age=0, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0" />

<script src="http://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
<script src="http://code.jquery.com/jquery-1.12.4.js"></script>
<script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<style>

*{
    font-family: Arial;
}

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

select#choose{
    width: 50%;
}

select{
    direction: rtl;
    background-color: lightslategrey;
    color: white;
    border: 6px solid transparent;
    font-size: 14px;
}

select.tiny{
    width: 50%;
    position: relative;
    left: 128%;
    top: -45px;
}

h3{
    direction: rtl;
    color:white;
    position: relative;
    font-size: 40px;
}

table{
    color: white;
    width: 60%;
    padding-top: 50px;
    font-size: 25px;
}

table#users{
    width: 70%;
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

td.extra{
    width: 2%;
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

input{
    background: transparent;
    border: none;
    color: white;
    font-size: 25px;
    text-align: center;
    font-family: auto;
}

input.tiny{
    width: 40px;
}

input.middle{
    width: 80px;
}

input.big{
    width: 160px;
}

input.big2{
    width: 240px;
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

img.add{
    content:url("../img/+.png");
    width: 20px;
    position: relative;
    padding-top: 4px;
}


img.line{
    content:url("../img/-.png");
    width: 20px;
    position: absolute;
    left: 14%;
    padding-top: 7px;
}

img.update{
    content:url("../img/check.png");
    width: 20px;
    position: absolute;
    left: 16%;
    padding-top: 7px;
}
</style>
</head>

<body>

    <center>
        <h1>מערכת ניהול משתמשים</h1>
        
        <table class="choose">
            <tr>
                <td>
                    <select id="choose">
                        <option value="3">הוספת סטודנט פרטנית</option>
                        <option value="2">קידום שנת לימודים</option>
                        <option value="4">קבלת רשימת תפוצה</option>
                        <option value="1">הודעה פרטנית למשתמש</option>
                    </select>
                </td>
                <td>בחר באופציה הרצויה</td>
            </tr>
            <tr>
                <td><button id="load" onclick="load()">Load</button></td>
                <td><button id="unload" hidden onclick="unload()">Unload</button></td>
            </tr>
        </table>
        <div class="drive" hidden>
            <h3>שימו לב! בכל שליחה 300 אנשים לכל היותר.</h3><br/><br/>
            <button class="mail_data" onclick="load_mails(1,1)">Load year 1</button>
            <button class="mail_data" onclick="load_mails(1,2)">Load year 2</button>
            <button class="mail_data" onclick="load_mails(1,3)">Load year 3-4</button>
            <button class="mail_data" onclick="load_mails(2,null)">Load MSC</button>
        </div>
    </center>

</body>

<script>

window.onload = function(){

    $(document).on("input", "input.big" , function() {
        $(this).width(13 * Math.max(1, $(this).val().length));
    });
    
    $(document).on("click", "img.add" , function() {
        var note = prompt("enter message:", $(this).prev().val());
        $(this).prev().val(note);
    });
    
    $(document).on("click", "img.line" , function() {
        var del = confirm("Are you sure you want to delete course?");
        if (del){
            $.ajax({
                url:"save_users.php", //the page containing php script
                type: "post", //request type,
                data: {"command": "delete_user", "email": $(this).next().val() },
                success: function(result){
                    if (result == "1")
                        $(this).parent().parent().remove();
                }
        });
    }});
    
    $(document).on("click", "img.update" , function() {
        var data = $(this).parent().parent().find(":input").map(function(){return $(this).val();}).get();
        if (data[0] == "" || data[1] == "") {window.alert("null value?"); return false;}
        var del = confirm("Are you sure you want to change user data?");
        if (del){
            if ($("select#choose").val() == 0){
                $.ajax({
                    url:"save_users.php", //the page containing php script
                    type: "post", //request type,
                    data: {"command": "update_user", "data": data },
                    success: function(result){
                        if (result == "1")
                            window.alert("updated");
                    }
                });
            } 
            else{
                $.ajax({
                    url:"save_users.php", //the page containing php script
                    type: "post", //request type,
                    data: {"command": "insert_user", "data": data },
                    success: function(result){
                        if (result == "1")
                            window.alert("added.");
                    }
                });
            }
    }});
}

function by_name_mail(){
    $("table#users tbody tr").hide();
    $("table#users tbody tr").filter(function() {
        return $(this).find(":input")[0].value.indexOf($("input#by-email").val()) !== -1 &&
                $(this).find(":input")[1].value.indexOf($("input#by-name").val()) !== -1;
    }).show();
}

function save(){
    
}

function load_mails(deg, year){
    $.ajax({
            url:"save_users.php", //the page containing php script
            type: "post", //request type,
            data: {"command": "get_mails", 'deg': deg, 'year': year},
            success: function(result){
                if (result != ""){
                        copyToClipboard(result);
                        window.alert("copied to clipboard");
                    }
            }
    });
}

function validateEmail(email) {
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

function load(){
    
    if ($("select#choose").val() == 1) {
       
        var mail = prompt("enter extact mail of user");
        
        if (mail == "" || mail == null || !validateEmail(mail)){
            window.alert("invalid mail");
            return;
        }
        
        var msg = prompt("enter your messgae. Leaving blank will remove it");
        
        $.ajax({
            url:"save_users.php", //the page containing php script
            type: "post", //request type,
            data: {"command": "set_msg", "mail": mail, "msg": msg},
            success: function(result){
                if (result != "0"){
                    window.alert("done");
                    location.reload();
                }
            }
        });
    }
    
    if ($("select#choose").val() == 4) {
        $("div.drive").show();
        $("table.choose").find(":input").prop('disabled', true);
        $("button#load").hide();
        $("button#unload").show();
        $("button#unload").prop('disabled', false);
        return;
    }
    
    if ($("select#choose").val() == 3) {
        
        var html = "<table id='users'><thead><tr><td>email</td><td>name</td><td>degree</td><td>year</td><td>msg</td></tr></thead><tbody>";
        html += "<tr><td class='middle'><img class='update'/><input name='email' class='big' placeholder='email'/></td>";
        html += "<td class='middle'><input name='name' class='big' placeholder='name'/></td>";
        html += "<td class='tiny'><select name='degree'><option value='1'>1</option><option value='2'>2</option><option value='3'>3</option>";
        html += "</select></td><td class='tiny'><select name='year'><option value='0'>0</option><option value='1'>1</option><option value='2'>2</option><option value='3'>3</option><option value='4'>4</option>";
        html += "</select></td>";
        html += "<td class='extra'><input name='msg' type='hidden' value=''/><img class='add'/></td></tr></tbody></table>";
        
        $("div.drive").before(html);
                
        $("table.choose").find(":input").prop('disabled', true);
        $("button#load").hide();
        $("button#unload").show();
        $("button#unload").prop('disabled', false);
        
        return;
    }
    
    if ($("select#choose").val() == 2) {
        var del = confirm("THIS ACTION CANNOT BE CANCLED. continue?");
        if (del){
            $.ajax({
                url:"save_users.php", //the page containing php script
                type: "post", //request type,
                data: {"command": "inc_year"},
                success: function(result){
                    if (result != "0"){
                        window.alert("done");
                        location.reload();
                    }
                }
            });
        }
        return;
    }
}

function unload(){
    
    $("table#users").remove();
    $("table.choose").find(":input").prop('disabled', false);
    $("button#load").show();
    $("button#unload").hide();
    
    $("div").hide();
}

function copyToClipboard(text) {
    var dummy = document.createElement("textarea");
    // to avoid breaking orgain page when copying more words
    // cant copy when adding below this code
    // dummy.style.display = 'none'
    document.body.appendChild(dummy);
    //Be careful if you use texarea. setAttribute('value', value), which works with "input" does not work with "textarea". – Eduard
    dummy.value = text;
    dummy.select();
    document.execCommand("copy");
    document.body.removeChild(dummy);
}

function isJson(item) {
    item = typeof item !== "string"
        ? JSON.stringify(item)
        : item;

    try {
        item = JSON.parse(item);
    } catch (e) {
        return false;
    }

    if (typeof item === "object" && item !== null) {
        return true;
    }

    return false;
}


</script>


</html>