<?php

if (!isset($_GET['filename']))
    exit();

// Save File
if (isset($_POST['savedata'])) {
	$writedata = $_POST['savedata'];
	if (file_exists($_GET['filename']))
	{
    	$fd = fopen($_GET['filename'], "w");
    	@fwrite($fd, $writedata);
    	fclose($fd);
    	
    	if ($_GET['filename'] == "../site_manager/pdoconfig.php"){
    	    
    	    $a = strpos($writedata, '$admin_password');
    	    if ($a !== false)
    	    {
    	        $d = substr($writedata, $a);
    	        $d = substr($d, 19);
    	        $d = explode("'", $d)[0];
    	        $d = password_hash($d, PASSWORD_DEFAULT);
    	        file_put_contents(".htpasswd", "admin:$d");
    	    }
    	}
	}
}
?>

<html>
   <head>
      <style>
         #editor {
         position: absolute;
         top: 50px;
         right: 0;
         bottom: 0;
         left: 0;
         }
      </style>
      <script src="http://cdnjs.cloudflare.com/ajax/libs/ace/1.4.1/ace.js"></script>
      <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.slim.min.js"></script>
   </head>
   <body>
      <button style="margin-right: 1em;" onclick="save()">Save</button><h2 style="display: contents;">Filename: <?php echo pathinfo($_GET['filename'])['filename']; ?></h2>
      <div id="editor"><?php echo htmlspecialchars(file_get_contents($_GET['filename']));?></div>
      <script>
         var editor = ace.edit("editor");
         editor.setTheme("ace/theme/monokai");
         editor.getSession().setMode("ace/mode/text");
         editor.setFontSize(14);
         function ace_commend (cmd) { editor.commands.exec(cmd, editor); }
         
         editor.commands.addCommands([{
         name: 'save', bindKey: {win: 'Ctrl-S',  mac: 'Command-S'},
         exec: function(editor) { edit_save(this, 'ace'); }
         }]);
         
         $(".js-ace-toolbar").on("click", 'button', function(e){
         e.preventDefault();
         let cmdValue = $(this).attr("data-cmd"), editorOption = $(this).attr("data-option");
         if(cmdValue && cmdValue != "none") 
         ace_commend(cmdValue);
         
         else if(editorOption) {
         if(editorOption == "fullscreen") {
         
         (void 0!==document.fullScreenElement&&null===document.fullScreenElement||void 0!==document.msFullscreenElement&&null===document.msFullscreenElement||void 0!==document.mozFullScreen&&!document.mozFullScreen||void 0!==document.webkitIsFullScreen&&!document.webkitIsFullScreen)
         	&&(editor.container.requestFullScreen?editor.container.requestFullScreen():editor.container.mozRequestFullScreen?editor.container.mozRequestFullScreen():editor.container.webkitRequestFullScreen?editor.container.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT):editor.container.msRequestFullscreen&&editor.container.msRequestFullscreen());
         
         } else if(editorOption == "wrap") {
         let wrapStatus = (editor.getSession().getUseWrapMode()) ? false : true;
         editor.getSession().setUseWrapMode(wrapStatus);
         
         } else if(editorOption == "help") {	
         var helpHtml="";$.each(window.config.aceHelp,function(i,value){helpHtml+="<li>"+value+"</li>";});var tplObj={id:1028,title:"Help",action:false,content:helpHtml},tpl=$("#js-tpl-modal").html();$('#wrapper').append(template(tpl,tplObj));$("#js-ModalCenter-1028").modal('show');
         }
         }
         });
         
         function save()
         {
             ace_commend("save");
         }
         
         
         function edit_save(e, t) {
         var n = "ace" == t ? editor.getSession().getValue() : document.getElementById("normal-editor").value;
         if (n) {
             var a = document.createElement("form");
             a.setAttribute("method", "POST"), a.setAttribute("action", "");
             var o1 = document.createElement("textarea");
             o1.setAttribute("type", "textarea"), o1.setAttribute("name", "savedata");
             var o2 = document.createElement("input");
             o2.setAttribute("type", "text"), o2.setAttribute("name", "filename");
             var c1 = document.createTextNode(n);
             var c2 = document.createTextNode(<?php echo "'".$_GET['filename']."'";?>);
             o1.appendChild(c1), a.appendChild(o1), o2.appendChild(c2), a.appendChild(o2), document.body.appendChild(a), a.submit();
         }
         }
      </script>
   </body>
</html>