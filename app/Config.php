<?php

namespace Mentosmenno2\SFTPDeploy;

use Mentosmenno2\SFTPDeploy\Utils\Output as OutputUtil;
use Mentosmenno2\SFTPDeploy\Utils\Path as PathUtil;

class Config
{

	public const FILENAMME = 'sftp-deploy.config.json';

	private $cliArgs = [];
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

	private function getDefault(): array
	{
		return [
			'directory_name' => 'deployments',
			'run_before' => [],
			'repo_clone_url' => 'https://github.com/mentosmenno2/sftp-deploy.git',
			'run_after' => [],
		];
	}

	private function getFromFile(): array
	{
		$fileName = $this->getFileName();
		$jsonString = file_get_contents($fileName);
		if (! $jsonString) {
			return array();
		}
		$jsonArray = json_decode($jsonString, true);
		if (! is_array($jsonArray)) {
			return array();
		}
		return $jsonArray;
	}

	public function generate(): bool
	{
		$outputUtil = new OutputUtil();

		$json = json_encode($this->getDefault(), JSON_PRETTY_PRINT);
		$fileName = $this->getFileName();
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

	public function get(): array
	{
		$default = $this->getDefault();
		$fromFile = $this->getFromFile();
		return array_merge($default, $fromFile);
	}

	private function getFileName(): string {
		$pathUtil = new PathUtil();
		$fileName = $pathUtil->trailingSlashSystemPath($this->basePath) . Config::FILENAMME;
		return $fileName;
	}
}
