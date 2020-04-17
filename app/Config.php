<?php

namespace Mentosmenno2\SFTPDeploy;

class Config
{

	public const FILENAMME = 'sftp-deploy.config.json';

	protected $cliArgs = [];
	public $basePath;

	public function __construct(array $cliArgs = [], $dieOnUnknownArg = true)
	{
		if (empty($cliArgs)) {
			$cliArgs = $_SERVER['argv'];
			array_shift($cliArgs);
		}
		$this->cliArgs = $cliArgs;

		$this->basePath = getcwd();
	}

	public function getCliArgs(): array
	{
		return $this->cliArgs;
	}

	public function getDefaultConfig(): array
	{
		return [
			'directory_name' => 'deployments',
		];
	}
}
