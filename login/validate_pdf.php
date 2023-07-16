<?php

include 'Smalot/vendor/autoload.php'; 

function extract_year($text, $pos)
{
    $i = 0;
    $chr = substr($text, $pos - 1, 1);

    while ($i < 3 && !is_numeric($chr))
    {
        $pos-=1;
        $chr = substr($text, $pos - 1, 1);
    }
    if (!is_numeric($chr))
        return "0";
    else
        return $chr;
}

function detect($PDFfile)
{
    //return array: code, degree, year, id

    $parser = new \Smalot\PdfParser\Parser(); 
    $PDF = $parser->parseFile($PDFfile);
    $PDFContent = $PDF->getText();
    $PDFContent = nl2br($PDFContent);

    $year = "";
    $degree = "0";
    $id = "";
    
    //try detect computer science
    $pos = strpos($PDFContent, "òãéî úåëøòî");
    if ($pos === false)
    {
        //last try to detect computer science. It will work if someone uploaded מערכת שעות
        //$pos = strpos($PDFContent, "203.");
       // return $pos === false ? -1 : array("", "", "");/
       return -1;
    }
    

    //allow only files from type 'מערכת שיעורים', 'אישור לימודים', 'אישור ציונים',changePriority
    $pos = strpos($PDFContent, "íéøåòù úëøòî");
    if ($pos === false)
    {
        //last try to detect אישור לימודים
        $pos = strpos($PDFContent, "íéãåîéì øåùéà");
        if ($pos === false)
        {
            //last try to detect אישור ציונים
            $pos = strpos($PDFContent, "íéðåéö øåùéà");
            if ($pos === false)
            {
                //last try to detect only accepted
                $pos = strpos($PDFContent, "changePriority");
                if ($pos === false)
                    return -1;
            }
        }
    }
    
    
    //try extract id
    $pattern= '/[0-9]{9}/';
    if (preg_match($pattern, $PDFContent, $matches)){
        $id = $matches[0];
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
    $pos = strpos($PDFContent, "éðù");
    if ($pos !== false)
        $degree = "2";
    
    //only if not MSC
    if ($degree == "0")
    {
        $pos = strpos($PDFContent, "B.Sc");
        if ($pos !== false)
            $degree = "1";
            
        $pos = strpos($PDFContent, "BSc");
        if ($pos !== false)
            $degree = "1";
        
        $pos = strpos($PDFContent, "ïåùàø");
        if ($pos !== false)
            $degree = "1";    
    }

    //try extract year
    $pos = strpos($PDFContent, ":äðù");
    if ($pos !== false)
        $year = extract_year($PDFContent, $pos);
    else{
        $pos = strpos($PDFContent, ":áìù");
        if ($pos !== false){
            $year = extract_year($PDFContent, $pos);
        }
        else
        {
            $pos = strpos($PDFContent, "áìù");
            if ($pos !== false)
                $year = substr($PDFContent, $pos - 6, 1);
        }
    }
    
    //someone who only got accepted
    $pos = strpos($PDFContent, "changePriority");
    if ($pos !== false)
        $year = 0;
    
    
    return array($degree, $year, $id);
}

//the next line describes cs students, first degree
//BSC-áùçîä éòãî
//extract year
//1:äðù
//extrat first degree
//âåçïåùàø :øàåúì
//òãéî מידע úåëøòîì למערכות âåçäïåùàø תואר :øàåúì ראשון 1:äðù שנה
//òãéî úåëøòîì âåçäïåùàø :øàåúì 1:äðù
?>