<?php

namespace Mentosmenno2\SFTPDeploy\Utils;

class Output
{
	public function printError(string $message): void
	{
		$this->printLine('##########################################');
		$this->printLine('ERROR: ' . $message);
		$this->printLine('##########################################');
	}

	public function printWarning(string $message): void
	{
		$this->printLine('##########################################');
		$this->printLine('WARNING: ' . $message);
		$this->printLine('##########################################');
	}

	public function printNotification(string $message): void
	{
		$this->printLine('##########################################');
		$this->printLine($message);
		$this->printLine('##########################################');
	}

	public function printLine(string $message): void
	{
		$this->print($message . PHP_EOL);
	}

	public function print(string $message): void
	{
		echo $message;
	}
}
