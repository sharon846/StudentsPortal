<?php

include 'Smalot/vendor/autoload.php'; 

function detect($PDFfile)
{
    //return array: code, degree, year

    $parser = new \Smalot\PdfParser\Parser(); 
    $PDF = $parser->parseFile($PDFfile);
    $PDFContent = $PDF->getText();
    $PDFContent = nl2br($PDFContent);

    $year = "";
    $degree = "0";
    $id = "";
    
    //try detect Department
    $pos = strpos($PDFContent, "Department Name (should be on the pdf)");
    if ($pos === false)
       return -1;
    

    $pos = strpos($PDFContent, "Replace this with keyword that matches only your students");
    if ($pos === false)
    {
        if ($pos === false)
            return -1;
    }
    
    //try extract degree (bsc, msc)
    $pos = strpos($PDFContent, "M.Sc");
    if ($pos !== false){
        $degree = "2";
        $PDFContent = substr($PDFContent, $pos);        //if someone uploaded complex file with more than one degree, trim the prev data
    }
    $pos = strpos($PDFContent, "MSc");
    if ($pos !== false){
        $degree = "2";
        $PDFContent = substr($PDFContent, $pos);        //if someone uploaded complex file with more than one degree, trim the prev data
    }
    
    //only if not MSC
    if ($degree == "0")
    {
        $pos = strpos($PDFContent, "B.Sc");
        if ($pos !== false)
            $degree = "1";
            
        $pos = strpos($PDFContent, "BSc");
        if ($pos !== false)
            $degree = "1";
    }

    //try extract year
    $pos = strpos($PDFContent, "year:");
    if ($pos !== false)
        $year = substr($PDFContent, $pos + 5, 1);
    else{
        $year = -1;
    }
    
    //someone who only got accepted
    $pos = strpos($PDFContent, "changePriority");
    if ($pos !== false)
        $year = 0;
    
    
    return array($degree, $year);
}
?>