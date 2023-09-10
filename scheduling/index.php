<?php

require_once '../data/semester_dates.php';

//TODO: ADD TO REMINDERS TO UPDATE THIS SHIT EVERY MONTH

$hebrewDays = array(
        'ראשון' => 0,
        'שני' => 1,
        'שלישי' => 2,
        'רביעי' => 3,
        'חמישי' => 4,
        'שישי' => 5,
        'שבת' => 6
    );

function proccess_lessons($data)
{
    global $semester_start_rep, $sem_repts, $hebrewDays;
    $wheres = array();
    
    $data = file_get_contents($data);
    $data = json_decode($data, true)["data"];
    
    $new_name = str_replace("'", "", str_replace('"', "", $data['courseName']));
    //$new_name .= " ({$data['credits']})";
    
    $code = $data['course'];
    $allLessons = [];
    
    // Iterate over the groups
    foreach ($data['groups'] as $group) {
        // Extract lessons from each group and merge them into $allLessons
        $allLessons = array_merge($allLessons, $group['lessons']);
    }
    
    foreach ($allLessons as $lsn)
    {
        $when = explode(' ', $lsn['when']);
        if (count($when) < 4)
            continue;
            
        $from = $when[0];
        $to = $when[1];
        
        $day = $hebrewDays[$when[3]];
        $day = date('Y-m-d', strtotime("+$day day", strtotime($semester_start_rep)));
        
        $name = $lsn['lessonsName'];
        $name = str_replace("'", "", str_replace('"', "", $name));
        $name = str_replace('-', "\r\n", $name);
        
        $lecturer = explode(' ', $lsn['lecturer']);
        if (count($lecturer) > 2)
            $lecturer = $lecturer[1] . ' ' . end($lecturer);
        else
            $lecturer = implode(' ', $lecturer);
        if (!str_ends_with($lsn['lesson'], '01'))
            $lecturer = "";
        
        $place = explode(' ', $lsn['where']);
        if (count($place) > 5)
            $place = $place[1] . ', ' . $place[4] . ' ' . $place[5];
        else
            $place = "";
        
        $pts = $data['credits'];

        array_push($wheres, array("name" => $name, "lecturer" => $lecturer, "place" => $place, "from" => $from, "to" => $to, "day" => $day, "pts" => $pts, "code" => $code));
    }
    
    $tempArray = [];

    // Filter out duplicates while keeping the first occurrence
    $resultArray = array_filter($wheres, function ($item) use (&$tempArray) {
        $key = "{$item['from']}##{$item['to']}##{$item['day']}";
        if (!isset($tempArray[$key])) {
            $tempArray[$key] = true;
            return true;
        }
        return false;
    });
    
    $resultArray = array_values($resultArray);
    return array("name" => $new_name, "lessons" => $resultArray);
}

function compareByName($a, $b) {
    return strcmp($a["name"], $b["name"]);
}

$sem = isset($_GET['sem']) ? $_GET['sem'] : "a";
$sem_idx = ord($sem) - 97;
$sem_repts = "00".strval($sem_idx+1);

$semester_start_rep = $semester_start[$sem_idx];
$year = substr($semester_end[0], 0, 4);

$items = scandir("../rooms");
unset($items[0]);
unset($items[1]);

$groupCourses = [];

foreach ($items as $item){
    if (str_ends_with($item, "-$sem_repts"))
        $groupCourses[] = proccess_lessons("../rooms/$item");
}

$groupCourses = array_filter($groupCourses, function($t) {
    return count($t['lessons']) > 0;
});
$groupCourses = array_values($groupCourses);

// Sort the array using the custom comparison function
usort($groupCourses, "compareByName");

$courses = array_map(function ($item) {
    return $item["lessons"];
}, $groupCourses);

$courses_names = array_map(function ($item) {
    return $item["name"];
}, $groupCourses);

$semester_start = str_replace("-", "", $semester_start_rep);
$semester_end = date('Y-m-d', strtotime(str_replace('/', '-', $semester_end[$sem_idx])));

$courses = base64_encode(json_encode($courses));
$courses_names = base64_encode(json_encode($courses_names));

$header_ics = file_get_contents("header.ics");
$year = file_get_contents("../data/kdams_year");
$year2 = $year-1;
$header_ics = str_replace("2022-2023", "$year2-$year", $header_ics);

if ($sem_idx == 1)
    $header_ics = str_replace("Winter", "Spring", $header_ics);

$header_ics = json_encode($header_ics);
$event_ics = json_encode(file_get_contents("event.ics"));

?>

