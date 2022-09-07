<?php

declare(strict_types=1);

function git_pull(string &$password):bool {
    $pipes = [];
    $process = proc_open(
        "git pull",
        [
            0 => ["pipe" , "r"], /* STDIN */
            1 => ["pipe" , "w"], /* STDOUT */
            2 => ["pipe" , "w"]  /* STDERR */
        ],
        $pipes
    );
    if (!$process) {
        echo "failed to execute git pull\n";
        exit(1);
    }
    sleep(3);
    if (!fwrite($pipes[0] , $password)) {
        echo "failed to pass password to git pull";
        exit(1);
    }
    do {
        $status = proc_get_status($process);
        $out = fread($pipes[1] , 10);
        $err = fread($pipes[2] , 10);
        echo $out . $err;
    } while ($status["running"] !== FALSE);
    if ($status["exitcode"] !== 0) {
        echo ERROR . "git pull terminated with exit code " . $status["exitcode"] . "\n";
        return FALSE;
    }

    return TRUE;

    /*
    $output = [];
	if (exec("git pull" , $output , $exitcode) === FALSE) {
		echo ERROR . "failed to execute git pull\n";
        return FALSE;
	}
	echo implode("\n" , $output) . "\n";
	if ($exitcode !== 0) {
		echo ERROR . "git pull terminated with exit code " . $exitcode . "\n";
        return FALSE;
	}
    */

    return TRUE;
}

function git_checkout(string $branch):bool {
    $output = [];
    if (exec("git checkout " . $branch , $output , $exitcode) === FALSE) {
        echo ERROR . "failed to execute git checkout";
        return FALSE;
    }
    echo implode("\n" , $output) . "\n";
    if ($exitcode !== 0) {
        echo ERROR . "git checkout terminated with exit code " . $exitcode . "\n";
        return FALSE;
    }
    return TRUE;
}

?>