<?php

declare(strict_types=1);


const LOCAL_CONFIG_FILE = __DIR__ . "/config.json";

const ERROR = "\033[0;31mERROR:\033[0;0m ";


$STATE = (object) [
	"console" => (object) [
		"_win_obscureprompt_status" => FALSE
	]
];

register_shutdown_function(function() {
	global $STATE;
	if ($STATE->console->_win_obscureprompt_status === TRUE) {
		echo "\033[0m";
		cli_clear_screen();
	}
});


?>