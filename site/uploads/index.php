<?php

// Upload

if (!empty($_FILES)) {

    session_name("SITE_SESSION_NAME");
    session_start();
    $mail = "";
    
    if (isset($_SESSION["SITE_SESSION_NAME"])){
        $mail = $_SESSION["SITE_SESSION_NAME"]['mail'];
    }

    $override_file_name = false;
    $f = $_FILES;
    $ds = DIRECTORY_SEPARATOR;
   
    $errors = 0;
    $uploads = 0;

    $filename = $f['file']['name'];
    $tmp_name = $f['file']['tmp_name'];

    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $ext_1 = $ext ? '.'.$ext : '';
    
    $name = $_REQUEST['fname'];
    $name = str_replace(' ', '_', $name);
    
    $fullPath = getcwd() . '/data/dept/'. $name . '/'. $_REQUEST['fullpath'];

    $folder = substr($fullPath, 0, strrpos($fullPath, "/")).'/';
	
	
    while(file_exists ($fullPath) && !$override_file_name) {
        $fullPath = str_replace($ext_1, '', $fullPath) .'_copy'. $ext_1;
    }

    if (!is_dir($folder)) {
        $old = umask(0);
        mkdir($folder, 0755, true);
        umask($old);
    }

    if (empty($f['file']['error']) && !empty($tmp_name) && $tmp_name != 'none') {
        if (move_uploaded_file($tmp_name, $fullPath)) {
            file_put_contents(getcwd()."/../../admin/updates", "$mail uploaded metirial from course $name##https://SITE_DOMAIN/site_manager/index.php?p=site/uploads/data/is".PHP_EOL, FILE_APPEND);
            die('Successfully uploaded');
        } else {
            die(sprintf('Error while uploading files. Uploaded files: %s', $uploads));
        }
    }
    exit();

}
else{
    $xml = simplexml_load_file('../../data/courses.xml');
}
	
	?>

<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../site_manager/header.css">
    <link rel="stylesheet" href="../../header.css">
     <link rel="stylesheet" href=" https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
      <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<title>SITE_NAME Upload</title>

<style>

.path{
    margin-top: 8em;
}

h5{
    float: right;
    margin-top: -0rem;
}

body.dark div.card-body{
    background-color: rgb(91,33,182);
}

#overlay {
  width: 100%;
  height: 100%;
  position: fixed;
  top: 0;
  left: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: none;
  z-index: 9999;
}

.label__checkbox {
  display: none;
}

.label__check {
  display: inline-block;
  border-radius: 50%;
  border: 5px solid rgba(0,0,0,0.1);
  background: white;
  vertical-align: middle;
  margin-right: 20px;
  width: 4em;
  height: 4em;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: border .3s ease;
  
  i.icon {
    opacity: 0.2;
    font-size: ~'calc(1rem + 1vw)';
    color: transparent;
    transition: opacity .3s .1s ease;
    -webkit-text-stroke: 3px rgba(0,0,0,.5);
  }
  
  &:hover {
    border: 5px solid rgba(0,0,0,0.2);
  }
}

.label__checkbox:checked + .label__text .label__check {
  animation: check .5s cubic-bezier(0.895, 0.030, 0.685, 0.220) forwards;
  
  .icon {
    opacity: 1;
    transform: scale(0);
    color: white;
    -webkit-text-stroke: 0;
    animation: icon .3s cubic-bezier(1.000, 0.008, 0.565, 1.650) .1s 1 forwards;
  }
}

.center {
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%,-50%);
}

@keyframes icon {
  from {
    opacity: 0;
    transform: scale(0.3);
  }
  to {
    opacity: 1;
    transform: scale(1)
  }
}

@keyframes check {
  0% {
    width: 1.5em;
    height: 1.5em;
    border-width: 5px;
  }
  10% {
    width: 1.5em;
    height: 1.5em;
    opacity: 0.1;
    background: rgba(0,0,0,0.2);
    border-width: 15px;
  }
  12% {
    width: 1.5em;
    height: 1.5em;
    opacity: 0.4;
    background: rgba(0,0,0,0.1);
    border-width: 0;
  }
  50% {
    width: 2em;
    height: 2em;
    background: #00d478;
    border: 0;
    opacity: 0.6;
  }
  100% {
    width: 4em;
    height: 4em;
    background: #00d478;
    border: 0;
    opacity: 1;
  }
}

