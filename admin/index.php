<html>
<head>
<title>Manage IS</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="UTF-8">
<meta http-equiv="Cache-Control" content="max-age=0, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0" />

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

input.tiny{
    width: 70px;
}

input {
    background: transparent;
    border: none;
    color: white;
    font-size: 25px;
    text-align: center;
    font-family: auto;
}

td {
    text-align: center;
    vertical-align: middle;
    direction: rtl;
    width: 4%;
}

tr>td {
    padding-bottom: 1em;
}

table {
    color: white;
    width: 50%;
    padding-top: 50px;
    font-size: 25px;
}

select {
    width: 50%;
    direction: rtl;
    background-color: lightslategrey;
    color: white;
    border: 6px solid transparent;
    font-size: 14px;
}

table#grade-edit{
    width: 60%;
}

</style>
</head>

<body>

    <center>
        <h1>מערכת ניהול אתר החוג</h1>
        <br/><br/><br/><br/><br/><br/>
        <button onclick="load(1)">ניהול משתמשים</button>
        <button onclick="load(2)">ניהול ציונים</button>
        <button onclick="load(3)">הודעות חדשות</button>
        <br/><br/><br/><br/>
        <button onclick="load(4)">ניהול קדמים</button>
        <button onclick="load(5)">עריכת קורסים</button>
        <button onclick="load(6)">עריכת מרצים</button>
        <br/><br/><br/><br/>
        <button onclick="load(7)">הודעות באתר</button>
        <button onclick="load(8)">מסד הנתונים</button>
		<button onclick="load(10)">לוח מבחנים</button>
        <br/><br/><br/><br/>>
        <button onclick="load(12)">שינוי מנהל</button>
        <button onclick="load(13)">תאריכי סמסטרים</button>
        <br/><br/><br/><br/>
    </center>

<script>
    function load(id){
        if (id == 1){
            window.location = "users.php";
        }
        if (id == 2){
            window.location = "grades.php";
        }
        if (id == 3){
            window.location = "updates.php";
        }
        if (id == 4){
            window.location = "kdams.php"
        }
        if (id == 5){
            window.location = "xml_editor.php?filename=../data/courses.xml";
        }
        if (id == 6){
            window.location = "xml_editor.php?filename=../data/lectures.xml";
        }
        if (id == 7){
            window.location = "xml_editor.php?filename=../data/news";
        }
        if (id == 8){
            window.location = "https://auth-db991.hstgr.io/index.php?route=/database/structure&db=u299615442_db_site";
        }
        if (id == 10){
            window.location = "exams.php";
        }
        if (id == 12){
            window.location = "txt_editor.php?filename=../site_manager/pdoconfig.php";
        }
        if (id == 13){
            window.location = "txt_editor.php?filename=../data/semester_dates.php";
        }
    }
</script>
</body>
</html>