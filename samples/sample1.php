<?php
require __DIR__ . '/../vendor/autoload.php';

use \Phprop\Phprop as Phprop;

$ini = Phprop::parse("sample1.ini");
var_dump($ini->application); 	//same as $ini['application']

echo str_repeat("-", 150)."<br />";

echo "DB configurations:";
foreach($ini->application->db as $k=>$v){	//with object notation
    var_dump($k." : ". $v);
}

