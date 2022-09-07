<?php

declare(strict_types=1);

function git_pull():bool {
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