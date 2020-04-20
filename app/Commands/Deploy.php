<?php

namespace Mentosmenno2\SFTPDeploy\Commands;

use Exception;
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

		// Run cleanup
		$cleanupCommand = new Cleanup($this->config);
		$response = $cleanupCommand->run();
		if ($response->hasErrors()) {
			return $response;
		}

		$outputUtil->printNotification('Project successfully deployed.');
		return $response;
	}

	private function deployProject(): bool
	{
		$outputUtil = new OutputUtil();
		$pathUtil = new PathUtil();
		$sftp = $this->getSftpFileSystem();
		if (! $sftp) {
			$outputUtil->printLine('No (s)ftp file system.');
			return false;
		}

		$deployPath = $this->config->getDeployPath();
		$deployPath = $pathUtil->realPath($deployPath);
		$deployPath = $pathUtil->trailingSlash($deployPath);
		$deployPathContents = $pathUtil->getRecursiveContentPaths($deployPath);

		// Extract files
		$filesToUpload = array_filter($deployPathContents, function ($dirOrFilePath) {
			return is_file($dirOrFilePath);
		});

		$outputUtil->printLine('Start uploading files to (s)ftp.');
		foreach ($filesToUpload as $index => $filePath) {
			$filePath = $pathUtil->realPath($filePath);
			$relative = $pathUtil->getRelative($filePath, $deployPath);
			$sftpPath = $pathUtil->toLinuxPath($relative);

			$outputUtil->printLine('[' . ($index + 1) . ' / ' . count($deployPathContents) . '] ' . $relative);

			$resource = fopen($filePath, 'r');
			$response = false;
			try {
				$response = $sftp->putStream($sftpPath, $resource);
			} catch (Exception $e) {
				$outputUtil->printLine($e->getMessage());
			}
			if (! $response) {
				$outputUtil->printLine('Failed to upload ' . $filePath);
				return false;
			}
		}

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

	private function getSftpFileSystem(): ?Filesystem
	{
		if (! $this->sftpFileSystem) {
			$adapter = $this->config->getItem('sftp_adapter');
			$sftpUtil = new SftpUtil($adapter);
			$this->sftpFileSystem = $sftpUtil->getFileSystem();
		}
		return $this->sftpFileSystem;
	}
}
