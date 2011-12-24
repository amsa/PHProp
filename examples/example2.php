<?php
require "../PHProp.php";

$ini = PHProp::parse("sample2.ini");	//sample3.ini contents is same as sample2.ini, but sample2.ini is organized in sections
var_dump($ini->application); 		//same as $ini['application']

echo str_repeat("-", 150)."<br />";

echo "DB configurations:";
foreach($ini['application']['config']['db'] as $k=>$v){	//with array access notation
	var_dump($k." : ". $v);
}
echo str_repeat("-", 150)."<br />";

echo "Application childs: ".count($ini->application);
