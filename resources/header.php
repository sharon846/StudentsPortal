<?php

function show_header(){
    ?>
    <!DOCTYPE html>
    <html dir="rtl">
	<head>
	    <title>SITE_NAME</title>
	    <meta charset="utf-8">
	    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;700;900&amp;display=swap">
		 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	    <link rel="stylesheet" href="/resources/header.css">
	    <link rel="stylesheet" href="/resources/style.css">
	</head>
	
	<body oncontextmenu="return false;" class="bg-secondary text-dark min-h-screen flex flex-col">
		<div class="flex-1 flex flex-col">
			<header>
				<div class="top-and-menu relative">
					<div class="top-bar">
						<div class="bar-block">
							<a href="/" class="h-full inline-flex nuxt-link-active">
								<img src="../img/logoranker.png" alt="לוגו ועד הסטודנטים" class="h-16 w-16 rounded-full bg-white"> 
								<div class="mr-6 flex flex-col justify-center items-center tracking-wider">
									<span>ועד הסטודנטים</span> 
									<span>DEPT_NAME</span>
								</div>
							</a>
						</div>
				    </div>
				</div>
			</header>
			<main class="flex-1 flex flex-col items-center lg:mx-32 xl:mx-64 bg-white px-3 md:px-6 py-2 flex-grow shadow-2xl">
				
    <?php
    
    
    
}



function show_footer(){

?>

        
			</main>
		</div>
		<script>
		    document.onkeydown = function(e) {
                if(event.keyCode == 123) {
                return false;
                }
                if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)){
                return false;
                }
                if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)){
                return false;
                }
                if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)){
                return false;
                }
                }

		</script>
	</body>
    </html>
    

<?php

}

?>
