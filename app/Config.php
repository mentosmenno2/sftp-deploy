<?php

namespace Mentosmenno2\SFTPDeploy;

use Mentosmenno2\SFTPDeploy\Utils\Output as OutputUtil;
use Mentosmenno2\SFTPDeploy\Utils\Path as PathUtil;
use DateTime;

class Config
{

	public const FILENAMME = 'sftp-deploy.config.json';

	private $cliArgs = [];
	private $basePath;
	private $buildPath;
	private $deployPath;
	private $data;

	public function __construct(array $cliArgs = [], $dieOnUnknownArg = true)
	{
		if (empty($cliArgs)) {
			$cliArgs = $_SERVER['argv'];
			array_shift($cliArgs); // Remove filename from cliargs
		}
		$this->cliArgs = $cliArgs;

		$this->basePath = getcwd();
	}

	public function getCliArgs(): array
	{
		return $this->cliArgs;
	}

	public function getCliArg(int $arg): ?string
	{
		return isset($this->cliArgs[$arg]) ? $this->cliArgs[$arg] : null;
	}

	public function getOperands(): array
	{
		$operands = array();
		$cliArgs = $this->getCliArgs();
		foreach ($cliArgs as $cliArg) {
			if (strpos($cliArg, '=') === false) {
				continue;
			}
			$parts = explode('=', $cliArg, 2); // Only alow 2 items in array
			$key = ltrim($parts[0], '-'); // strip left dashes of key
			$operands[$key] = trim($parts[1], '"\'');
		}
		return $operands;
	}

	public function getOperand(string $operand): ?string
	{
		$operands = $this->getOperands();
		if (array_key_exists($operand, $operands)) {
			return $operands[$operand];
		}
		return null;
	}

	private function getDefault(): array
	{
		return [
			'builds_directory' => 'deployments',
			'builds_use_subdirectory' => true,
			'builds_keep_revisions' => 5,
			'run_before' => [],
			'repo_url' => null,
			'repo_clone_directory' => '.',
			'repo_checkout' => 'master',
			'run_after' => [
				'composer install --no-dev',
				'composer dump-autoload -o'
			],
			'deploy_directory' => '.',
			'sftp_adapter' => 'sftp',
			'sftp_host' => 'example.com',
			'sftp_port' => 22,
			'sftp_username' => 'username',
			'sftp_password' => 'password',
			'sftp_root' => '/path/to/root',
			'sftp_passive' => true,
			'sftp_ssl' => true,
			'sftp_ignore_passive_address' => false,
			'sftp_private_key_file' => null,
			'sftp_private_key_password' => null,
			'sftp_directory_permission' => 0755,
		];
	}

	public function fileExists(): bool
	{
		return file_exists($this->getFileName());
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
		if (! $this->data) {
			$default = $this->getDefault();
			$fromFile = $this->getFromFile();
			$combined = array_merge($default, $fromFile);
			$this->data = $combined;
		}

		return $this->data;
	}

	public function getItem(string $item)
	{
		$data = $this->get();
		if (! array_key_exists($item, $data)) {
			return null;
		}
		return $data[$item];
	}

	private function getFileName(): string
	{
		$pathUtil = new PathUtil();

		$baseFileName = Config::FILENAMME;
		$fileNameFromOperand = $this->getOperand('config');
		if ($fileNameFromOperand) {
			$baseFileName = $fileNameFromOperand;
		}

		$fileName = $pathUtil->trailingSlash($this->getBasePath()) . $baseFileName;
		return $fileName;
	}

	public function getBasePath(): string
	{
		return $this->basePath;
	}

	public function getBaseBuildPath(): string
	{
		$pathUtil = new PathUtil();
		$buildPath = '';
		if (! $pathUtil->isRootPath($this->getItem('builds_directory'))) {
			$buildPath .= $pathUtil->trailingSlash($this->getBasePath());
		}
		$buildPath .= $pathUtil->trailingSlash($this->getItem('builds_directory'));
		return $buildPath;
	}

	public function getBuildPath(): string
	{
		if (! $this->buildPath) {
			$pathUtil = new PathUtil();
			$buildPath = $this->getBaseBuildPath();

			if ($this->getItem('builds_use_subdirectory')) {
				$subDirName = ( new DateTime() )->format('YmdHis');
				$buildPath .= $pathUtil->trailingSlash($subDirName);
			}
			$this->buildPath = $buildPath;
		}
		return $this->buildPath;
	}

	public function getDeployPath()
	{
		if (! $this->deployPath) {
			$pathUtil = new PathUtil();
			$deployPath = '';
			if (! $pathUtil->isRootPath($this->getItem('deploy_directory'))) {
				$deployPath .= $pathUtil->trailingSlash($this->getBuildPath());
			}
			$deployPath .= $pathUtil->trailingSlash($this->getItem('deploy_directory'));
			$this->deployPath = $deployPath;
		}
		return $this->deployPath;
		$this->config->getItem('deploy_directory');
	}
}
