<?php

function cli_clear_screen():void {
	echo "\e[H\e[J";
}

function cli_read_line(string $prompt = "" , string &$line , string &$errormsg):bool {
	$error = TRUE;
	$line_raw = "";
	$line = "";

	echo $prompt;
	$stdin = fopen("php://stdin" , "r");
	if (!$stdin) {
		$errormsg = "failed to read input";
		goto end;
	}
	$line_raw = fgets($stdin);
	if ($line_raw == FALSE && $line_raw !== "") {
		$errormsg = "failed to read input";
		goto end;
	}
	$line = trim($line_raw , "\r\n");

	$error = FALSE;
	
	end:
		sodium_memzero($line_raw);

		return !$error;
}

function cli_read_line_obscured(string $prompt = "" , string &$password , string &$errormsg):bool {
	global $STATE;
	$error = TRUE;
	$line = "";
	$line_trimmed = "";

	echo $prompt;
	$stdin = fopen("php://stdin" , "r");
	if (!$stdin) {
		$errormsg = "failed to read input";
		goto end;
	}
	if (PHP_OS === "Linux") {
		if (!cli_linux_disable_echo()) {
			$errormsg = "failed to read input";
			goto end;
		}
	} else {
		$STATE->console->_win_obscureprompt_status = TRUE;
		echo "\033[30;40m";
	}
	$line = fgets($stdin);
	if ($line == FALSE && $line !== "") {
		$line = "";
		$errormsg = "failed to read input";
		goto end;
	}
	$line_trimmed = trim($line , "\r\n");
	if (PHP_OS === "Linux") {
		if (!cli_linux_enable_echo()) {
			$errormsg = "failed to read input";
			goto end;
		}
	} else {
		echo "\033[0m";
		cli_clear_screen();
		$STATE->console->_win_obscureprompt_status = FALSE;
	}
	$password = $line_trimmed;

	$error = FALSE;
	
	end:
		sodium_memzero($line);
		sodium_memzero($line_trimmed);

		return !$error;
}

function cli_linux_disable_echo():bool {
	exec("stty -echo" , $output , $exitcode);
	if ($exitcode !== 0) {
		return FALSE;
	}
	return TRUE;
}

function cli_linux_enable_echo():bool {
	exec("stty echo" , $output , $exitcode);
	if ($exitcode !== 0) {
		return FALSE;
	}
	return TRUE;
}

function cli_prompt(string $prompt = "" , string &$line , string &$errormsg , bool $allow_empty = FALSE): bool {
	$line = "";
	if (cli_read_line($prompt , $line , $errormsg) !== TRUE) {
		return FALSE;
	}
	if ($line == "" && $allow_empty !== TRUE) {
		$errormsg = "value cannot be empty";
		return FALSE;
	}
	return TRUE;
}

function cli_prompt_verify(string $prompt = "" ,  string $verify_prompt = "" , string &$line , string &$errormsg , bool $allow_empty = FALSE): bool {
	$error = TRUE;
	$line_1 = "";
	$line_2 = "";

	if (cli_prompt($prompt , $line_1 , $errormsg , $allow_empty) !== TRUE) {
		goto end;
	}
	if (cli_prompt($verify_prompt , $line_2 , $errormsg , $allow_empty) !== TRUE) {
		goto end;
	}
	/* compare values */
	if (hash_equals($line_1 , $line_2) !== TRUE) {
		$errormsg = "values don't match";
		goto end;
	}
	$line = $line_1;

	$error = FALSE;

	end:
		sodium_memzero($line_1);
		sodium_memzero($line_2);

		return !$error;
}

function cli_prompt_password(string $prompt = "" , string &$password , string &$errormsg):bool {
	if (cli_read_line_obscured($prompt , $password , $errormsg) !== TRUE) {
		return FALSE;
	}
	if ($password == "") {
		$errormsg = "password cannot be empty";
		return FALSE;
	}
	return TRUE;
}

function cli_prompt_password_verify(string $prompt = "" , string $verify_prompt = "" , string &$password , string &$errormsg):bool {
	$error = TRUE;
	$password_1 = "";
	$password_2 = "";

	if (cli_prompt_password($prompt , $password_1 , $errormsg) !== TRUE) {
		goto end;
	}
	if (cli_prompt_password($verify_prompt , $password_2 , $errormsg) !== TRUE) {
		goto end;
	}
	/* compare passwords */
	if (hash_equals($password_1 , $password_2) !== TRUE) {
		$errormsg = "passwords don't match";
		goto end;
	}
	$password = $password_1;

	$error = FALSE;

	end:
		sodium_memzero($password_1);
		sodium_memzero($password_2);

		return !$error;
}

?>