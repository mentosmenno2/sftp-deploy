<?php

namespace Mentosmenno2\SFTPDeploy;

use Mentosmenno2\SFTPDeploy\Utils\Output as OutputUtil;
use Mentosmenno2\SFTPDeploy\Utils\Path as PathUtil;
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

	public function getDefault(): array
	{
		return [
			'directory_name' => 'deployments',
			'run_before' => [],
			'repo_clone_url' => 'https://github.com/mentosmenno2/sftp-deploy.git',
			'run_after' => [],
		];
	}

	public function generate(): bool
	{
		$pathUtil = new PathUtil();
		$outputUtil = new OutputUtil();

		$json = json_encode($this->getDefault(), JSON_PRETTY_PRINT);
		$fileName = $pathUtil->trailingSlashSystemPath($this->basePath) . Config::FILENAMME;

		if (file_exists($fileName)) {
			$outputUtil->printLine('Config file already exists.');
			return false;
		}

		$bytes = file_put_contents($fileName, $json);
		if (! $bytes) {
			$outputUtil->printLine('Could not write to config file.');
			return false;
		}
		return true;
	}

	public function get() {
		$default = $this->getDefault();
	}
}
