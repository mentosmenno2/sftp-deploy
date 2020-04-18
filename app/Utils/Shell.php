<?php

namespace Mentosmenno2\SFTPDeploy\Utils;

class Shell
{
	public function runCommands(array $commands): ?string
	{
		$commandsString = implode('&&', $commands);
		$output = $this->runCommand($commandsString);
		return $output;
	}

	public function runCommand(string $command): ?string
	{
		$output = shell_exec($command);
		return $output;
	}
}
