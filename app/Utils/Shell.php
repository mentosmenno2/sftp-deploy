<?php

namespace Mentosmenno2\SFTPDeploy\Utils;

use Mentosmenno2\SFTPDeploy\Utils\Output as OutputUtil;

class Shell
{
	public const EXIT_CODE_SUCCESS = 0;
	public const EXIT_CODE_WARNING = 1;
	public const EXIT_CODE_ERROR = 2;

	public function runCommands(array $commands): int
	{
		$commandsString = implode('&&', $commands);
		$output = $this->runCommand($commandsString);
		return $output;
	}

	/**
	 * @see https://stackoverflow.com/a/22790364
	 */
	public function runCommand(string $command): int
	{
		$outputUtil = new OutputUtil();

		$proc = proc_open($command, [['pipe','r'],['pipe','w'],['pipe','w']], $pipes);
		if (! $proc) {
			return self::EXIT_CODE_ERROR;
		}
		while (($line = fgets($pipes[1])) !== false) {
			$outputUtil->printLine($line);
		}
		while (($line = fgets($pipes[2])) !== false) {
			$outputUtil->printLine($line);
		}
		fclose($pipes[0]);
		fclose($pipes[1]);
		fclose($pipes[2]);
		return proc_close($proc);
	}
}
