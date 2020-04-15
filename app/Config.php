<?php

namespace Mentosmenno2\SFTPDeploy;

class Config
{

	protected $cliArgs = [];

	public function __construct(array $cliArgs = [], $dieOnUnknownArg = true)
	{
		if (empty($cliArgs)) {
			$cliArgs = $_SERVER['argv'];
			array_shift($cliArgs);
		}
		$this->cliArgs = $cliArgs;
	}

	public function getCliArgs(): array
	{
		return $this->cliArgs;
	}
}
