<html>
    <head>
        
    </head>
    <body>
        <ul style="right: 15px; position: relative" align="right">
            <?php 
            include 'ds.php';
            
            $allCourses = getAllAsArray();
            foreach ($allCourses as $key=>$kdams)
            {
                $key = trim($key, "''");
                array_shift($kdams);
                
                echo "<li style='direction: rtl'>$key<ul style='direction: rtl'>";
                foreach ($kdams as $kdam)
                    echo "<li>$kdam</li>";
                
                echo "</ul></li><br /><br />";
            }
            
            ?>
        </ul>
    </body>
</html>