<!DOCTYPE html>
<html>
<head>
    <title>SITE_NAME Scheduler</title>

    <!-- head -->
    <meta charset="utf-8"/>
    <meta name="referrer" content="no-referrer-when-downgrade"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="helpers/main.css?v=2022.3.432" type="text/css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet"/>
    <script src="daypilot-all.min.js?v=2022.3.5439"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
    .calendar_menu_event_inner {
        position: relative;
        padding-top: 30px;
    }
    .dpw-body{
        margin-left: 0px !important;
    }
    div#menu{
        left: 1% !important;
        overflow-y: scroll !important;
        width: 12% !important;
    }
    
    div.panel a{
        display: inline;
        margin-left: 10px;
        margin-right: 10px;
    }
    .file__input{
        opacity: 0;
        width: 0;
    }
    .calendar_default_event_inner{
        direction: rtl;
    }
    </style>
    
    <!-- /head -->

</head>
<body>

<!-- top -->
<template id="content" data-version="2022.3.432">

    <!-- /top -->
    <div id="dp"><div hidden class="search">
                <div class="search-box"><input type="text" id="search-box-input" placeholder="Quick search"><button id="search-box-clear">×</button></div>
            </div></div>
    
    
    <script type="text/javascript">
    
        window.mobileAndTabletCheck = function() {
          let check = false;
          (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
          return check;
        };
    
        document.getElementsByClassName("space")[0].remove();
        document.getElementsByClassName("dpw-subheader")[0].remove();
        document.getElementsByClassName("dpw-title-inner")[0].innerHTML = '<a id="downloadAnchorElem" style="display:none"></a><div class="panel"><a href="?sem=a">semester a</a><a href="?sem=b">semester b</a>' + 
        '<br/><a href="#" onclick="cal()">export to Calendar</a><a href="#" onclick="exprt()">save as image</a>' + 
        '<br/><a href="#" onclick="exams()">go to exams</a>' +
        '<br/><input accept=".json" type="file" name="fileToUpload" id="fromJSON" class="file__input"><a href="#" onclick="load()">load from json</a><a href="#" onclick="save()">export to json</a>' + 
        '<br/><a href="#" onclick="reset();">reset</a></div>';
        
        document.getElementsByClassName("dpw-header-item dpw-header-main")[0].innerHTML = '<a>' + <?php echo "'$year'"; ?> + ', semester ' + <?php echo "'$sem'"; ?> + '</a>';
        document.getElementsByClassName("search")[0].hidden = "hidden";
        document.getElementsByClassName("dp-menu")[0].outerHTML = '<div class="dp-menu"><div class="calendar_default_event" id="menu" style="position: absolute;%;height: 100px;overflow: hidden;cursor: pointer;"></div></div>';
    
        // Get a reference to the div element
        const myDiv = document.getElementById('dp');
        
        // Create a MutationObserver to watch for changes in the div's subtree
        const observer = new MutationObserver((mutations) => {
          mutations.forEach((mutation) => {
            // Check if nodes were removed from the div
            if (mutation.removedNodes.length == 7) {
              // Content is being removed, handle it here
              console.log('Content removed from div:', mutation.removedNodes);
              
               mutation.removedNodes.forEach((node) => {
                 myDiv.appendChild(node);
               });
               $("#dp").contents()[0].remove();
            }
          });
        });
        
        // Configure and start the observer
        observer.observe(myDiv, {
          childList: true, // Watch for changes to the children of the div
          subtree: true,   // Watch for changes throughout the entire subtree
        });

    </script>
    
    <script type="text/javascript">

        const dp = new DayPilot.Calendar("dp", {
            //viewType: "Week",
            viewType: "Days",
            days: 6,
            startDate: <?php echo "'$semester_start_rep'"; ?>,
            businessBeginsHour: 8,
            businessEndsHour: 20,
            showNonBusiness: false,
            //startDate: DayPilot.Date.today(),
            headerDateFormat: "dddd",
            eventDoubleClickHandling: "Enabled",
            contextMenu: new DayPilot.Menu({
                items: [
                    { 
                        text: "Delete", onClick: function (args) { 
                            dp.events.remove(args.source); 
                            //const e = dp.events.find(e => 
                             //   e.data.code == args.source.data.data.code); 
                        } 
                    },
                ]
              }),

            onEventClick: async args => {

                const colors = [
                    {name: "Blue", id: "#3c78d8"},
                    {name: "Green", id: "#6aa84f"},
                    {name: "Yellow", id: "#f1c232"},
                    {name: "Red", id: "#cc0000"},
                    {name: "Black", id: "#000000"},
                    {name: "Purple", id: "#800080"}
                ];

                const form = [
                    {name: "Text", id: "text"},
                    {name: "Start", id: "start", type: "datetime"},
                    {name: "End", id: "end", type: "datetime"},
                    {name: "Color", id: "barColor", type: "select", options: colors},
                ];

                const modal = await DayPilot.Modal.form(form, args.e.data);

                if (modal.canceled) {
                    return;
                }

                dp.events.update(modal.result);

            },
            onBeforeEventRender: args => {
                args.data.barBackColor = "transparent";
                if (!args.data.barColor) {
                    args.data.barColor = "#333";
                }
            },
            onTimeRangeSelected: async args => {

                const form = [
                    {name: "Name", id: "text"}
                ];

                const data = {
                    text: "Event"
                };

                const modal = await DayPilot.Modal.form(form, data);

                dp.clearSelection();

                if (modal.canceled) {
                    return;
                }

                dp.events.add({
                    start: args.start,
                    end: args.end,
                    id: DayPilot.guid(),
                    text: modal.result.text,
                    barColor: "#3c78d8"
                });
            },
            onHeaderClick: args => {
                console.log("args", args);
            }
        });

        dp.init();
        $("div.calendar_default_corner > div:contains('DEMO')").remove();
        $("div#menu").css("height", $("div#dp").height());
        
        var courses = JSON.parse(atob(<?php echo "'$courses'" ?>));  
        var names = JSON.parse(atob(<?php echo "'$courses_names'" ?>)); 
        
        var str = "";
        for (var i = 0; i < names.length; i++)
            str += '<div unselectable="on" del="0" code="' + courses[i][0]["code"] + '" class="calendar_default_event_inner calendar_menu_event_inner">' + names[i] + " (" + courses[i][0]["pts"] + ')</div>';
        $("div#menu").html(str);
        
        function removeAll(code)
        {
            var ret = false;
            dp.events.all().forEach(crs => {
                if (crs.data.data.code == code) {
                    dp.events.remove(crs);
                    ret = code;
                }
            });
            
            return ret;
        }
        
        var courses1 = [];
        const start_date_rep = <?php echo "'".substr($semester_start_rep, 0, -2)."'"; ?>;
        
        
        $("div#menu").on("click", "div", function() {
            
            if ($(this).attr('del') == "0")
            {
                var index = names.indexOf($(this).text().slice(0,-4));
                courses1.push(courses[index][0].code);
                courses[index].forEach(course => {
                    dp.clearSelection();
                    var txt = course.name +"\r\n" + course.pts + ' נ"ז' + "\r\n"+course.from+"-"+course.to+"\r\n";
                    if (course.lecturer != "")
                        txt += course.lecturer+"\r\n";
                    if (course.place != "")
                        txt += course.place+"\r\n";
                    var e = new DayPilot.Event({
                        start: course.day+"T"+course.from+":00",
                        end: course.day+"T"+course.to+":00",
                        id: DayPilot.guid(),
                        text: txt,
                        barColor: "#3c78d8",
                        data: {
                            code: course.code,
                            org_name: course.name,
                            pts: course.pts
                        }
                    });
                    dp.events.add(e);
                });
                $(this).text($(this).text() + " - מחיקה");
                $(this).attr('del', "1"); 
            }
            else
            {
                var removedCode = removeAll($(this).attr('code'));
                if (removedCode){
                    const index = courses1.indexOf(removedCode);
                    if (index > -1) { 
                      courses1.splice(index, 1);
                    }
                    return;
                }
                $(this).text($(this).text().replace(" - מחיקה", ""));
                $(this).attr('del', "0"); 
            }
        });
        
        function save()
        {
            var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(dp.events.list));
            var dlAnchorElem = document.getElementById('downloadAnchorElem');
            dlAnchorElem.setAttribute("href",     dataStr     );
            dlAnchorElem.setAttribute("download", "cal.json");
            dlAnchorElem.click();
        }
        
        function reset()
        {
            dp.events.list = [];
            dp.update();
            
            $("div#menu").children().each(function() {
                $(this).text($(this).text().replace(" - מחיקה", ""));
            });
        }
        
        function exprt()
        {
            dp.exportAs("png", {area: "full"}).download();
        }
        
        function exams()
        {
            var list = courses1.join(',');
            $('<form hidden method="POST" action="https://SITE_URL/exams/output.php"><input name="hidden-input" value="'+list+'"/><input name="sem" value="'+<?php echo "'$sem'"; ?>+'"/></form>').appendTo('body').submit();
        }
        
        function get_obj(code, start, end)
        {
            var obj = null;
            var date = start.split('T')[0];
            from = start.split('T')[1].slice(0,5);
            to = end.split('T')[1].slice(0,5);
            
            for (var i = 0; i < courses.length; i++)
            {
                if (courses[i][0].code == code)
                {
                    courses[i].forEach(course => {
                        if (from == course.from && to == course.to && course.day == date) {
                            obj = course;
                        }
                    });
                    if (obj != null)
                        return obj;
                }
            }
            return null;
        }
        
        function format_hour(from, to, date1)
        {
            date1 = date1.split('-').join("");
            from = from.split(':')[0];
            to = to.split(':')[0];
            to = parseInt(to) - 1;
            
            var res1 = date1 + "T" + from + "1500";
            var res2 = date1 + "T" + to + "4500";
            
            if (parseInt(from) == 8)
                res1 = date1 + "T083000";
            
            if (parseInt(to) == 9)          //origin 10
                res2 = date1 + "T100000";  
                
            return [res1, res2];
        }
        
        var head = <?php echo "'$header_ics'"; ?>;
        head = head.slice(1, -1);
        var evt = <?php echo "'$event_ics'"; ?>;
        evt = evt.slice(1, -1);
        var weekdays = ["SU", "MO", "TU", "WE", "TH", "FR", "SA"];
        const semster_end = <?php echo "'$semester_end'"; ?>.split('-').join("");
        
        const locations = ["הנמל 16, חיפה, ישראל", "הנמל 65, חיפה, ישראל"];
        
        function cal()
        {
            var all_text = [head];
            
            dp.events.all().forEach(crs => {
                var obj = get_obj(crs.data.data.code, crs.data.start.value, crs.data.end.value);
                var tt = format_hour(obj.from, obj.to, obj.day);
                var text = evt.replace("lesson_start", tt[0]);
                text = text.replace("lesson_end", tt[1]);
                text = text.replace("semester_end", semster_end);
                text = text.replace("day", weekdays[new Date(obj.day).getDay()]);
                text = text.replace("where", obj.place);
                text = text.replace("name", obj.name);
                
                var loc = locations[0];
                if (obj.place.startsWith("עמיר")) loc = locations[1];
                text = text.replace("loc", loc);
                
                all_text.push(text);
                
            });
            
            all_text.push("END:VCALENDAR");
            all_text = all_text.join('\r\n');
            all_text = all_text.replace('"', "");
            var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(all_text);
            var dlAnchorElem = document.getElementById('downloadAnchorElem');
            dlAnchorElem.setAttribute("href",     dataStr     );
            dlAnchorElem.setAttribute("download", "course.ics");
            dlAnchorElem.click();
        }
        
        function load()
        {
            $("#fromJSON").click();
        }
        
        function readURL(input) {
          if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                
              var list = JSON.parse(e.target.result);
              list.forEach(lst => {
                  dp.events.add(new DayPilot.Event(lst));
              });
            }
            
            reader.readAsText(input.files[0]); // convert to base64 string
          }
        }
        
        $("#fromJSON").change(function() {
            readURL(this);
        });
        
        // Check for congruence between any pair of events
        function areEventsCongruent(event1, event2) {
            return (event1.start < event2.end) && (event1.end > event2.start);
        }

        
        function generate()
        {
            
            // Loop through all pairs of events and check for congruence
            for (var i = 0; i < dp.events.list.length; i++) {
                for (var j = i + 1; j < dp.events.list.length; j++) {
                    if (areEventsCongruent(dp.events.list[i], dp.events.list[j])) {
                        window.alert("events are overlapping!");
                        return false;
                    } 
                }
            }
             // Create a form element
            var form = $('<form></form>', {
                'action': 'generate_csv.php', // Replace with your PHP endpoint
                'method': 'POST',
                'style': 'display: none;' // Hide the form
            });

            // Add input fields to the form
            form.append($('<input>', {
                'type': 'text',
                'name': 'codes',
                'value': JSON.stringify(courses1)
            }));
            form.append($('<input>', {
                'type': 'text',
                'name': 'zero_date',
                'value': <?php echo "'$semester_start_rep'"; ?>
            }));
            form.append($('<input>', {
                'type': 'text',
                'name': 'events',
                'value': JSON.stringify(dp.events.list)
            }));


            // Append the form to the document and submit it
            $('body').append(form);
            form.submit();
            
            form.remove();
        }
        
    </script>

    <!-- bottom -->
</template>

<script src="helpers/app.js?v=2022.3.5439"></script>


<!-- /bottom -->

</body>
</html>

