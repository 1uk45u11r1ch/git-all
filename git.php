<?php

declare(strict_types=1);

function git_pull(string &$password):bool {
    $output = [];
    if (PHP_OS === "Linux") {
        $pipes = [];
        $process = proc_open(
            "exec sshpass -v -P \"passphrase\" git pull",
            [
                0 => ["pipe" , "r"], /* STDIN */
                1 => ["pipe" , "w"], /* STDOUT */
                2 => ["pipe" , "w"]  /* STDERR */
            ],
            $pipes
        );
        if (!$process) {
            echo ERROR . "failed to execute git pull\n";
            return FALSE;
        }
        sleep(2);
        /* pass password on stdin */
        if (!fwrite($pipes[0] , $password)) {
            echo ERROR . "failed to pass password to git pull\n";
            exit(1);
        }
        /* wait for child process to exit */
        do {
            $status = proc_get_status($process);
            $out = "out:" . fread($pipes[1] , 1000) . "\n";
            $err = "err:" . fread($pipes[2] , 1000) . "\n";
            echo $out . $err;
        } while ($status["running"] !== FALSE);
        $exitcode = $status["exitcode"];
    } else {
        if (exec("git pull" , $output , $exitcode) === FALSE) {
            echo ERROR . "failed to execute git pull\n";
            return FALSE;
        }
        echo implode("\n" , $output) . "\n";
    }
	if ($exitcode !== 0) {
		echo ERROR . "git pull terminated with exit code " . $exitcode . "\n";
        return FALSE;
	}
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