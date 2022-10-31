<?php

declare(strict_types=1);


const LOCAL_CONFIG_FILE = __DIR__ . "/config.json";

const ERROR = "\033[0;31mERROR:\033[0;0m ";


$STATE = (object) [
	"console" => (object) [
		"_win_obscureprompt_status" => FALSE
	]
];

function shutdown() {
	global $STATE;
	global $password;
	if (isset($password)) {
		sodium_memzero($password);
	}
	if ($STATE->console->_win_obscureprompt_status === TRUE) {
		echo "\033[0m";
		cli_clear_screen();
	};
	exit(0);
}

if (PHP_OS === "Linux") {
	pcntl_signal(SIGINT , "shutdown");
} else {
	sapi_windows_set_ctrl_handler("shutdown");
}


?>