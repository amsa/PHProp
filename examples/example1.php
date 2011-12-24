<?php
require "../PHProp.php";

if (function_exists('xhprof_enable')) {
    xhprof_enable(XHPROF_FLAGS_NO_BUILTINS + XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
}
$ini = PHProp::parse("sample1.ini");
var_dump($ini->application); 	//same as $ini['application']

echo str_repeat("-", 150)."<br />";

echo "DB configurations:";
foreach($ini->application->db as $k=>$v){	//with object notation
	var_dump($k." : ". $v);
}

if (function_exists('xhprof_disable')) {
    $xhprof_data = xhprof_disable();
    include_once $_SERVER['DOCUMENT_ROOT']."/xhprof/xhprof_lib/utils/xhprof_lib.php";
    include_once $_SERVER['DOCUMENT_ROOT']."/xhprof/xhprof_lib/utils/xhprof_runs.php";

    $xhprof_runs = new XHProfRuns_Default();
    $run_id = $xhprof_runs->save_run($xhprof_data, "PHProp");

    echo "---------------\n".
        "XHProf \n".
        "<a href=http://localhost/xhprof/xhprof_html/index.php?run=$run_id&source=PHProp> Profiling Result </a>\n".
        "---------------\n";
}
