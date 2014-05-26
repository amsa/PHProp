<?php
require __DIR__ . '/../vendor/autoload.php';

use \Phprop\Phprop as Phprop;

$ini = Phprop::parse("sample2.ini");
var_dump($ini->user1->read);
