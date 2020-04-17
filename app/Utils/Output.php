<?php

namespace Mentosmenno2\SFTPDeploy\Utils;

class Output
{
	public function printError(string $message): void
	{
		$this->printLine('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
		$this->printLine($message);
		$this->printLine('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
	}

	public function printWarning(string $message): void
	{
		$this->printLine('!#!#!#!#!#!#!#!#!#!#!#!#!#!#!#!#!#!#!#!#!#');
		$this->printLine($message);
		$this->printLine('!#!#!#!#!#!#!#!#!#!#!#!#!#!#!#!#!#!#!#!#!#');
	}

	public function printNotification(string $message): void
	{
		$this->printLine('##########################################');
		$this->printLine($message);
		$this->printLine('##########################################');
	}

	public function printLine(string $message): void
	{
		echo $message . PHP_EOL;
	}
}
