<?php

declare(strict_types=1);

require __DIR__ . "/config.php";
require __DIR__ . "/git.php";
require __DIR__ . "/cli.php";

$argv = $_SERVER["argv"];

/* validate local config file */
if (!file_exists(LOCAL_CONFIG_FILE)) {
	if (!file_put_contents(LOCAL_CONFIG_FILE , str_replace("    " , "\t" , json_encode((object) [
		"pull" => []
	] , JSON_PRETTY_PRINT)))) {
		echo ERROR . "failed to create local config file\n";
		exit(1);
	}
} else {
	if (!is_file(LOCAL_CONFIG_FILE) || !is_readable(LOCAL_CONFIG_FILE)) {
		echo ERROR . "cannot read local config file\n";
		exit(1);
	}
}
/* load local config */
$config_json = file_get_contents(LOCAL_CONFIG_FILE);
if ($config_json == FALSE) {
	echo ERROR . "failed to read config file\n";
	exit(1);
}
$CONFIG = json_decode($config_json);
if ($CONFIG === NULL || !is_object($CONFIG)) {
	echo ERROR . "invalid local config file\n";
	exit(1);
}


if (!isset($argv[1])) {
	print_help();
	exit(1);
}

/* actions */
if ($argv[1] === "pull") {
	if (!isset($CONFIG->pull) || count($CONFIG->pull) < 1) {
		echo ERROR . "no paths to pull configured\n";
		exit(1);
	}
	foreach ($CONFIG->pull as $pull) {
		if (
			!is_object($pull) ||
			!isset($pull->path) || !is_string($pull->path) || $pull->path == "" ||
			(isset($pull->branches) && !is_array($pull->branches))
		) {
			echo ERROR . "invalid pull spec\n";
			exit(1);
		}
		if (!file_exists($pull->path) || !is_dir($pull->path)) {
			echo ERROR . "directory not found: " . $pull->path . "\n";
			exit(1);
		}
		if(!chdir($pull->path)) {
			echo ERROR . "failed to cd to " . $Pull->path . "\n";
			exit(1);
		}
		echo "\n" . $pull->path . "\n";
		if (!isset($pull->branches) || count($pull->branches) < 1) {
			/* pull branch that is currently checked out */
			if (!git_pull()) {
				exit(1);
			}
		} else {
			echo "\n";
			foreach ($pull->branches as $branch) {
				if ($branch == "") {
					echo ERROR . "empty branch name\n";
					continue;
				}
				if (!git_checkout($branch)) {
					exit(1);
				}
				echo "\n";
				if (!git_pull()) {
					exit(1);
				}
				echo "\n";
			}
		}
	}
	
} else {
	print_help();
	exit(1);
}



?>