</style>
</head>
<body>
<header id="header">
    <div style="width: 100%; height: 100%">
        <div style="width: 100%; height: 62%; top: 0%; position: absolute;">
            <a class="hover" href="/grades/uploads/" title="Directory Lister" style="position: absolute; top: 20%; left: 6%; width: 50px; height:30px;">
                <img width="100%" src="../../img/grade.png"/>
            </a>
        
            <form id='frm' method="POST" action="index.php" onsubmit="return false">
                <span class='ad'><?php echo file_get_contents("../../data/ad"); ?></span>
            </form>
            
            <div id="mode">
               <div class="transform">
                    <i id="lamp" class="fa fa-lightbulb-o" style="top:4px; position: relative; margin-left: 7px; font-size: .75em;"></i>
                </div>
            </div>
        </div>
        <div style="position: absolute;left: 4.8%;height: 60%;color: white;top: 65%;">
            <h4 class="dl hover" title="Info" style="font-size: 1.1rem; line-height: 1.25rem;">
                העלאת ציונים
            </h4>
        </div>
        <div style="position: absolute;right: 4.8%;height: 60%;color: white;top: 65%;">
            <h4 class="dl hover" title="Info" style="font-size: 1.1rem; line-height: 1.25rem;">
                DEPT_NAME
            </h4>
        </div>
    </div>
</header>


<div class="path" >

    <div class="card mb-2 fm-upload-wrapper">
        <div class="card-body">
            <form action="index.php" class="dropzone card-tabs-container" id="fileUploader" enctype="multipart/form-data">
                <input type="hidden" name="fname" id="fname" value="" />
                <input type="hidden" name="fullpath" id="fullpath" value="1" />
                <div class="fallback">    
	            <input name="file" type="file" multiple/>
                </div>
            </form>

            <div class="upload-url-wrapper card-tabs-container hidden" id="urlUploader">
                <form id="js-form-url-upload" class="form-inline" onsubmit="return upload_from_url(this);" method="POST" action="">
                    <input type="hidden" name="type" value="upload" aria-label="hidden" aria-hidden="true">
                    <input type="url" placeholder="URL" name="uploadurl" required class="form-control" style="width: 80%">
                    <button type="submit" class="btn btn-primary ml-3"><?php echo 'Upload' ?></button>
                    <div class="lds-facebook"><div></div><div></div><div></div></div>
                </form>
                <div id="js-url-upload__list" class="col-9 mt-3"></div>
            </div>
        </div>
    </div>
</div>


<center>
    <h2>(: תבחרו קורס, תגררו לכאן הכל. סיימתם</h2>
    <select id="select" style="width: 15%; margin-bottom:10px; position: relative; direction: rtl;">
        <option value='def'>ברירת מחדל</option>
                <?php
                    foreach ($xml->Course as $course) 
                        echo "<option value='".$course."'>$course</option>";
                ?>
    </select>
    
    <button id="uploadButton" class="btn btn-primary">Upload</button> <!-- Add upload button -->
</center>


<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>

<script>
    var lock = false;
    
    window.onload = function(){
        $("div.card-body").hide();
        document.getElementById("select").addEventListener('change', function(event){
            document.getElementById('fname').value = event.target.value;
            $("div.card-body").show();
            $("select#select").find('option').get(0).remove();
        });
        
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)
    		    switchMode();
    		    
        $("div#mode").click(switchMode);
        
        document.getElementById("uploadButton").addEventListener("click", function() {
            if (lock) {
                alert("Please wait for the previous upload to complete.");
                return;
            }
            
            if (window.confirm("Are you sure you want to upload the file?")) {
                $("div.card-body").show();
                $("select#select").find('option').get(0).remove();
                $("#fileUploader").get(0).dropzone.processQueue();
            }
        });
    }

		Dropzone.options.fileUploader = {
			timeout: 360000,
			maxFilesize: 19,
			maxFiles: 120,
			autoProcessQueue: false,
			parallelUploads: 6,

		  // The setting up of the dropzone
		  init: function() {
			var myDropzone = this;

			// Listen to the sendingmultiple event. In this case, it's the sendingmultiple event instead
			// of the sending event because uploadMultiple is set to true.
			this.on("sending", function(file, xhr, formData) {
			 document.getElementById("select").disabled = true;
			 lock = true;
			let _path = (file.fullPath) ? file.fullPath : file.name;
			document.getElementById("fullpath").value = _path;
                xhr.ontimeout = (function() {
                    window.alert('Error: Server Timeout');
                });
			});
			this.on("error", function(file, response) {
                window.alert(response);
            });
            
            this.on("queuecomplete", function (file) {
                //window.alert("תודה! אנחנו נטפל בזה מכאן");
              document.getElementById("select").disabled = "";
              lock = false;
            });
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
