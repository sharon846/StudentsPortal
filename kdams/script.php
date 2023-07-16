<?php

if (!isset($_GET['xml']))
    exit();

$xml = simplexml_load_file("../data/kdams");
$xml = json_encode($xml);
$xml = json_decode($xml,TRUE)["Course"];
$xml = array_map(function($cc) { $tmp = $cc["@attributes"]; @$tmp["Dependency"] = $cc["Dependency"]; return $tmp; }, $xml);

$keys = array_map(function($cc) { return $cc["link"]; }, $xml);
$values = array_map(function($cc) { return $cc["name"]; }, $xml);

$name_to_link = array_combine($keys, $values);
$str = "INSERT INTO `Tkdams`(`link`, `name`, `lecture`, `ids`, `pts`, `year`, `note`, `kdams`) VALUES ";

foreach ($xml as $course){
    $str .= "('".$course["link"]."', '".$course["name"]."', '".$course["lecture"]."', '".$course["ids"]."', ".$course["pts"]. ", '".$course["year"]."', '".$course["note"]."', ";

    if ($course["Dependency"] == NULL) $str .= "NULL),";
    else
    {
        $pt = is_array($course["Dependency"]) ? $course["Dependency"] : array($course["Dependency"]);


        $str .= "'" . implode(',', $pt) . "'";

        $str .= "),";
    }
}
$str = substr($str, 0, -1);

echo $str;

?>