<?php

namespace Mentosmenno2\SFTPDeploy\Commands;

use DateTime;
use Mentosmenno2\SFTPDeploy\Models\CommandResponse;
use Mentosmenno2\SFTPDeploy\Utils\Output as OutputUtil;
use Mentosmenno2\SFTPDeploy\Utils\Path as PathUtil;
use Mentosmenno2\SFTPDeploy\Utils\Shell as ShellUtil;

class Build extends BaseCommand
{
	private $buildPath;

	public function run(): CommandResponse
	{
		// Run parent
		$outputUtil = new OutputUtil();
		$response = parent::run();
		if ($response->hasErrors()) {
			return $response;
		}

		// Create build path
		$outputUtil->printLine('Creating build path.');
		$buildPathCreated = $this->createBuildPath();
		if (! $buildPathCreated) {
			$response->addError('Could not create build path.');
			return $response;
		}

		// Build project
		$outputUtil->printLine('Building project.');
		$buildCreated = $this->buildProject();
		if (! $buildCreated) {
			$response->addError('Building project failed.');
			return $response;
		}

		$outputUtil->printNotification('Project built successfully.');
		return $response;
	}

	protected function checkRequirements(): bool
	{
		$success = parent::checkRequirements();
		$outputUtil = new OutputUtil();

		if (! $this->config->fileExists()) {
			$outputUtil->printLine('Config file does not exist.');
			$success = false;
		}
		return $success;
	}

	private function createBuildPath(): bool
	{
		$outputUtil = new OutputUtil();
		$pathUtil = new PathUtil();

		$path = $pathUtil->trailingSlash($this->getBuildPath());
		if (is_dir($path)) {
			$outputUtil->printLine('Path exists.');
			return true;
		}

		$created = mkdir($path, 0777, true);
		if (!$created) {
			$outputUtil->printLine('Could not create path.');
			return false;
		}
		return true;
	}

	private function buildProject(): bool
	{
		$outputUtil = new OutputUtil();

		$outputUtil->printLine('Running before commands.');
		$before = $this->runBeforeCommands();
		if (!$before) {
			return false;
		}

		$outputUtil->printLine('Cloning repository.');
		$clone = $this->cloneRepository();
		if (!$clone) {
			return false;
		}

		$outputUtil->printLine('Running after commands.');
		$after = $this->runAfterCommands();
		if (!$after) {
			return false;
		}

		return true;
	}

	private function cloneRepository(): bool
	{
		$shellUtil = new ShellUtil();
		$pathUtil = new PathUtil();
		$outputUtil = new OutputUtil();

		$repoUrl = $this->config->getItem('repo_url');
		$repoDirectory = $pathUtil->trailingSlash($this->getBuildPath());
		$repoDirectory .= $pathUtil->trailingSlash($this->config->getItem('repo_clone_directory'));
		$repoCheckout = $this->config->getItem('repo_checkout');

		// Create repo directory
		if (! is_dir($repoDirectory)) {
			$created = mkdir($repoDirectory, 0777, true);
			if (!$created) {
				$outputUtil->printLine('Could not create path.');
				return false;
			}
		}

		// Clone repo
		$commands = [
			'cd ' . $pathUtil->trailingSlash($repoDirectory),
			'git clone ' . $repoUrl . ' .',
			'git checkout ' . $repoCheckout,
		];
		$output = $shellUtil->runCommands($commands);

		// Handle output
		if (null === $output) {
			$outputUtil->printLine('Could not clone repository.');
			return false;
		}
		$outputUtil->printLine($output);
		return true;
	}

	private function runBeforeCommands(): bool
	{
		$shellUtil = new ShellUtil();
		$pathUtil = new PathUtil();
		$outputUtil = new OutputUtil();

		$config_commands = $this->config->getItem('run_before');
		if (!$config_commands) {
			return true;
		}

		$commands = [
			'cd ' . $pathUtil->trailingSlash($this->getBuildPath()),
		];
		$commands = array_merge($commands, $config_commands);

		$output = $shellUtil->runCommands($commands);
		if (null === $output) {
			return false;
		}
		$outputUtil->printLine($output);
		return true;
	}

	private function runAfterCommands(): bool
	{
		$shellUtil = new ShellUtil();
		$pathUtil = new PathUtil();
		$outputUtil = new OutputUtil();

		$config_commands = $this->config->getItem('run_after');
		if (!$config_commands) {
			return true;
		}

		$commands = [
			'cd ' . $pathUtil->trailingSlash($this->getBuildPath()),
		];
		$commands = array_merge($commands, $config_commands);

		$output = $shellUtil->runCommands($commands);
		if (null === $output) {
			return false;
		}
		$outputUtil->printLine($output);
		return true;
	}

	private function getBuildPath(): string
	{
		if (! $this->buildPath) {
			$pathUtil = new PathUtil();
			$buildPath = $pathUtil->trailingSlash($this->config->getBasePath());
			$buildPath .= $pathUtil->trailingSlash($this->config->getItem('builds_directory'));

			if ($this->config->getItem('use_build_subdirectory')) {
				$subDirName = ( new DateTime() )->format('YmdHis');
				$buildPath .= $pathUtil->trailingSlash($subDirName);
			}
			$this->buildPath = $buildPath;
		}


		return $this->buildPath;
	}
}
