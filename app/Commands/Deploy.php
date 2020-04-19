<?php

namespace Mentosmenno2\SFTPDeploy\Commands;

use League\Flysystem\Filesystem;
use Mentosmenno2\SFTPDeploy\Models\CommandResponse;
use Mentosmenno2\SFTPDeploy\Utils\Output as OutputUtil;
use Mentosmenno2\SFTPDeploy\Utils\Path as PathUtil;
use Mentosmenno2\SFTPDeploy\Utils\Sftp as SftpUtil;

class Deploy extends BaseCommand
{
	private $sftpFileSystem;

	public function run(): CommandResponse
	{
		// Run parent
		$outputUtil = new OutputUtil();
		$response = parent::run();
		if ($response->hasErrors()) {
			return $response;
		}

		// Run build
		$buildCommand = new Build($this->config);
		$response = $buildCommand->run();
		if ($response->hasErrors()) {
			return $response;
		}

		// Deploy project
		$outputUtil->printLine('Deploying project.');
		$projectDeployed = $this->deployProject();
		if (! $projectDeployed) {
			$response->addError('Deploying project failed.');
			return $response;
		}

		$outputUtil->printNotification('Project successfully deployed.');
		return $response;
	}

	private function deployProject(): bool
	{
		$pathUtil = new PathUtil();
		$deployPath = $this->config->getDeployPath();

		$pathContents = $pathUtil->getContents($deployPath);

		

		return true;
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

	private function getFileSystem(): ?Filesystem
	{
		if (! $this->sftpFileSystem) {
			$adapter = $this->config->getItem('sftp_adapter');
			$sftpUtil = new SftpUtil($adapter);
			$this->sftpFileSystem = $sftpUtil->getFileSystem();
		}
		return $this->sftpFileSystem;
	}
}
