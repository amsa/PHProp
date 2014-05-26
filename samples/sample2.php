<?php
require "../PHProp.php";

$ini = PHProp::parse("sample2.ini");
var_dump($ini->user1->read);
