<?php

function get_server_cpu_usage() {

    $com = new COM('winmgmts:{impersonationLevel=impersonate}');
    $cpus = $com->execquery('SELECT LoadPercentage FROM Win32_Processor');
    foreach ($cpus as $cpu) { $load = $cpu->LoadPercentage; break; }
	
	return $load;

};

?>